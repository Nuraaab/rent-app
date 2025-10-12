<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Http\Resources\RentalResource;
use App\Http\Resources\JobPositionResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    /**
     * Toggle favorite status (add/remove) for rental or job
     * Modern approach: single endpoint for toggle
     */
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'rental_id' => 'nullable|exists:rentals,id',
            'job_position_id' => 'nullable|exists:job_positions,id',
        ]);

        // Ensure only one type is provided
        if (($request->rental_id && $request->job_position_id) || 
            (!$request->rental_id && !$request->job_position_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide either rental_id or job_position_id, not both or neither'
            ], 400);
        }

        try {
            $userId = auth()->id();
            $type = $request->rental_id ? 'rental' : 'job';
            $itemId = $request->rental_id ?? $request->job_position_id;

            // Check if favorite exists
            $favorite = Favorite::where('user_id', $userId)
                ->where($type === 'rental' ? 'rental_id' : 'job_position_id', $itemId)
                ->first();

            if ($favorite) {
                // Remove from favorites
                $favorite->delete();
                return response()->json([
                    'success' => true,
                    'is_favorited' => false,
                    'message' => ucfirst($type) . ' removed from favorites',
                    'type' => $type,
                    'item_id' => $itemId
                ]);
            } else {
                // Add to favorites
                $favorite = Favorite::create([
                    'user_id' => $userId,
                    'rental_id' => $request->rental_id,
                    'job_position_id' => $request->job_position_id,
                ]);

                return response()->json([
                    'success' => true,
                    'is_favorited' => true,
                    'message' => ucfirst($type) . ' added to favorites',
                    'favorite' => $favorite,
                    'type' => $type,
                    'item_id' => $itemId
                ], 201);
            }
        } catch (\Exception $e) {
            Log::error('Toggle favorite error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle favorite'
            ], 500);
        }
    }

    /**
     * Check favorite status for rental
     */
    public function checkRentalFavoriteStatus(Request $request)
    {
        $request->validate([
            'rental_id' => 'required|exists:rentals,id',
        ]);

        $isFavorite = Favorite::where('user_id', auth()->id())
            ->where('rental_id', $request->rental_id)
            ->exists();

        return response()->json([
            'success' => true,
            'status' => $isFavorite,
            'is_favorited' => $isFavorite,
            'message' => $isFavorite
                ? 'Rental is in favorites'
                : 'Rental is not in favorites',
        ]);
    }

    /**
     * Check favorite status for job
     */
    public function checkJobFavoriteStatus(Request $request)
    {
        $request->validate([
            'job_position_id' => 'required|exists:job_positions,id',
        ]);

        $isFavorite = Favorite::where('user_id', auth()->id())
            ->where('job_position_id', $request->job_position_id)
            ->exists();

        return response()->json([
            'success' => true,
            'status' => $isFavorite,
            'is_favorited' => $isFavorite,
            'message' => $isFavorite
                ? 'Job is in favorites'
                : 'Job is not in favorites',
        ]);
    }

    /**
     * Remove rental from favorites
     */
    public function removeRentalFavorite(Request $request)
    {
        $request->validate([
            'rental_id' => 'required|exists:rentals,id',
        ]);

        $favorite = Favorite::where('user_id', auth()->id())
            ->where('rental_id', $request->rental_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'status' => false,
                'is_favorited' => false,
                'message' => 'Rental removed from favorites',
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => false,
            'is_favorited' => false,
            'message' => 'Rental not found in favorites',
        ], 404);
    }

    /**
     * Remove job from favorites
     */
    public function removeJobFavorite(Request $request)
    {
        $request->validate([
            'job_position_id' => 'required|exists:job_positions,id',
        ]);

        $favorite = Favorite::where('user_id', auth()->id())
            ->where('job_position_id', $request->job_position_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'status' => false,
                'is_favorited' => false,
                'message' => 'Job removed from favorites',
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => false,
            'is_favorited' => false,
            'message' => 'Job not found in favorites',
        ], 404);
    }

    /**
     * Add to favorites (legacy support)
     */
    public function addFavorite(Request $request)
    {
        $request->validate([
            'rental_id' => 'nullable|exists:rentals,id',
            'job_position_id' => 'nullable|exists:job_positions,id',
        ]);

        try {
            // Check if already exists
            $existing = Favorite::where('user_id', auth()->id())
                ->where(function ($query) use ($request) {
                    if ($request->rental_id) {
                        $query->where('rental_id', $request->rental_id);
                    }
                    if ($request->job_position_id) {
                        $query->where('job_position_id', $request->job_position_id);
                    }
                })
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => true,
                    'is_favorited' => true,
                    'message' => 'Already in favorites',
                    'favorite' => $existing
                ]);
            }

            $favorite = Favorite::create([
                'user_id' => auth()->id(),
                'rental_id' => $request->rental_id,
                'job_position_id' => $request->job_position_id,
            ]);

            return response()->json([
                'success' => true,
                'is_favorited' => true,
                'message' => 'Added to favorites',
                'favorite' => $favorite
            ], 201);
        } catch (\Exception $e) {
            Log::error('Add favorite error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add favorite'
            ], 500);
        }
    }

    /**
     * Get all favorite rentals for authenticated user
     */
    public function getFavorites()
    {
        try {
            $favorites = Favorite::where('user_id', auth()->id())
                ->with('rental')
                ->whereNotNull('rental_id')
                ->get();

            $rentals = $favorites->map(function ($favorite) {
                return $favorite->rental;
            })->filter();

            $response = RentalResource::collection($rentals);

            return response()->json([
                'success' => true,
                'data' => $response,
                'count' => $rentals->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Get favorites error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch favorites'
            ], 500);
        }
    }

    /**
     * Get all favorite jobs for authenticated user
     */
    public function getJobFavorites()
    {
        try {
            $favorites = Favorite::where('user_id', auth()->id())
                ->with('jobPosition')
                ->whereNotNull('job_position_id')
                ->get();

            $jobs = $favorites->map(function ($favorite) {
                return $favorite->jobPosition;
            })->filter();

            $response = JobPositionResource::collection($jobs);

            return response()->json([
                'success' => true,
                'data' => $response,
                'count' => $jobs->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Get job favorites error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch job favorites'
            ], 500);
        }
    }

    /**
     * Get all favorite IDs for quick checking (bulk status check)
     */
    public function getFavoriteIds(Request $request)
    {
        $request->validate([
            'type' => 'required|in:rental,job,both',
        ]);

        try {
            $query = Favorite::where('user_id', auth()->id());

            $result = [
                'success' => true,
            ];

            if ($request->type === 'rental' || $request->type === 'both') {
                $rentalIds = (clone $query)->whereNotNull('rental_id')
                    ->pluck('rental_id')
                    ->toArray();
                $result['rental_ids'] = $rentalIds;
            }

            if ($request->type === 'job' || $request->type === 'both') {
                $jobIds = (clone $query)->whereNotNull('job_position_id')
                    ->pluck('job_position_id')
                    ->toArray();
                $result['job_ids'] = $jobIds;
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Get favorite IDs error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch favorite IDs'
            ], 500);
        }
    }

    /**
     * Batch check favorite status
     */
    public function batchCheckStatus(Request $request)
    {
        $request->validate([
            'rental_ids' => 'nullable|array',
            'rental_ids.*' => 'exists:rentals,id',
            'job_ids' => 'nullable|array',
            'job_ids.*' => 'exists:job_positions,id',
        ]);

        try {
            $userId = auth()->id();
            $result = [
                'success' => true,
            ];

            if ($request->rental_ids) {
                $favoritedRentals = Favorite::where('user_id', $userId)
                    ->whereIn('rental_id', $request->rental_ids)
                    ->pluck('rental_id')
                    ->toArray();
                $result['favorited_rentals'] = $favoritedRentals;
            }

            if ($request->job_ids) {
                $favoritedJobs = Favorite::where('user_id', $userId)
                    ->whereIn('job_position_id', $request->job_ids)
                    ->pluck('job_position_id')
                    ->toArray();
                $result['favorited_jobs'] = $favoritedJobs;
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Batch check status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check favorite status'
            ], 500);
        }
    }

    /**
     * Remove favorite (legacy support)
     */
    public function removeFavorite(Request $request)
    {
        $request->validate([
            'favorite_id' => 'required|exists:favorites,id',
        ]);

        $favorite = Favorite::where('id', $request->favorite_id)
            ->where('user_id', auth()->id())
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'message' => 'Removed from favorites'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Favorite not found'
        ], 404);
    }
}
