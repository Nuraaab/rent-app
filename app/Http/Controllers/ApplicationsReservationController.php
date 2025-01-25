<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApplicationsReservation;
class ApplicationsReservationController extends Controller
{
    // public function create(Request $request)
    // {
    //     $request->validate([
    //         'job_position_id' => 'nullable|exists:job_positions,id',
    //         'rental_id' => 'nullable|exists:rentals,id',
    //         'notes' => 'nullable|string',
    //         'cv' => 'nullable|string',
    //         'application_letter' => 'nullable',
    //         'github_link' => 'nullable|string',
    //         'linkedin_link' => 'nullable|string',
    //         'portfolio_link' => 'nullable|string'
    //     ]);

    //     $application = ApplicationsReservation::create([
    //         'user_id' => auth()->id(),
    //         'job_position_id' => $request->job_position_id,
    //         'rental_id' => $request->rental_id,
    //         'notes' => $request->notes,
    //         'applied_at' => now(),
    //     ]);

    //     return response()->json(['message' => 'Action recorded successfully', 'data' => $application]);
    // }

    public function create(Request $request)
        {
            $request->validate([
                'job_position_id' => 'nullable|exists:job_positions,id',
                'rental_id' => 'nullable|exists:rentals,id',
                'notes' => 'nullable|string',
                'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Validate CV as a file
                'application_letter' => 'nullable|string',
                'github_link' => 'nullable|string',
                'linkedin_link' => 'nullable|string',
                'portfolio_link' => 'nullable|string'
            ]);

            // Handle CV file upload if provided
            $cvPath = null;
            if ($request->hasFile('cv')) {
                $cvFile = $request->file('cv');
                $cvPath = 'cvs/' . time() . '_' . $cvFile->getClientOriginalName();
                $cvFile->move(public_path('cvs'), $cvPath);
            }

            // Create the application record
            $application = ApplicationsReservation::create([
                'user_id' => auth()->id(),
                'job_position_id' => $request->job_position_id,
                'rental_id' => $request->rental_id,
                'notes' => $request->notes,
                'cv' => $cvPath, // Save the CV file path
                'application_letter' => $request->application_letter,
                'github_link' => $request->github_link,
                'linkedin_link' => $request->linkedin_link,
                'portfolio_link' => $request->portfolio_link,
                'applied_at' => now(),
            ]);

            return response()->json([
                'message' => 'Action recorded successfully',
                'data' => $application,
            ], 201);
        }


        public function index() //myApplication
        {
            $applications = ApplicationsReservation::where('user_id', auth()->id())
                ->with(['user','jobPosition', 'rental'])
                ->get();
            return response()->json($applications);
        }
        
        public function appForMe() //inbox
        {
            $userId = auth()->id();
            $applications = ApplicationsReservation::with(['user', 'jobPosition', 'rental'])
                ->whereHas('jobPosition', function ($query) use ($userId) {
                    $query->where('user_id', $userId); 
                })
                ->orWhereHas('rental', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->where('user_id', '!=', $userId)
                ->get();

            return response()->json($applications);
        }
        
}
