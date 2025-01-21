<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApplicationsReservation;
class ApplicationsReservationController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'job_position_id' => 'nullable|exists:job_positions,id',
            'rental_id' => 'nullable|exists:rentals,id',
            'notes' => 'nullable|string',
            'cv' => 'nullable|string',
            'application_letter' => 'nullable',
            'github_link' => 'nullable|string',
            'linkedin_link' => 'nullable|string',
            'portfolio_link' => 'nullable|string'
        ]);

        $application = ApplicationsReservation::create([
            'user_id' => auth()->id(),
            'job_position_id' => $request->job_position_id,
            'rental_id' => $request->rental_id,
            'notes' => $request->notes,
            'applied_at' => now(),
        ]);

        return response()->json(['message' => 'Action recorded successfully', 'data' => $application]);
    }

    public function index()
    {
        $applications = ApplicationsReservation::where('user_id', auth()->id())
            ->with(['jobPosition', 'rental'])
            ->get();
        return response()->json($applications);
    }
    public function appForMe(){
        $userId = auth()->id();
        $applications = ApplicationsReservation::with(['jobPosition', 'rental'])
            ->whereHas('jobPosition', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orWhereHas('rental', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();
            return response()->json($applications);
    }
}
