<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\JoinGroupRequest;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    /**
     * Get all groups with optional filtering.
     */
    public function index(Request $request): JsonResponse
    {
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

        // If user is authenticated, add join status to each group
        if (Auth::check()) {
            $userId = Auth::id();
            foreach ($groups->items() as $group) {
                $group->is_joined = $group->members()->where('user_id', $userId)->exists();
            }
        }

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
    }

    /**
     * Get a specific group by ID.
     */
    public function show(Group $group): JsonResponse
    {
        $group->load(['creator', 'members']);
        
        // If user is authenticated, add join status
        if (Auth::check()) {
            $userId = Auth::id();
            $group->is_joined = $group->members()->where('user_id', $userId)->exists();
        }
        
        return response()->json([
            'success' => true,
            'data' => $group
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

            // Handle banner image URL (uploaded via existing upload service)
            if ($request->has('group_banner_image') && !empty($request->group_banner_image)) {
                $data['group_banner_image'] = $request->group_banner_image;
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
     * Join a group.
     */
    public function join(Group $group): JsonResponse
    {
        try {
            $user = Auth::user();
            
            \Log::info('Join group request', [
                'group_id' => $group->id,
                'user_id' => $user ? $user->id : 'null',
                'user_email' => $user ? $user->email : 'null'
            ]);
            
            if (!$user) {
                \Log::error('User not authenticated for join group');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Check if user is already a member
            if ($group->hasMember($user->id)) {
                \Log::info('User already a member', ['user_id' => $user->id, 'group_id' => $group->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'You are already a member of this group'
                ], 400);
            }

            // Add user to group using the model method
            $group->addMember($user->id);
            
            \Log::info('User successfully joined group', ['user_id' => $user->id, 'group_id' => $group->id]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully joined the group'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error joining group', [
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
            $user = Auth::user();
            $groups = $user->joinedGroups()->with(['creator', 'members'])->get();

            return response()->json([
                'success' => true,
                'data' => $groups
            ]);

        } catch (\Exception $e) {
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
     * Get members of a group.
     */
    public function members(Group $group): JsonResponse
    {
        try {
            $members = $group->members()->get();
            
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
