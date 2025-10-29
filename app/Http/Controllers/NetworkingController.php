<?php

namespace App\Http\Controllers;

use App\Models\NetworkingProfile;
use App\Models\NetworkingConnection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NetworkingController extends Controller
{
    /**
     * Get all networking profiles with optional filtering.
     */
    public function index(Request $request): JsonResponse
    {
        // Manually authenticate user if token is present (for public routes)
        if ($request->bearerToken()) {
            try {
                $token = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken());
                if ($token && $token->tokenable) {
                    Auth::setUser($token->tokenable);
                }
            } catch (\Exception $e) {
                // Token invalid or expired, continue as unauthenticated
                Log::info('Failed to authenticate token in public networking route: ' . $e->getMessage());
            }
        }

        $query = NetworkingProfile::with(['user']);

        // Filter by privacy
        if ($request->has('privacy') && $request->privacy) {
            $query->where('privacy', $request->privacy);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $profiles = $query->orderBy('created_at', 'desc')->paginate(20);

        // If user is authenticated, add connection status to each profile
        if (Auth::check()) {
            $userId = Auth::id();
            foreach ($profiles->items() as $profile) {
                $profile->is_connected = $profile->connections()
                    ->where('user_id', $userId)
                    ->where('status', 'accepted')
                    ->exists();
            }
        }

        return response()->json([
            'success' => true,
            'data' => $profiles->items(),
            'pagination' => [
                'current_page' => $profiles->currentPage(),
                'last_page' => $profiles->lastPage(),
                'per_page' => $profiles->perPage(),
                'total' => $profiles->total(),
            ]
        ]);
    }

    /**
     * Get a specific networking profile by ID.
     */
    public function show(NetworkingProfile $networkingProfile): JsonResponse
    {
        $networkingProfile->load(['user']);

        // If user is authenticated, add connection status
        if (Auth::check()) {
            $userId = Auth::id();
            $networkingProfile->is_connected = $networkingProfile->connections()
                ->where('user_id', $userId)
                ->where('status', 'accepted')
                ->exists();
        }

        // Build a lightweight list of accepted connections with user info
        $connections = $networkingProfile->acceptedConnections()
            ->with(['user:id,first_name,last_name,profile_image_path'])
            ->get()
            ->map(function ($conn) {
                return [
                    'id' => $conn->id,
                    'user' => [
                        'id' => $conn->user->id,
                        'first_name' => $conn->user->first_name,
                        'last_name' => $conn->user->last_name,
                        'profile_image_path' => $conn->user->profile_image_path,
                    ],
                    'connected_at' => optional($conn->created_at)->toISOString(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $networkingProfile,
            'connections' => $connections,
        ]);
    }

    /**
     * Create a new networking profile.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'cover_image' => 'nullable|string',
            'privacy' => 'nullable|string|in:open,closed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['user_id'] = Auth::id();
            
            // Set default privacy if not provided
            if (!isset($data['privacy'])) {
                $data['privacy'] = 'open';
            }

            // Handle cover image URL
            if ($request->has('cover_image') && !empty($request->cover_image)) {
                $data['cover_image'] = $request->cover_image;
            }

            $profile = NetworkingProfile::create($data);
            $profile->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Networking profile created successfully',
                'data' => $profile
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating networking profile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create networking profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a networking profile.
     */
    public function update(Request $request, NetworkingProfile $networkingProfile): JsonResponse
    {
        // Check if user owns the profile
        if ($networkingProfile->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only update your own networking profile',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'privacy' => 'nullable|string|in:open,closed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();

            $networkingProfile->update($data);
            $networkingProfile->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Networking profile updated successfully',
                'data' => $networkingProfile
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating networking profile', [
                'profile_id' => $networkingProfile->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update networking profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a networking profile.
     */
    public function destroy(NetworkingProfile $networkingProfile): JsonResponse
    {
        // Check if user owns the profile
        if ($networkingProfile->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own networking profile',
            ], 403);
        }

        try {
            $networkingProfile->delete();

            return response()->json([
                'success' => true,
                'message' => 'Networking profile deleted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting networking profile', [
                'profile_id' => $networkingProfile->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete networking profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connect to a networking profile.
     */
    public function connect(NetworkingProfile $networkingProfile): JsonResponse
    {
        try {
            $userId = Auth::id();

            // Prevent users from connecting to their own profile
            if ($networkingProfile->user_id == $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot connect to your own profile',
                ], 400);
            }

            // Check if connection already exists
            $existingConnection = NetworkingConnection::where('user_id', $userId)
                ->where('networking_profile_id', $networkingProfile->id)
                ->first();

            if ($existingConnection) {
                // If connection exists, toggle it (remove if accepted, add if not)
                if ($existingConnection->status === 'accepted') {
                    $existingConnection->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Connection removed successfully',
                        'connected' => false,
                    ]);
                } else {
                    // Update status to accepted
                    $existingConnection->status = 'accepted';
                    $existingConnection->save();
                    return response()->json([
                        'success' => true,
                        'message' => 'Connected successfully',
                        'connected' => true,
                    ]);
                }
            }

            // Create new connection (automatically accepted for networking)
            NetworkingConnection::create([
                'user_id' => $userId,
                'networking_profile_id' => $networkingProfile->id,
                'status' => 'accepted',
            ]);

            $networkingProfile->refresh();
            $networkingProfile->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Connected successfully',
                'connected' => true,
                'data' => $networkingProfile,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error connecting to networking profile', [
                'profile_id' => $networkingProfile->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
