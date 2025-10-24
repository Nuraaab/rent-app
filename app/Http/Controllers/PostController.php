<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Get all posts with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Post::withDetails()->latest();

            // Paginate posts
            $posts = $query->paginate(20);

            // If user is authenticated, add like status to each post
            if (Auth::check()) {
                $userId = Auth::id();
                foreach ($posts->items() as $post) {
                    $post->is_liked = $post->isLikedBy($userId);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new post.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'content' => 'required|string|max:2000',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $data = [
                'user_id' => Auth::id(),
                'content' => $request->content,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('post_images', $filename, 'public');
                $data['image_url'] = Storage::url($path);
            }

            $post = Post::create($data);
            $post->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Like or unlike a post.
     */
    public function toggleLike(Post $post): JsonResponse
    {
        try {
            $user = Auth::user();
            $isLiked = $post->isLikedBy($user->id);

            if ($isLiked) {
                // Unlike the post
                $post->likes()->detach($user->id);
                $post->decrement('likes_count');
            } else {
                // Like the post
                $post->likes()->attach($user->id);
                $post->increment('likes_count');
            }

            return response()->json([
                'success' => true,
                'is_liked' => !$isLiked,
                'likes_count' => $post->fresh()->likes_count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle like',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a comment to a post.
     */
    public function addComment(Request $request, Post $post): JsonResponse
    {
        try {
            $request->validate([
                'content' => 'required|string|max:500'
            ]);

            $comment = Comment::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
                'content' => $request->content,
            ]);

            // Update comments count
            $post->increment('comments_count');

            $comment->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => $comment
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comments for a post.
     */
    public function getComments(Post $post): JsonResponse
    {
        try {
            $comments = $post->comments()->with(['user'])->get();

            return response()->json([
                'success' => true,
                'data' => $comments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a post.
     */
    public function destroy(Post $post): JsonResponse
    {
        try {
            // Check if user is the author of the post
            if ($post->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this post'
                ], 403);
            }

            // Delete associated image if exists
            if ($post->image_url) {
                $imagePath = str_replace('/storage/', '', $post->image_url);
                Storage::disk('public')->delete($imagePath);
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
