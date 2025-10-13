<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationsReservation;
use Illuminate\Http\Request;

class ApplicationManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = ApplicationsReservation::with('user', 'rental', 'jobPosition');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $applications = $query->paginate(20)->appends($request->except('page'));
        
        return view('admin.applications.index', compact('applications'));
    }

    public function show(ApplicationsReservation $application)
    {
        $application->load('user', 'rental', 'jobPosition');
        return view('admin.applications.show', compact('application'));
    }

    public function updateStatus(ApplicationsReservation $application, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,cancelled'
        ]);

        $application->update(['status' => $request->status]);

        return back()->with('success', 'Application status updated');
    }

    public function destroy(ApplicationsReservation $application)
    {
        $application->delete();

        return redirect()->route('admin.applications.index')
            ->with('success', 'Application deleted successfully');
    }
}

