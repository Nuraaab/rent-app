<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Favorite::with('user', 'rental', 'jobPosition');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            if ($request->type == 'property') {
                $query->whereNotNull('rental_id');
            } elseif ($request->type == 'job') {
                $query->whereNotNull('job_id');
            }
        }

        $favorites = $query->latest()->paginate(20)->appends($request->except('page'));
        
        return view('admin.favorites.index', compact('favorites'));
    }

    public function destroy(Favorite $favorite)
    {
        $favorite->delete();

        return back()->with('success', 'Favorite removed successfully');
    }
}

