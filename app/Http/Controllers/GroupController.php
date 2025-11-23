<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\JoinGroupRequest;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    /**
     * Get all groups with optional filtering.
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
                Log::info('Failed to authenticate token in public groups route: ' . $e->getMessage());
            }
        }

        $query = Group::with(['creator', 'members']);

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by meeting type
        if ($request->has('meeting_type')) {
            $query->byMeetingType($request->meeting_type);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        $groups = $query->orderBy('created_at', 'desc')->paginate(20);

        // If user is authenticated, add join status and pending request status to each group
        $groupsData = [];
        if (Auth::check()) {
            $userId = Auth::id();
            foreach ($groups->items() as $group) {
                // Reload the group with members relationship to ensure pivot data is available
                $group->load('members');
                
                $isJoined = $group->members()
                    ->where('users.id', $userId)
                    ->wherePivot('status', 'accepted')
                    ->exists();
                
                $hasPendingRequest = $group->members()
                    ->where('users.id', $userId)
                    ->wherePivot('status', 'pending')
                    ->exists();
                
                // Debug logging
                Log::info('Group pending request check', [
                    'group_id' => $group->id,
                    'user_id' => $userId,
                    'has_pending_request' => $hasPendingRequest,
                    'pending_count' => $group->members()->wherePivot('status', 'pending')->count()
                ]);
                
                // Convert group to array and add the dynamic fields
                $groupData = $group->toArray();
                $groupData['is_joined'] = $isJoined;
                $groupData['has_pending_request'] = $hasPendingRequest;
                $groupsData[] = $groupData;
            }
        } else {
            // If not authenticated, just convert to array
            foreach ($groups->items() as $group) {
                $groupData = $group->toArray();
                $groupData['is_joined'] = false;
                $groupData['has_pending_request'] = false;
                $groupsData[] = $groupData;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $groupsData,
            'pagination' => [
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'per_page' => $groups->perPage(),
                'total' => $groups->total(),
            ]
        ]);
    }

    /**
     * Get a specific group by ID.
     */
    public function show(Group $group): JsonResponse
    {
        $group->load(['creator', 'members']);
        
        // Convert group to array
        $groupData = $group->toArray();
        
        // If user is authenticated, add join status and pending request status
        if (Auth::check()) {
            $userId = Auth::id();
            // Reload the group with members relationship to ensure pivot data is available
            $group->load('members');
            
            $groupData['is_joined'] = $group->members()
                ->where('users.id', $userId)
                ->wherePivot('status', 'accepted')
                ->exists();
            $groupData['has_pending_request'] = $group->members()
                ->where('users.id', $userId)
                ->wherePivot('status', 'pending')
                ->exists();
        } else {
            $groupData['is_joined'] = false;
            $groupData['has_pending_request'] = false;
        }
        
        return response()->json([
            'success' => true,
            'data' => $groupData
        ]);
    }

    /**
     * Create a new group.
     */
    public function store(CreateGroupRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();

            // Handle banner/cover image URL (uploaded via existing upload service)
            if ($request->has('group_banner_image') && !empty($request->group_banner_image)) {
                $data['group_banner_image'] = $request->group_banner_image;
            }
            
            // Also check for cover_image field (alternative name)
            if ($request->has('cover_image') && !empty($request->cover_image)) {
                $data['group_banner_image'] = $request->cover_image;
            }

            // Set default values for nullable fields if not provided
            if (!isset($data['privacy'])) {
                $data['privacy'] = 'open';
            }
            if (!isset($data['category'])) {
                $data['category'] = '';
            }
            if (!isset($data['meeting_type'])) {
                $data['meeting_type'] = 'Online';
            }

            $group = Group::create($data);
            
            // Automatically add the creator as a member of the group
            $group->members()->attach($data['created_by']);
            
            $group->load(['creator', 'members']);

            return response()->json([
                'success' => true,
                'message' => 'Group created successfully',
                'data' => $group
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create group',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Join a group or request to join (for private groups).
     */
    public function join(Group $group): JsonResponse
    {
        try {
            $user = Auth::user();
            
            Log::info('Join group request', [
                'group_id' => $group->id,
                'user_id' => $user ? $user->id : 'null',
                'user_email' => $user ? $user->email : 'null',
                'privacy' => $group->privacy
            ]);
            
            if (!$user) {
                Log::error('User not authenticated for join group');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Check if user is already a member (accepted)
            $existingMember = $group->members()->where('user_id', $user->id)->first();
            if ($existingMember && $existingMember->pivot->status === 'accepted') {
                Log::info('User already a member', ['user_id' => $user->id, 'group_id' => $group->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'You are already a member of this group'
                ], 400);
            }

            // Check if there's a pending request
            if ($existingMember && $existingMember->pivot->status === 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a pending request for this group'
                ], 400);
            }

            // For private groups, create a pending request
            if ($group->privacy === 'closed') {
                // Create or update request with pending status
                if ($existingMember) {
                    // Update existing rejected request to pending
                    $group->members()->updateExistingPivot($user->id, [
                        'status' => 'pending',
                        'joined_at' => now()
                    ]);
                } else {
                    // Create new pending request
                    $group->members()->attach($user->id, [
                        'status' => 'pending',
                        'joined_at' => now()
                    ]);
                }
                
                Log::info('Join request created for private group', ['user_id' => $user->id, 'group_id' => $group->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Join request sent. Waiting for approval.',
                    'status' => 'pending'
                ]);
            } else {
                // For open groups, directly add as member
                if ($existingMember) {
                    // Update status to accepted if it was rejected
                    $group->members()->updateExistingPivot($user->id, [
                        'status' => 'accepted',
                        'joined_at' => now()
                    ]);
                } else {
                    $group->members()->attach($user->id, [
                        'status' => 'accepted',
                        'joined_at' => now()
                    ]);
                }
                
                Log::info('User successfully joined group', ['user_id' => $user->id, 'group_id' => $group->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully joined the group',
                    'status' => 'accepted'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error joining group', [
                'group_id' => $group->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to join group',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Leave a group.
     */
    public function leave(Group $group): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Check if user is the group creator
            if ($group->created_by === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot leave your own group'
                ], 400);
            }
            
            // Check if user is a member
            if (!$group->hasMember($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 400);
            }

            // Remove user from group using the model method
            $group->removeMember($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Successfully left the group'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to leave group',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's joined groups.
     */
    public function joined(): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            // Get groups where the user is a member
            $groups = Group::whereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['creator', 'members'])
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $groups
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching joined groups', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch joined groups',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search groups.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = Group::with(['creator', 'members']);

            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhere('category', 'like', "%{$searchTerm}%");
                });
            }

            $groups = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $groups->items(),
                'pagination' => [
                    'current_page' => $groups->currentPage(),
                    'last_page' => $groups->lastPage(),
                    'per_page' => $groups->perPage(),
                    'total' => $groups->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search groups',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a group.
     */
    public function update(CreateGroupRequest $request, Group $group): JsonResponse
    {
        try {
            // Check if user is the creator of the group
            if ($group->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to update this group'
                ], 403);
            }

            $data = $request->validated();

            // Handle banner image upload
            if ($request->hasFile('group_banner_image')) {
                // Delete old image if exists
                if ($group->group_banner_image) {
                    $oldPath = str_replace('/storage/', '', $group->group_banner_image);
                    Storage::disk('public')->delete($oldPath);
                }

                $file = $request->file('group_banner_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('group_banners', $filename, 'public');
                $data['group_banner_image'] = Storage::url($path);
            }

            $group->update($data);
            $group->load(['creator', 'members']);

            return response()->json([
                'success' => true,
                'message' => 'Group updated successfully',
                'data' => $group
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update group',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get members of a group (only accepted members).
     */
    public function members(Group $group): JsonResponse
    {
        try {
            $members = $group->members()->wherePivot('status', 'accepted')->get();
            
            $formattedMembers = $members->map(function ($member) {
                return [
                    'id' => $member->id,
                    'first_name' => $member->first_name,
                    'last_name' => $member->last_name,
                    'email' => $member->email,
                    'profile_image_path' => $member->profile_image_path,
                    'phone_number' => $member->phone_number,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedMembers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch members',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending join requests for a group (owner only).
     */
    public function pendingRequests(Group $group): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Check if user is the group owner
            if ($group->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to view requests for this group'
                ], 403);
            }

            $pendingRequests = $group->members()
                ->wherePivot('status', 'pending')
                ->get();
            
            $formattedRequests = $pendingRequests->map(function ($member) {
                return [
                    'id' => $member->id,
                    'first_name' => $member->first_name,
                    'last_name' => $member->last_name,
                    'email' => $member->email,
                    'profile_image_path' => $member->profile_image_path,
                    'phone_number' => $member->phone_number,
                    'requested_at' => $member->pivot->joined_at,
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
     * Approve or reject a join request (owner only).
     */
    public function approveRequest(Group $group, Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Check if user is the group owner
            if ($group->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to approve requests for this group'
                ], 403);
            }

            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'action' => 'required|string|in:approve,reject'
            ]);

            $requestUserId = $request->user_id;
            $action = $request->action;

            // Check if request exists
            $member = $group->members()->where('user_id', $requestUserId)->first();
            if (!$member || $member->pivot->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending request found for this user'
                ], 404);
            }

            if ($action === 'approve') {
                // Update status to accepted
                $group->members()->updateExistingPivot($requestUserId, [
                    'status' => 'accepted',
                    'joined_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Join request approved successfully'
                ]);
            } else {
                // Update status to rejected
                $group->members()->updateExistingPivot($requestUserId, [
                    'status' => 'rejected'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Join request rejected'
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

    /**
     * Delete a group.
     */
    public function destroy(Group $group): JsonResponse
    {
        try {
            // Check if user is the creator of the group
            if ($group->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this group'
                ], 403);
            }

            // Delete banner image if exists
            if ($group->group_banner_image) {
                $oldPath = str_replace('/storage/', '', $group->group_banner_image);
                Storage::disk('public')->delete($oldPath);
            }

            $group->delete();

            return response()->json([
                'success' => true,
                'message' => 'Group deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete group',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
