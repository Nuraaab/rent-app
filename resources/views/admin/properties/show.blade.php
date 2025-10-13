@extends('admin.layouts.app')

@section('title', 'Property Details')
@section('page-title', 'Property Details')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <!-- Property Info -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h3 class="mb-2">{{ $property->title }}</h3>
                        <p class="text-muted mb-0">
                            <i class="bi bi-geo-alt"></i> {{ $property->address }}
                        </p>
                    </div>
                    <span class="badge bg-success fs-5">${{ number_format($property->price) }}/month</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="bi bi-door-open fs-3 text-primary"></i>
                            <div class="fw-semibold mt-2">{{ $property->number_of_bedrooms ?? 0 }} Beds</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="bi bi-droplet fs-3 text-info"></i>
                            <div class="fw-semibold mt-2">{{ $property->number_of_baths ?? 0 }} Baths</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="bi bi-people fs-3 text-success"></i>
                            <div class="fw-semibold mt-2">{{ $property->max_number_of_gusts ?? 0 }} Guests</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="bi bi-heart fs-3 text-danger"></i>
                            <div class="fw-semibold mt-2">{{ $property->favorites->count() }} Likes</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-3">Description</h6>
                    <p class="text-muted">{{ $property->description }}</p>
                </div>

                @if($property->houseGallery && $property->houseGallery->count() > 0)
                <div>
                    <h6 class="fw-semibold mb-3">Gallery ({{ $property->houseGallery->count() }} photos)</h6>
                    <div class="row g-2">
                        @foreach($property->houseGallery->take(6) as $image)
                        <div class="col-md-4">
                            <img src="{{ $image->gallery_path }}" class="img-fluid rounded" alt="Property Image">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Owner Info -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">Property Owner</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar bg-primary text-white rounded-circle mx-auto mb-2" 
                         style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 600;">
                        {{ strtoupper(substr($property->user->first_name ?? $property->user->email, 0, 1)) }}
                    </div>
                    <h6 class="mb-0">{{ $property->user->first_name }} {{ $property->user->last_name }}</h6>
                    <small class="text-muted">{{ $property->user->email }}</small>
                </div>
                <a href="{{ route('admin.users.show', $property->user) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-person me-2"></i>View Profile
                </a>
            </div>
        </div>

        <!-- Property Details -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold">Property Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Category</label>
                    <p class="mb-0 text-capitalize">{{ $property->category ?? 'Not specified' }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Listing Type</label>
                    <p class="mb-0 text-capitalize">{{ $property->listing_type ?? 'Not specified' }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Posted On</label>
                    <p class="mb-0">{{ $property->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <label class="text-muted small">Last Updated</label>
                    <p class="mb-0">{{ $property->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-body d-grid gap-2">
                <a href="{{ route('admin.properties.edit', $property) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Property
                </a>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePropertyModal">
                    <i class="bi bi-trash me-2"></i>Delete Property
                </button>
                <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deletePropertyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong>{{ $property->title }}</strong>?
                This will also delete all associated data. This action cannot be undone.
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
@endsection

