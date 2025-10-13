<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosition;
use Illuminate\Http\Request;

class JobManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPosition::with('user');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('company_name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%");
            });
        }

        // Filter by job type
        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        // Filter by employment type
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // Filter by work modality
        if ($request->filled('work_modality')) {
            $query->where('work_modality', $request->work_modality);
        }

        // Salary range
        if ($request->filled('min_salary')) {
            $query->where('job_salary', '>=', $request->min_salary);
        }
        if ($request->filled('max_salary')) {
            $query->where('job_salary', '<=', $request->max_salary);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $jobs = $query->paginate(20)->appends($request->except('page'));

        // Get filter options
        $jobTypes = JobPosition::distinct()->whereNotNull('job_type')->pluck('job_type')->filter();
        $employmentTypes = JobPosition::distinct()->whereNotNull('employment_type')->pluck('employment_type')->filter();
        
        return view('admin.jobs.index', compact('jobs', 'jobTypes', 'employmentTypes'));
    }

    public function show(JobPosition $job)
    {
        $job->load('user', 'jobResponsibilities', 'jobQualifications', 'favorites', 'applications');
        return view('admin.jobs.show', compact('job'));
    }

    public function edit(JobPosition $job)
    {
        return view('admin.jobs.edit', compact('job'));
    }

    public function update(Request $request, JobPosition $job)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'job_salary' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:500',
            'job_type' => 'nullable|string',
            'employment_type' => 'nullable|string',
            'work_modality' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        $job->update($validated);

        return redirect()->route('admin.jobs.show', $job)
            ->with('success', 'Job updated successfully');
    }

    public function destroy(JobPosition $job)
    {
        $job->delete();

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Job deleted successfully');
    }
}

