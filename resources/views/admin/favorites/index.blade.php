@extends('admin.layouts.app')

@section('title', 'Favorites Management')
@section('page-title', 'Favorites Management')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-heart me-2"></i>All Favorites
                </h5>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary">{{ $favorites->total() }} Total</span>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Search and Filter -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by user..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="property" {{ request('type') == 'property' ? 'selected' : '' }}>Properties</option>
                        <option value="job" {{ request('type') == 'job' ? 'selected' : '' }}>Jobs</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.favorites.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Favorites Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Favorite Item</th>
                        <th>Type</th>
                        <th>Added</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($favorites as $favorite)
                    <tr>
                        <td><strong>#{{ $favorite->id }}</strong></td>
                        <td>
                            <div class="fw-semibold">{{ $favorite->user->first_name }} {{ $favorite->user->last_name }}</div>
                            <small class="text-muted">{{ $favorite->user->email }}</small>
                        </td>
                        <td>
                            @if($favorite->rental)
                                <div class="fw-semibold">{{ Str::limit($favorite->rental->title, 40) }}</div>
                                <small class="text-muted">${{ number_format($favorite->rental->price) }}</small>
                            @elseif($favorite->jobPosition)
                                <div class="fw-semibold">{{ Str::limit($favorite->jobPosition->title, 40) }}</div>
                                <small class="text-muted">{{ $favorite->jobPosition->company_name }}</small>
                            @else
                                <span class="text-muted">Deleted Item</span>
                            @endif
                        </td>
                        <td>
                            @if($favorite->rental_id)
                                <span class="badge bg-warning">Property</span>
                            @else
                                <span class="badge bg-info">Job</span>
                            @endif
                        </td>
                        <td><small>{{ $favorite->created_at->diffForHumans() }}</small></td>
                        <td class="text-end">
                            <form action="{{ route('admin.favorites.destroy', $favorite) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                        onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No favorites found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $favorites->firstItem() ?? 0 }} to {{ $favorites->lastItem() ?? 0 }} of {{ $favorites->total() }} favorites
            </div>
            <div>
                {{ $favorites->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

