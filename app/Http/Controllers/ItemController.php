<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * Get all items with pagination and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Item::with(['user'])->latest();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $query->search($request->search);
            }

            // Featured filter
            if ($request->has('featured') && $request->featured) {
                $query->featured();
            }

            // Price range filter
            if ($request->has('min_price') && $request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price') && $request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }

            // Paginate items
            $items = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $items->items(),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new item.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'image_url' => 'nullable|string|url',
                'description' => 'required|string|max:2000',
                'contact_email' => 'required|email|max:255',
                'contact_phone' => 'required|string|max:20',
                'featured' => 'boolean',
            ]);

            $data = [
                'user_id' => Auth::id(),
                'title' => $request->title,
                'price' => $request->price,
                'description' => $request->description,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'featured' => $request->featured ?? false,
            ];

            // Handle image URL from the upload service
            if ($request->has('image_url') && !empty($request->image_url)) {
                $data['image_url'] = $request->image_url;
            }

            $item = Item::create($data);
            $item->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Item created successfully',
                'data' => $item
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Item creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific item.
     */
    public function show(Item $item): JsonResponse
    {
        try {
            $item->load(['user']);

            return response()->json([
                'success' => true,
                'data' => $item
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an item.
     */
    public function update(Request $request, Item $item): JsonResponse
    {
        try {
            // Check if user is the author of the item
            if ($item->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to update this item'
                ], 403);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'image_url' => 'nullable|string|url',
                'description' => 'required|string|max:2000',
                'contact_email' => 'required|email|max:255',
                'contact_phone' => 'required|string|max:20',
                'featured' => 'boolean',
            ]);

            $item->update($request->only([
                'title', 'price', 'image_url', 'description',
                'contact_email', 'contact_phone', 'featured'
            ]));

            $item->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => $item
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an item.
     */
    public function destroy(Item $item): JsonResponse
    {
        try {
            // Check if user is the author of the item
            if ($item->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this item'
                ], 403);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's own items.
     */
    public function myItems(): JsonResponse
    {
        try {
            $items = Item::where('user_id', Auth::id())
                ->with(['user'])
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $items
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch your items',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
