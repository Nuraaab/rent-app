<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Http\Resources\RentalResource;
use App\Http\Resources\JobPositionResource;
class FavoriteController extends Controller
{
    public function addFavorite(Request $request)
    {
        $request->validate([
            'rental_id' => 'nullable|exists:rentals,id',
            'job_position_id' => 'nullable|exists:job_positions,id',
        ]);

        $favorite = Favorite::create([
            'user_id' => auth()->id(),
            'rental_id' => $request->rental_id,
            'job_position_id' => $request->job_position_id,
        ]);

        return response()->json(['message' => 'Added to favorites', 'favorite' => $favorite]);
    }

    public function removeFavorite(Request $request)
    {
        $request->validate([
            'favorite_id' => 'required|exists:favorites,id',
        ]);

        $favorite = Favorite::where('id', $request->favorite_id)
            ->where('user_id', auth()->id())
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Removed from favorites']);
        }

        return response()->json(['message' => 'Favorite not found'], 404);
    }

    public function checkRentalFavoriteStatus(Request $request)
    {
       
        $request->validate([
            'rental_id' => 'required|exists:rentals,id',
        ]);

        $isFavorite = Favorite::where('user_id', auth()->id())
            ->where('rental_id', $request->rental_id)
            ->exists();
        return response()->json([
            'status' => $isFavorite,
            'message' => $isFavorite
                ? 'Rental is in favorites'
                : 'Rental is not in favorites',
        ]);
    }

    public function checkJobFavoriteStatus(Request $request)
    {
        $request->validate([
            'job_position_id' => 'required|exists:job_positions,id',
        ]);

        $isFavorite = Favorite::where('user_id', auth()->id())
            ->where('job_position_id', $request->job_position_id)
            ->exists();
        return response()->json([
            'status' => $isFavorite,
            'message' => $isFavorite
                ? 'Job is in favorites'
                : 'Job is not in favorites',
        ]);
    }

    public function removeRentalFavorite(Request $request)
    {
        $request->validate([
            'rental_id' => 'required|exists:rentals,id',
        ]);
        $favorite = Favorite::where('user_id', auth()->id())
            ->where('rental_id', $request->rental_id)
            ->first();
        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => true,
                'message' => 'Rental favorite removed successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Rental favorite not found',
        ], 404);
    }

    public function removeJobFavorite(Request $request)
    {
        $request->validate([
            'job_position_id' => 'required|exists:job_positions,id',
        ]);
        $favorite = Favorite::where('user_id', auth()->id())
            ->where('job_position_id', $request->job_position_id)
            ->first();
        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => true,
                'message' => 'Job favorite removed successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Job favorite not found',
        ], 404);
    }




    public function getFavorites()
    {
        // $favorites = Favorite::where('user_id', auth()->id())
        //     ->with(['rental', 'jobPosition'])
        //     ->get();
        $favorites = Favorite::where('user_id', auth()->id())->get();
        $rentals = $favorites->map(function ($favorite) {
            return $favorite->rental; // Get the related rental for each favorite
        })->filter(); 
       $response =  RentalResource::collection($rentals);

        return response()->json($response);
    }

    public function getJobFavorites(){
        $favorites = Favorite::where('user_id', auth()->id())->get();
        $jobs = $favorites->map(function ($favorite) {
            return $favorite->jobPosition; 
        })->filter(); 
       $response =  JobPositionResource::collection($jobs);

        return response()->json($response);
    }
}
