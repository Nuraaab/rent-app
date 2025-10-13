@extends('admin.layouts.app')

@section('title', 'Properties Management')
@section('page-title', 'Properties Management')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-house-door me-2"></i>All Properties
                </h5>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary">{{ $properties->total() }} Total</span>
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
                        <input type="text" name="search" class="form-control" placeholder="Search properties..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="listing_type" class="form-select">
                        <option value="">Listing Type</option>
                        <option value="rent" {{ request('listing_type') == 'rent' ? 'selected' : '' }}>For Rent</option>
                        <option value="sale" {{ request('listing_type') == 'sale' ? 'selected' : '' }}>For Sale</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Properties Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Property</th>
                        <th>Owner</th>
                        <th>Price</th>
                        <th>Type</th>
                        <th>Posted</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($properties as $property)
                    <tr>
                        <td><strong>#{{ $property->id }}</strong></td>
                        <td>
                            <div class="fw-semibold">{{ Str::limit($property->title, 40) }}</div>
                            <small class="text-muted">
                                <i class="bi bi-geo-alt"></i> {{ Str::limit($property->address, 30) }}
                            </small>
                        </td>
                        <td>
                            <div>{{ $property->user->first_name ?? 'N/A' }} {{ $property->user->last_name ?? '' }}</div>
                            <small class="text-muted">{{ $property->user->email }}</small>
                        </td>
                        <td>
                            <span class="badge bg-success">${{ number_format($property->price) }}</span>
                            <small class="text-muted d-block">{{ $property->listing_type == 'rent' ? '/month' : '' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info text-capitalize">{{ $property->listing_type ?? 'N/A' }}</span>
                        </td>
                        <td><small>{{ $property->created_at->format('M d, Y') }}</small></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.properties.show', $property) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.properties.edit', $property) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Delete" 
                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $property->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal{{ $property->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete <strong>{{ $property->title }}</strong>?
                                    This action cannot be undone.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.properties.destroy', $property) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete Property</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No properties found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $properties->firstItem() ?? 0 }} to {{ $properties->lastItem() ?? 0 }} of {{ $properties->total() }} properties
            </div>
            <div>
                {{ $properties->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

