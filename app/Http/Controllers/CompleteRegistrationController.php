<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompleteRegistrationController extends Controller
{
    public function complete(Request $request)
    {
        $user = $request->user();

        // Validate the incoming request data
        $validatedData = $request->validate([
            'height' => 'nullable|string|max:20',
            'pets' => 'nullable|string|max:20',
            'children' => 'nullable|string|max:40',
            'politics' => 'nullable|string|max:20',
            'faith_identity' => 'nullable|string|max:60',
            'education' => 'nullable|string|max:40',
            'body_type' => 'nullable|string|max:20',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'user_pictures' => 'nullable|array',
            'user_pictures.*' => 'nullable|string|max:255',
        ]);

        // Update the user's profile with the validated data
        $user->update($validatedData);

        // Store additional images in user_pictures table if provided
        if ($request->has('user_pictures') && is_array($request->user_pictures)) {
            foreach ($request->user_pictures as $picturePath) {
                $user->userPictures()->create([
                    'picture_path' => $picturePath,
                ]);
            }
        }

        return response()->json(['message' => 'Registration completed successfully.']);
    }
}
