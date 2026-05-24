<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompleteRegistrationController extends Controller {
    public function complete(Request $request) {
        $user = $request->user();

        $isProfileCompleted =
            !empty($user->first_name) &&
            !empty($user->last_name) &&
            !empty($user->email) &&
            $user->userPictures()->exists();

        if ($isProfileCompleted) {
            return response()->json([
                'message' => 'Profile already completed.'
            ], 409);
        }
        $validatedData = $request->validate([
            'height' => 'nullable|string|max:20',
            'pets' => 'nullable|string|max:20',
            'children' => 'nullable|string|max:40',
            'politics' => 'nullable|string|max:20',
            'faith_identity' => 'nullable|string|max:60',
            'education' => 'nullable|string|max:40',
            'body_type' => 'nullable|string|max:20',
            'exercise' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        \Log::info($validatedData);
        $user->update($validatedData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('assets/images/upload/profile'), $fileName);
                $user->userPictures()->create([
                    'picture_path' => 'assets/images/upload/profile/' . $fileName,
                ]);
            }
        }

        return response()->json(['message' => 'Registration completed successfully.']);
    }
}
