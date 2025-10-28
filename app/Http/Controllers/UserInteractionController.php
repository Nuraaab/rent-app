<?php

namespace App\Http\Controllers;

use App\Models\UserInteraction;
use App\Models\NudgeUsage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserInteractionController extends Controller
{
    /**
     * Send an interaction (like, nudge, or super_like) to a user.
     */
    public function sendInteraction(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'target_user_id' => 'required|exists:users,id',
            'type' => 'required|string|in:like,nudge,super_like',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $currentUserId = Auth::id();
            $targetUserId = $request->target_user_id;
            $type = $request->type;

            // Prevent users from interacting with themselves
            if ($currentUserId == $targetUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot interact with yourself',
                ], 400);
            }

            // Check if interaction already exists
            $existingInteraction = UserInteraction::where('user_id', $currentUserId)
                ->where('target_user_id', $targetUserId)
                ->where('type', $type)
                ->first();

            if ($existingInteraction) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already sent this interaction',
                ], 400);
            }

            // For nudges, check if user has available nudges
            if ($type === 'nudge') {
                $nudgeUsage = NudgeUsage::firstOrCreate(
                    ['user_id' => $currentUserId],
                    [
                        'nudges_used' => 0,
                        'nudges_purchased' => 0,
                        'last_reset_date' => now()->toDateString(),
                    ]
                );

                if (!$nudgeUsage->canNudge()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have used all your available nudges for this month',
                        'available_nudges' => 0,
                    ], 403);
                }

                // Increment nudge usage
                $nudgeUsage->incrementUsage();
            }

            // Create the interaction
            $interaction = UserInteraction::create([
                'user_id' => $currentUserId,
                'target_user_id' => $targetUserId,
                'type' => $type,
            ]);

            // Get target user info
            $targetUser = User::find($targetUserId);

            // Get updated nudge usage if it was a nudge
            $updatedNudgeUsage = null;
            if ($type === 'nudge') {
                $nudgeUsage->refresh();
                $updatedNudgeUsage = [
                    'available_nudges' => $nudgeUsage->available_nudges,
                    'nudges_used' => $nudgeUsage->nudges_used,
                    'nudges_purchased' => $nudgeUsage->nudges_purchased,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' sent successfully',
                'data' => [
                    'interaction' => [
                        'id' => $interaction->id,
                        'type' => $interaction->type,
                        'target_user' => [
                            'id' => $targetUser->id,
                            'first_name' => $targetUser->first_name,
                            'last_name' => $targetUser->last_name,
                        ],
                        'created_at' => $interaction->created_at->toISOString(),
                    ],
                    'nudge_usage' => $updatedNudgeUsage,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error sending interaction', [
                'user_id' => Auth::id(),
                'target_user_id' => $request->target_user_id,
                'type' => $request->type,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send interaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's nudge usage statistics.
     */
    public function getNudgeUsage(): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            $nudgeUsage = NudgeUsage::firstOrCreate(
                ['user_id' => $userId],
                [
                    'nudges_used' => 0,
                    'nudges_purchased' => 0,
                    'last_reset_date' => now()->toDateString(),
                ]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'available_nudges' => $nudgeUsage->available_nudges,
                    'nudges_used' => $nudgeUsage->nudges_used,
                    'nudges_purchased' => $nudgeUsage->nudges_purchased,
                    'free_nudges_per_month' => 3,
                    'last_reset_date' => $nudgeUsage->last_reset_date->toDateString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching nudge usage', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch nudge usage',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get interactions received by the current user.
     */
    public function getReceivedInteractions(): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            $interactions = UserInteraction::where('target_user_id', $userId)
                ->with(['user:id,first_name,last_name,profile_image_path'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $interactions->map(function ($interaction) {
                    return [
                        'id' => $interaction->id,
                        'type' => $interaction->type,
                        'user' => [
                            'id' => $interaction->user->id,
                            'first_name' => $interaction->user->first_name,
                            'last_name' => $interaction->user->last_name,
                            'profile_image_path' => $interaction->user->profile_image_path,
                        ],
                        'created_at' => $interaction->created_at->toISOString(),
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching received interactions', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch interactions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
