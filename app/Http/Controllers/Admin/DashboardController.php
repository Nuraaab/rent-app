<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rental;
use App\Models\JobPosition;
use App\Models\ApplicationsReservation;
use App\Models\Favorite;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $stats = [
            'total_users' => User::count(),
            'total_properties' => Rental::count(),
            'total_jobs' => JobPosition::count(),
            'total_applications' => ApplicationsReservation::count(),
            'active_users' => User::where('updated_at', '>=', now()->subDays(7))->count(),
            'new_properties' => Rental::where('created_at', '>=', now()->subDays(7))->count(),
            'total_favorites' => Favorite::count(),
        ];

        // Recent users
        $recent_users = User::latest()->take(5)->get();

        // Recent properties
        $recent_properties = Rental::with('user')->latest()->take(5)->get();

        // Recent jobs
        $recent_jobs = JobPosition::latest()->take(5)->get();

        // Charts data - Users growth (last 7 days)
        $user_growth = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Property types distribution
        $property_types = Rental::select('category', DB::raw('COUNT(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->get();

        // Job types distribution
        $job_types = JobPosition::select('job_type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('job_type')
            ->groupBy('job_type')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_users',
            'recent_properties',
            'recent_jobs',
            'user_growth',
            'property_types',
            'job_types'
        ));
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function settings()
    {
        return view('admin.settings');
    }
}

