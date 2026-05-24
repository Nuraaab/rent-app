<?php

namespace App\Http\Controllers;

use App\Models\NetworkingProfile;
use App\Models\NetworkingConnection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NetworkingController extends Controller {
    /**
     * Get all networking profiles with optional filtering.
     */
    public function index(Request $request): JsonResponse {
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

        $query = NetworkingProfile::with(['user.userPictures']);

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

        // If user is authenticated, add connection status and pending request status to each profile
        if (Auth::check()) {
            $userId = Auth::id();
            foreach ($profiles->items() as $profile) {
                $profile->is_connected = $profile->connections()
                    ->where('user_id', $userId)
                    ->where('status', 'accepted')
                    ->exists();
                // Also add pending status
                $profile->has_pending_request = $profile->connections()
                    ->where('user_id', $userId)
                    ->where('status', 'pending')
                    ->exists();
                $profile->can_edit = ((int) $profile->user_id === (int) $userId);
            }
        } else {
            foreach ($profiles->items() as $profile) {
                $profile->can_edit = false;
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
    public function show(NetworkingProfile $networkingProfile): JsonResponse {
        $networkingProfile->load(['user.userPictures']);

        if (Auth::check()) {
            $userId = Auth::id();
            $networkingProfile->is_connected = $networkingProfile->connections()
                ->where('user_id', $userId)
                ->where('status', 'accepted')
                ->exists();
            $networkingProfile->has_pending_request = $networkingProfile->connections()
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->exists();
            $networkingProfile->can_edit = ((int) $networkingProfile->user_id === (int) $userId);
        } else {
            $networkingProfile->can_edit = false;
        }

        $connections = $networkingProfile->acceptedConnections()
            ->with([
                'user:id,first_name,last_name,email,phone_number,profile_image_path,height,pets,children,politics,faith_identity,education,body_type,exercise',
                'user.userPictures'
            ])
            ->get()
            ->map(function ($conn) {
                return [
                    'id' => $conn->id,
                    'user' => [
                        'id' => $conn->user->id,
                        'first_name' => $conn->user->first_name,
                        'last_name' => $conn->user->last_name,
                        'email' => $conn->user->email,
                        'phone_number' => $conn->user->phone_number,
                        'profile_image_path' => $conn->user->profile_image_path,
                        'height' => $conn->user->height,
                        'pets' => $conn->user->pets,
                        'children' => $conn->user->children,
                        'politics' => $conn->user->politics,
                        'faith_identity' => $conn->user->faith_identity,
                        'education' => $conn->user->education,
                        'body_type' => $conn->user->body_type,
                        'exercise' => $conn->user->exercise,
                        'user_pictures' => $conn->user->userPictures->map(function ($picture) {
                            return [
                                'id' => $picture->id,
                                'user_id' => $picture->user_id,
                                'picture_path' => $picture->picture_path,
                                'picture_url' => $picture->picture_url,
                                'created_at' => $picture->created_at,
                                'updated_at' => $picture->updated_at,
                            ];
                        })->values(),
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
    public function store(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'privacy' => 'nullable|string|in:open,closed'
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

            if (!isset($data['privacy'])) {
                $data['privacy'] = 'open';
            }

            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('assets/images/networks');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $fileName);
                $data['cover_image'] = 'assets/images/networks/' . $fileName;
            }

            $profile = NetworkingProfile::create($data);

            NetworkingConnection::create([
                'user_id' => Auth::id(),
                'networking_profile_id' => $profile->id,
                'status' => 'accepted',
            ]);

            $profile->load(['user']);
            $profile->can_edit = true;

            return response()->json([
                'success' => true,
                'message' => 'Networking profile created successfully',
                'data' => $profile
            ], 201);
        } catch (\Exception $e) {
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
    public function update(Request $request, NetworkingProfile $networkingProfile): JsonResponse {
        // Check if user owns the profile
        if ((int) $networkingProfile->user_id !== (int) Auth::id()) {
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
            'height' => 'nullable|string|max:20',
            'pets' => 'nullable|string|in:cat,dog,other',
            'children' => 'nullable|string|in:dont_want,want_someday,have_and_want_more,have_and_dont_want_more',
            'politics' => 'nullable|string|in:liberal,moderate,conservative,other',
            'faith_identity' => 'nullable|string|in:agnostic,anglican,baptist,catholic,church_of_christ,episcopalian,evangelical,lutheran,methodist,nazarene,non_denominational,orthodox,pentecostal,presbyterian,spiritual_but_not_religious,not_sure_yet,other',
            'education' => 'nullable|string|in:high_school,some_college,associates_degree,graduate_degree,phd_post_doctoral',
            'body_type' => 'nullable|string|in:fit,curvy,slim,chubby',
            'exercise' => 'nullable|string|in:never,sometimes,often',
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
            $networkingProfile->can_edit = true;

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
    public function destroy(NetworkingProfile $networkingProfile): JsonResponse {
        // Check if user owns the profile
        if ((int) $networkingProfile->user_id !== (int) Auth::id()) {
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
     * Connect to a networking profile or request connection (for private profiles).
     */
    public function connect(NetworkingProfile $networkingProfile): JsonResponse {
        try {
            $userId = Auth::id();

            // Prevent users from connecting to their own profile (they're already connected automatically on creation)
            if ($networkingProfile->user_id == $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already connected to your own profile',
                ], 400);
            }

            // Check if connection already exists
            $existingConnection = NetworkingConnection::where('user_id', $userId)
                ->where('networking_profile_id', $networkingProfile->id)
                ->first();

            if ($existingConnection) {
                // If connection exists and is accepted, remove it (disconnect)
                if ($existingConnection->status === 'accepted') {
                    $existingConnection->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Connection removed successfully',
                        'connected' => false,
                    ]);
                } else if ($existingConnection->status === 'pending') {
                    // Already has a pending request
                    return response()->json([
                        'success' => false,
                        'message' => 'You already have a pending connection request',
                    ], 400);
                } else {
                    // Rejected connection - create new pending request
                    $existingConnection->status = 'pending';
                    $existingConnection->save();

                    if ($networkingProfile->privacy === 'closed') {
                        return response()->json([
                            'success' => true,
                            'message' => 'Connection request sent. Waiting for approval.',
                            'connected' => false,
                            'status' => 'pending',
                        ]);
                    } else {
                        // For open profiles, auto-accept
                        $existingConnection->status = 'accepted';
                        $existingConnection->save();
                        return response()->json([
                            'success' => true,
                            'message' => 'Connected successfully',
                            'connected' => true,
                        ]);
                    }
                }
            }

            // For private profiles, create pending request
            if ($networkingProfile->privacy === 'closed') {
                NetworkingConnection::create([
                    'user_id' => $userId,
                    'networking_profile_id' => $networkingProfile->id,
                    'status' => 'pending',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Connection request sent. Waiting for approval.',
                    'connected' => false,
                    'status' => 'pending',
                ]);
            } else {
                // For open profiles, create accepted connection
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
            }
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

    /**
     * Get pending connection requests for a networking profile (owner only).
     */
    public function pendingRequests(NetworkingProfile $networkingProfile): JsonResponse {
        try {
            $user = Auth::user();

            if ((int) $networkingProfile->user_id !== (int) $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to view requests for this profile'
                ], 403);
            }

            $pendingRequests = NetworkingConnection::where('networking_profile_id', $networkingProfile->id)
                ->where('status', 'pending')
                ->with([
                    'user:id,first_name,last_name,email,profile_image_path,phone_number,height,pets,children,politics,faith_identity,education,body_type,exercise',
                    'user.userPictures'
                ])
                ->get();

            $formattedRequests = $pendingRequests->map(function ($connection) {
                return [
                    'id' => $connection->user->id,
                    'first_name' => $connection->user->first_name,
                    'last_name' => $connection->user->last_name,
                    'email' => $connection->user->email,
                    'profile_image_path' => $connection->user->profile_image_path,
                    'phone_number' => $connection->user->phone_number,
                    'height' => $connection->user->height,
                    'pets' => $connection->user->pets,
                    'children' => $connection->user->children,
                    'politics' => $connection->user->politics,
                    'faith_identity' => $connection->user->faith_identity,
                    'education' => $connection->user->education,
                    'body_type' => $connection->user->body_type,
                    'exercise' => $connection->user->exercise,
                    'requested_at' => $connection->created_at,
                    'connection_id' => $connection->id,
                    'user_pictures' => $connection->user->userPictures->map(function ($picture) {
                        return [
                            'id' => $picture->id,
                            'user_id' => $picture->user_id,
                            'picture_path' => $picture->picture_path,
                            'picture_url' => $picture->picture_url,
                            'created_at' => $picture->created_at,
                            'updated_at' => $picture->updated_at,
                        ];
                    })->values(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedRequests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve or reject a connection request (owner only).
     */
    public function approveRequest(NetworkingProfile $networkingProfile, Request $request): JsonResponse {
        try {
            $user = Auth::user();

            // Check if user owns the profile
            if ((int) $networkingProfile->user_id !== (int) $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to approve requests for this profile'
                ], 403);
            }

            $request->validate([
                'connection_id' => 'required|integer|exists:networking_connections,id',
                'action' => 'required|string|in:approve,reject'
            ]);

            $connectionId = $request->connection_id;
            $action = $request->action;

            // Check if connection exists and belongs to this profile
            $connection = NetworkingConnection::where('id', $connectionId)
                ->where('networking_profile_id', $networkingProfile->id)
                ->where('status', 'pending')
                ->first();

            if (!$connection) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending request found'
                ], 404);
            }

            if ($action === 'approve') {
                // Update status to accepted
                $connection->status = 'accepted';
                $connection->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Connection request approved successfully'
                ]);
            } else {
                // Update status to rejected
                $connection->status = 'rejected';
                $connection->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Connection request rejected'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
