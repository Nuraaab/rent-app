@extends('admin.layouts.app')

@section('title', 'Job Openings Management')
@section('page-title', 'Job Openings Management')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-briefcase me-2"></i>All Job Openings
                </h5>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary">{{ $jobs->total() }} Total</span>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Search and Filter -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search jobs..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="job_type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($jobTypes as $type)
                            <option value="{{ $type }}" {{ request('job_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="employment_type" class="form-select">
                        <option value="">Employment</option>
                        @foreach($employmentTypes as $type)
                            <option value="{{ $type }}" {{ request('employment_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="work_modality" class="form-select">
                        <option value="">Modality</option>
                        <option value="Remote" {{ request('work_modality') == 'Remote' ? 'selected' : '' }}>Remote</option>
                        <option value="Onsite" {{ request('work_modality') == 'Onsite' ? 'selected' : '' }}>Onsite</option>
                        <option value="Hybrid" {{ request('work_modality') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Jobs Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Salary</th>
                        <th>Type</th>
                        <th>Modality</th>
                        <th>Posted</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                    <tr>
                        <td><strong>#{{ $job->id }}</strong></td>
                        <td>
                            <div class="fw-semibold">{{ Str::limit($job->title, 40) }}</div>
                            <small class="text-muted">
                                <i class="bi bi-geo-alt"></i> {{ $job->location ?? 'Not specified' }}
                            </small>
                        </td>
                        <td>{{ $job->company_name ?? $job->client ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-success">${{ number_format($job->job_salary ?? 0) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-info text-capitalize">{{ $job->employment_type ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $job->work_modality == 'Remote' ? 'primary' : 'secondary' }}">
                                {{ $job->work_modality ?? 'N/A' }}
                            </span>
                        </td>
                        <td><small>{{ $job->created_at->format('M d, Y') }}</small></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Delete" 
                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $job->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal{{ $job->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete <strong>{{ $job->title }}</strong>?
                                    This action cannot be undone.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete Job</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No jobs found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $jobs->firstItem() ?? 0 }} to {{ $jobs->lastItem() ?? 0 }} of {{ $jobs->total() }} jobs
            </div>
            <div>
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

