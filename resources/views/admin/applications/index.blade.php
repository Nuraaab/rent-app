@extends('admin.layouts.app')

@section('title', 'Applications Management')
@section('page-title', 'Applications Management')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-file-earmark-text me-2"></i>All Applications
                </h5>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary">{{ $applications->total() }} Total</span>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Search and Filter -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by applicant name..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="property" {{ request('type') == 'property' ? 'selected' : '' }}>Property</option>
                        <option value="job" {{ request('type') == 'job' ? 'selected' : '' }}>Job</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Applications Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Applicant</th>
                        <th>For</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                    <tr>
                        <td><strong>#{{ $application->id }}</strong></td>
                        <td>
                            <div class="fw-semibold">{{ $application->user->first_name }} {{ $application->user->last_name }}</div>
                            <small class="text-muted">{{ $application->user->email }}</small>
                        </td>
                        <td>
                            @if($application->rental)
                                <div class="fw-semibold">{{ Str::limit($application->rental->title, 30) }}</div>
                                <small class="text-muted">Property</small>
                            @elseif($application->jobPosition)
                                <div class="fw-semibold">{{ Str::limit($application->jobPosition->title, 30) }}</div>
                                <small class="text-muted">Job</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($application->rental_id)
                                <span class="badge bg-warning">Property</span>
                            @else
                                <span class="badge bg-info">Job</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ 
                                $application->status == 'approved' ? 'success' : 
                                ($application->status == 'rejected' ? 'danger' : 
                                ($application->status == 'cancelled' ? 'secondary' : 'warning')) 
                            }}">
                                {{ ucfirst($application->status ?? 'Pending') }}
                            </span>
                        </td>
                        <td><small>{{ $application->created_at->format('M d, Y') }}</small></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.applications.show', $application) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Delete" 
                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $application->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal{{ $application->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete this application?
                                    This action cannot be undone.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.applications.destroy', $application) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete Application</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No applications found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} applications
            </div>
            <div>
                {{ $applications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

