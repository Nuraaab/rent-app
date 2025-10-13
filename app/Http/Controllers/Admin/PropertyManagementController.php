<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class PropertyManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Rental::with('user');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by listing type
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $properties = $query->paginate(20)->appends($request->except('page'));

        // Get filter options
        $categories = Rental::distinct()->whereNotNull('category')->pluck('category')->filter();
        
        return view('admin.properties.index', compact('properties', 'categories'));
    }

    public function show(Rental $property)
    {
        $property->load('user', 'houseGallery', 'houseOffers', 'reviews', 'favorites');
        return view('admin.properties.show', compact('property'));
    }

    public function edit(Rental $property)
    {
        return view('admin.properties.edit', compact('property'));
    }

    public function update(Request $request, Rental $property)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'address' => 'required|string|max:500',
            'category' => 'nullable|string',
            'listing_type' => 'nullable|string',
            'number_of_bedrooms' => 'nullable|integer',
            'number_of_baths' => 'nullable|integer',
            'max_number_of_gusts' => 'nullable|integer',
        ]);

        $property->update($validated);

        return redirect()->route('admin.properties.show', $property)
            ->with('success', 'Property updated successfully');
    }

    public function destroy(Rental $property)
    {
        $property->delete();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Property deleted successfully');
    }
}

