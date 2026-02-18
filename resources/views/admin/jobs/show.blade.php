@extends('admin.layouts.app')

@section('title', 'Job Details')
@section('page-title', 'Job Details')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h3 class="mb-2">{{ $job->title }}</h3>
                        <p class="text-muted mb-0">
                            <i class="bi bi-buildings"></i> {{ $job->company_name ?? $job->client ?? 'N/A' }}
                        </p>
                    </div>
                    <span class="badge bg-success fs-6">
                        ${{ number_format((float) str_replace(',', '', (string) ($job->job_salary ?? 0))) }}
                    </span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="bi bi-briefcase fs-3 text-primary"></i>
                            <div class="fw-semibold mt-2">{{ $job->job_type ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="bi bi-person-badge fs-3 text-info"></i>
                            <div class="fw-semibold mt-2">{{ $job->employment_type ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="bi bi-heart fs-3 text-danger"></i>
                            <div class="fw-semibold mt-2">{{ $job->favorites->count() }} Favorites</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-2">Description</h6>
                    <p class="text-muted mb-0">{{ $job->description ?: 'No description provided.' }}</p>
                </div>

                @if($job->jobResponsibilities->count() > 0)
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">Responsibilities</h6>
                        <ul class="mb-0">
                            @foreach($job->jobResponsibilities as $responsibility)
                                <li>{{ $responsibility->responsiblity }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($job->jobQualifications->count() > 0)
                    <div>
                        <h6 class="fw-semibold mb-2">Qualifications</h6>
                        <ul class="mb-0">
                            @foreach($job->jobQualifications as $qualification)
                                <li>{{ $qualification->qualification }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">Posted By</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar bg-primary text-white rounded-circle mx-auto mb-2"
                         style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 600;">
                        {{ strtoupper(substr($job->user->first_name ?? $job->user->email, 0, 1)) }}
                    </div>
                    <h6 class="mb-0">{{ $job->user->first_name }} {{ $job->user->last_name }}</h6>
                    <small class="text-muted">{{ $job->user->email }}</small>
                </div>
                <a href="{{ route('admin.users.show', $job->user) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-person me-2"></i>View User
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">Job Meta</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Work Modality</label>
                    <p class="mb-0">{{ $job->work_modality ?? 'N/A' }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Deadline</label>
                    <p class="mb-0">{{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('M d, Y') : 'N/A' }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Applications</label>
                    <p class="mb-0">{{ $job->applications->count() }}</p>
                </div>
                <div>
                    <label class="text-muted small">Posted On</label>
                    <p class="mb-0">{{ $job->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body d-grid gap-2">
                <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Job
                </a>
                <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Delete this job?')">
                        <i class="bi bi-trash me-2"></i>Delete Job
                    </button>
                </form>
                <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

