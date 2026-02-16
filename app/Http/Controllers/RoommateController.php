<?php

namespace App\Http\Controllers;

use App\Models\RoommateInteraction;
use App\Models\RoommatePreference;
use App\Models\RoommateProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoommateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'budget' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'gender' => 'nullable|in:any,male,female,other',
            'lifestyle' => 'nullable|string',
            'amenities' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();
        $preference = RoommatePreference::where('user_id', $userId)->first();

        $query = RoommateProfile::with([
            'user:id,first_name,last_name,email,phone_number,profile_image_path,last_seen,is_online',
        ])
            ->where('is_active', true)
            ->where('user_id', '!=', $userId);

        if ($request->filled('location')) {
            $location = trim((string) $request->location);
            $query->where(function ($q) use ($location) {
                $q->where('city', 'like', "%{$location}%")
                    ->orWhere('state', 'like', "%{$location}%")
                    ->orWhere('country', 'like', "%{$location}%");
            });
        }

        if ($request->filled('gender') && $request->gender !== 'any') {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('budget')) {
            $budget = (float) $request->budget;
            $query->where(function ($q) use ($budget) {
                $q->whereNull('budget_min')->orWhere('budget_min', '<=', $budget);
            })->where(function ($q) use ($budget) {
                $q->whereNull('budget_max')->orWhere('budget_max', '>=', $budget);
            });
        }

        $perPage = (int) ($request->per_page ?? 20);
        $profiles = $query->latest()->paginate($perPage);

        $lifestyleFilters = $request->filled('lifestyle')
            ? array_values(array_filter(array_map('trim', explode(',', (string) $request->lifestyle))))
            : [];
        $amenityFilters = $request->filled('amenities')
            ? array_values(array_filter(array_map('trim', explode(',', (string) $request->amenities))))
            : [];

        $profiles->getCollection()->transform(function (RoommateProfile $profile) use ($preference, $lifestyleFilters, $amenityFilters) {
            $matchScore = $this->calculateMatchScore($profile, $preference, $lifestyleFilters, $amenityFilters);
            return [
                'id' => $profile->id,
                'user_id' => $profile->user_id,
                'headline' => $profile->headline,
                'bio' => $profile->bio,
                'gender' => $profile->gender,
                'age' => $profile->age,
                'occupation' => $profile->occupation,
                'budget_min' => $profile->budget_min,
                'budget_max' => $profile->budget_max,
                'preferred_locations' => $profile->preferred_locations ?? [],
                'lifestyle_tags' => $profile->lifestyle_tags ?? [],
                'amenity_preferences' => $profile->amenity_preferences ?? [],
                'city' => $profile->city,
                'state' => $profile->state,
                'country' => $profile->country,
                'move_in_date' => optional($profile->move_in_date)->toDateString(),
                'is_smoker' => $profile->is_smoker,
                'has_pets' => $profile->has_pets,
                'sleep_schedule' => $profile->sleep_schedule,
                'cleanliness_level' => $profile->cleanliness_level,
                'social_level' => $profile->social_level,
                'is_active' => $profile->is_active,
                'user' => $profile->user,
                'match_score' => $matchScore,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $profiles->items(),
            'pagination' => [
                'current_page' => $profiles->currentPage(),
                'last_page' => $profiles->lastPage(),
                'per_page' => $profiles->perPage(),
                'total' => $profiles->total(),
            ],
        ]);
    }

    public function me(): JsonResponse
    {
        $userId = Auth::id();
        $profile = RoommateProfile::with('user:id,first_name,last_name,email,phone_number,profile_image_path')
            ->where('user_id', $userId)
            ->first();
        $preference = RoommatePreference::where('user_id', $userId)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'profile' => $profile,
                'preference' => $preference,
            ],
        ]);
    }

    public function upsertProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:2000',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'age' => 'nullable|integer|min:18|max:99',
            'occupation' => 'nullable|string|max:255',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'preferred_locations' => 'nullable|array',
            'preferred_locations.*' => 'string|max:255',
            'lifestyle_tags' => 'nullable|array',
            'lifestyle_tags.*' => 'string|max:100',
            'amenity_preferences' => 'nullable|array',
            'amenity_preferences.*' => 'string|max:100',
            'city' => 'nullable|string|max:120',
            'state' => 'nullable|string|max:120',
            'country' => 'nullable|string|max:120',
            'move_in_date' => 'nullable|date',
            'is_smoker' => 'nullable|boolean',
            'has_pets' => 'nullable|boolean',
            'sleep_schedule' => 'nullable|string|max:100',
            'cleanliness_level' => 'nullable|integer|min:1|max:5',
            'social_level' => 'nullable|integer|min:1|max:5',
            'is_active' => 'nullable|boolean',

            'gender_preference' => 'nullable|in:any,male,female,other',
            'min_budget' => 'nullable|numeric|min:0',
            'max_budget' => 'nullable|numeric|min:0',
            'preference_locations' => 'nullable|array',
            'preference_locations.*' => 'string|max:255',
            'lifestyle_preferences' => 'nullable|array',
            'lifestyle_preferences.*' => 'string|max:100',
            'preference_amenities' => 'nullable|array',
            'preference_amenities.*' => 'string|max:100',
            'smoking_preference' => 'nullable|in:any,smoker,non_smoker',
            'pet_preference' => 'nullable|in:any,pets_ok,no_pets',
            'preference_sleep_schedule' => 'nullable|string|max:100',
            'move_in_from' => 'nullable|date',
            'move_in_to' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();
        $data = $validator->validated();

        $profileData = [
            'headline' => $data['headline'] ?? null,
            'bio' => $data['bio'] ?? null,
            'gender' => $data['gender'] ?? null,
            'age' => $data['age'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'budget_min' => $data['budget_min'] ?? null,
            'budget_max' => $data['budget_max'] ?? null,
            'preferred_locations' => $data['preferred_locations'] ?? [],
            'lifestyle_tags' => $data['lifestyle_tags'] ?? [],
            'amenity_preferences' => $data['amenity_preferences'] ?? [],
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? null,
            'move_in_date' => $data['move_in_date'] ?? null,
            'is_smoker' => $data['is_smoker'] ?? null,
            'has_pets' => $data['has_pets'] ?? null,
            'sleep_schedule' => $data['sleep_schedule'] ?? null,
            'cleanliness_level' => $data['cleanliness_level'] ?? null,
            'social_level' => $data['social_level'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ];

        $profile = RoommateProfile::updateOrCreate(
            ['user_id' => $userId],
            $profileData
        );

        $preferenceData = [
            'gender_preference' => $data['gender_preference'] ?? 'any',
            'min_budget' => $data['min_budget'] ?? null,
            'max_budget' => $data['max_budget'] ?? null,
            'preferred_locations' => $data['preference_locations'] ?? [],
            'lifestyle_preferences' => $data['lifestyle_preferences'] ?? [],
            'amenity_preferences' => $data['preference_amenities'] ?? [],
            'smoking_preference' => $data['smoking_preference'] ?? 'any',
            'pet_preference' => $data['pet_preference'] ?? 'any',
            'sleep_schedule' => $data['preference_sleep_schedule'] ?? null,
            'move_in_from' => $data['move_in_from'] ?? null,
            'move_in_to' => $data['move_in_to'] ?? null,
        ];

        $hasPreferenceInput = isset($data['gender_preference'])
            || isset($data['min_budget'])
            || isset($data['max_budget'])
            || isset($data['preference_locations'])
            || isset($data['lifestyle_preferences'])
            || isset($data['preference_amenities'])
            || isset($data['smoking_preference'])
            || isset($data['pet_preference'])
            || isset($data['preference_sleep_schedule'])
            || isset($data['move_in_from'])
            || isset($data['move_in_to']);

        $preference = null;
        if ($hasPreferenceInput) {
            $preference = RoommatePreference::updateOrCreate(
                ['user_id' => $userId],
                $preferenceData
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Roommate profile saved successfully',
            'data' => [
                'profile' => $profile,
                'preference' => $preference,
            ],
        ]);
    }

    public function interact(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'target_user_id' => 'required|exists:users,id',
            'type' => 'required|in:like,pass,super_like',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();
        $targetUserId = (int) $request->target_user_id;
        $type = (string) $request->type;

        if ($userId === $targetUserId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot interact with yourself',
            ], 400);
        }

        $targetHasProfile = RoommateProfile::where('user_id', $targetUserId)
            ->where('is_active', true)
            ->exists();

        if (!$targetHasProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Target user does not have an active roommate profile',
            ], 404);
        }

        $existing = RoommateInteraction::where('user_id', $userId)
            ->where('target_user_id', $targetUserId)
            ->where('type', $type)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' removed',
                'data' => [
                    'removed' => true,
                    'matched' => false,
                ],
            ]);
        }

        $interaction = RoommateInteraction::create([
            'user_id' => $userId,
            'target_user_id' => $targetUserId,
            'type' => $type,
        ]);

        $matched = false;
        if (in_array($type, ['like', 'super_like'], true)) {
            $reciprocal = RoommateInteraction::where('user_id', $targetUserId)
                ->where('target_user_id', $userId)
                ->whereIn('type', ['like', 'super_like'])
                ->latest()
                ->first();

            if ($reciprocal) {
                $matched = true;
                $now = now();
                $interaction->matched_at = $now;
                $interaction->save();
                $reciprocal->matched_at = $now;
                $reciprocal->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' saved',
            'data' => [
                'id' => $interaction->id,
                'type' => $interaction->type,
                'matched' => $matched,
                'matched_at' => optional($interaction->matched_at)->toISOString(),
            ],
        ], 201);
    }

    public function matches(): JsonResponse
    {
        $userId = Auth::id();

        $matches = RoommateInteraction::with([
            'targetUser:id,first_name,last_name,email,phone_number,profile_image_path,last_seen,is_online',
            'targetUser.roommateProfile',
        ])
            ->where('user_id', $userId)
            ->whereIn('type', ['like', 'super_like'])
            ->whereNotNull('matched_at')
            ->orderBy('matched_at', 'desc')
            ->get();

        $data = $matches->map(function (RoommateInteraction $match) {
            $profile = optional($match->targetUser)->roommateProfile;
            return [
                'interaction_id' => $match->id,
                'matched_at' => optional($match->matched_at)->toISOString(),
                'target_user' => $match->targetUser,
                'target_profile' => $profile,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function calculateMatchScore(
        RoommateProfile $profile,
        ?RoommatePreference $preference,
        array $lifestyleFilters,
        array $amenityFilters
    ): int {
        $score = 40;

        if ($preference) {
            if ($preference->gender_preference !== 'any' && $profile->gender === $preference->gender_preference) {
                $score += 15;
            }

            if ($preference->min_budget !== null && $profile->budget_max !== null && $profile->budget_max >= $preference->min_budget) {
                $score += 10;
            }

            if ($preference->max_budget !== null && $profile->budget_min !== null && $profile->budget_min <= $preference->max_budget) {
                $score += 10;
            }

            $preferredLocations = $preference->preferred_locations ?? [];
            if (!empty($preferredLocations)) {
                $locationHit = in_array($profile->city, $preferredLocations, true)
                    || in_array($profile->state, $preferredLocations, true)
                    || in_array($profile->country, $preferredLocations, true);
                if ($locationHit) {
                    $score += 10;
                }
            }
        }

        if (!empty($lifestyleFilters)) {
            $profileTags = $profile->lifestyle_tags ?? [];
            $overlap = count(array_intersect($lifestyleFilters, $profileTags));
            $score += min($overlap * 4, 12);
        }

        if (!empty($amenityFilters)) {
            $profileAmenities = $profile->amenity_preferences ?? [];
            $overlap = count(array_intersect($amenityFilters, $profileAmenities));
            $score += min($overlap * 3, 9);
        }

        return max(0, min($score, 100));
    }
}

