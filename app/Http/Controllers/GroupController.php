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
            $group->load(['creator']);

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
            if ($group->hasMember(Auth::id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already a member of this group'
                ], 400);
            }

            $group->addMember(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Successfully joined the group'
            ]);

        } catch (\Exception $e) {
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
            if (!$group->hasMember(Auth::id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 400);
            }

            $group->removeMember(Auth::id());

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
        $groups = Group::joinedBy(Auth::id())
            ->with(['creator', 'members'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    /**
     * Search groups.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q');
        
        if (empty($search)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $groups = Group::search($search)
            ->with(['creator', 'members'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
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
