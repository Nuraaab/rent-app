@extends('admin.layouts.app')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('content')
<div class="row g-4">
    <!-- User Info Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar bg-primary text-white rounded-circle mx-auto mb-3" 
                     style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 600;">
                    {{ strtoupper(substr($user->first_name ?? $user->email, 0, 1)) }}
                </div>
                <h4 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h4>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                
                @if($user->is_admin)
                    <span class="badge bg-danger mb-3">Administrator</span>
                @else
                    <span class="badge bg-secondary mb-3">Regular User</span>
                @endif

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </a>
                    <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-outline-warning w-100">
                            <i class="bi bi-shield me-2"></i>{{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Properties Posted</span>
                    <strong>{{ $user->rentals->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Jobs Posted</span>
                    <strong>{{ $user->jobPositions->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Favorites</span>
                    <strong>{{ $user->favorites->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Applications</span>
                    <strong>{{ $user->applications->count() }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Details & Activities -->
    <div class="col-lg-8">
        <!-- User Details -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">User Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Email</label>
                        <p class="mb-0">{{ $user->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Phone Number</label>
                        <p class="mb-0">{{ $user->phone_number ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Firebase UID</label>
                        <p class="mb-0"><code>{{ $user->firebase_uid ?? 'N/A' }}</code></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Account Status</label>
                        <p class="mb-0">
                            @if($user->email_verified_at)
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-warning">Unverified</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Joined</label>
                        <p class="mb-0">{{ $user->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Last Login</label>
                        <p class="mb-0">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Properties Posted -->
        @if($user->rentals->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">Properties Posted ({{ $user->rentals->count() }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Posted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->rentals->take(5) as $property)
                            <tr>
                                <td>{{ Str::limit($property->title, 40) }}</td>
                                <td>${{ number_format($property->price) }}</td>
                                <td><span class="badge bg-{{ $property->status == 'active' ? 'success' : 'warning' }}">{{ $property->status }}</span></td>
                                <td><small class="text-muted">{{ $property->created_at->diffForHumans() }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Jobs Posted -->
        @if($user->jobPositions->count() > 0)
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">Job Openings Posted ({{ $user->jobPositions->count() }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Salary</th>
                                <th>Type</th>
                                <th>Posted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->jobPositions->take(5) as $job)
                            <tr>
                                <td>{{ Str::limit($job->title, 40) }}</td>
                                <td>${{ number_format($job->job_salary) }}</td>
                                <td><span class="badge bg-info">{{ $job->employment_type }}</span></td>
                                <td><small class="text-muted">{{ $job->created_at->diffForHumans() }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Users
    </a>
</div>
@endsection

