@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="row g-4 mb-4">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1">Total Users</p>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['total_users']) }}</h3>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> {{ $stats['active_users'] }} active
                    </small>
                </div>
                <div class="stats-icon" style="background-color: rgba(79, 70, 229, 0.1); color: #4F46E5;">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1">Total Properties</p>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['total_properties']) }}</h3>
                    <small class="text-success">
                        <i class="bi bi-plus-circle"></i> {{ $stats['new_properties'] }} new this week
                    </small>
                </div>
                <div class="stats-icon" style="background-color: rgba(255, 90, 95, 0.1); color: #FF5A5F;">
                    <i class="bi bi-house-door"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1">Job Openings</p>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['total_jobs']) }}</h3>
                    <small class="text-info">
                        <i class="bi bi-briefcase"></i> Active listings
                    </small>
                </div>
                <div class="stats-icon" style="background-color: rgba(16, 185, 129, 0.1); color: #10B981;">
                    <i class="bi bi-briefcase"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1">Applications</p>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['total_applications']) }}</h3>
                    <small class="text-primary">
                        <i class="bi bi-file-text"></i> Total received
                    </small>
                </div>
                <div class="stats-icon" style="background-color: rgba(59, 130, 246, 0.1); color: #3B82F6;">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">User Growth (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="userGrowthChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">Property Types</h5>
            </div>
            <div class="card-body">
                <canvas id="propertyTypesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Items -->
<div class="row g-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">Recent Users</h5>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                            {{ strtoupper(substr($user->first_name ?? $user->email, 0, 1)) }}
                                        </div>
                                        <span>{{ $user->first_name }} {{ $user->last_name }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">Recent Properties</h5>
                <a href="{{ route('admin.properties.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Posted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_properties as $property)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ Str::limit($property->title, 30) }}</div>
                                    <small class="text-muted">{{ $property->address }}</small>
                                </td>
                                <td><span class="badge bg-success">${{ number_format($property->price) }}</span></td>
                                <td><small class="text-muted">{{ $property->created_at->diffForHumans() }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart');
    const userGrowthData = @json($user_growth);
    
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: userGrowthData.map(item => new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
            datasets: [{
                label: 'New Users',
                data: userGrowthData.map(item => item.count),
                borderColor: '#FF5A5F',
                backgroundColor: 'rgba(255, 90, 95, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Property Types Chart
    const propertyTypesCtx = document.getElementById('propertyTypesChart');
    const propertyTypesData = @json($property_types);
    
    new Chart(propertyTypesCtx, {
        type: 'doughnut',
        data: {
            labels: propertyTypesData.map(item => item.category || 'Other'),
            datasets: [{
                data: propertyTypesData.map(item => item.count),
                backgroundColor: [
                    '#FF5A5F',
                    '#10B981',
                    '#3B82F6',
                    '#F59E0B',
                    '#8B5CF6',
                    '#EC4899'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection

