<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Get all services with pagination and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Service::with(['user'])->latest();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $query->search($request->search);
            }

            // Featured filter
            if ($request->has('featured') && $request->featured) {
                $query->featured();
            }

            // Paginate services
            $services = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $services->items(),
                'pagination' => [
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch services',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new service.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'image_url' => 'nullable|string|url',
                'service_link' => 'nullable|string|url',
                'contact_phone' => 'required|string|max:20',
                'contact_email' => 'required|email|max:255',
                'featured' => 'boolean',
            ]);

            $data = [
                'user_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'service_link' => $request->service_link,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'featured' => $request->featured ?? false,
            ];

            // Handle image URL from the upload service
            if ($request->has('image_url') && !empty($request->image_url)) {
                $data['image_url'] = $request->image_url;
            }

            $service = Service::create($data);
            $service->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $service
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Service creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific service.
     */
    public function show(Service $service): JsonResponse
    {
        try {
            $service->load(['user']);

            return response()->json([
                'success' => true,
                'data' => $service
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a service.
     */
    public function update(Request $request, Service $service): JsonResponse
    {
        try {
            // Check if user is the author of the service
            if ($service->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to update this service'
                ], 403);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'image_url' => 'nullable|string|url',
                'service_link' => 'nullable|string|url',
                'contact_phone' => 'required|string|max:20',
                'contact_email' => 'required|email|max:255',
                'featured' => 'boolean',
            ]);

            $service->update($request->only([
                'title', 'description', 'image_url', 'service_link',
                'contact_phone', 'contact_email', 'featured'
            ]));

            $service->load(['user']);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a service.
     */
    public function destroy(Service $service): JsonResponse
    {
        try {
            // Check if user is the author of the service
            if ($service->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this service'
                ], 403);
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's own services.
     */
    public function myServices(): JsonResponse
    {
        try {
            $userId = Auth::id();
            \Log::info('🔍 [MyServices] User ID: ' . $userId);
            
            if (!$userId) {
                \Log::warning('🔴 [MyServices] User not authenticated');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'data' => []
                ], 401);
            }
            
            $services = Service::where('user_id', $userId)
                ->with(['user'])
                ->latest()
                ->get();
            
            \Log::info('🔍 [MyServices] Found ' . $services->count() . ' services for user ' . $userId);
            
            if ($services->count() > 0) {
                \Log::info('🔍 [MyServices] First service ID: ' . $services->first()->id);
                \Log::info('🔍 [MyServices] First service title: ' . $services->first()->title);
            }

            return response()->json([
                'success' => true,
                'data' => $services
            ]);

        } catch (\Exception $e) {
            \Log::error('🔴 [MyServices] Error: ' . $e->getMessage());
            \Log::error('🔴 [MyServices] Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch your services',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
