@extends('admin.layouts.app')

@section('title', 'Edit Property')
@section('page-title', 'Edit Property')

@section('content')
<div class="row">
    <div class="col-xl-9 col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-house-gear me-2"></i>Edit Property
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.properties.update', $property) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Title</label>
                            <input
                                type="text"
                                name="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $property->title) }}"
                                required
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Price</label>
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                name="price"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price', $property->price) }}"
                                required
                            >
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category</label>
                            <input
                                type="text"
                                name="category"
                                class="form-control @error('category') is-invalid @enderror"
                                value="{{ old('category', $property->category) }}"
                                placeholder="Apartment, Condo, Villa..."
                            >
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Listing Type</label>
                            <select name="listing_type" class="form-select @error('listing_type') is-invalid @enderror">
                                <option value="">Select type</option>
                                <option value="rent" {{ old('listing_type', $property->listing_type) == 'rent' ? 'selected' : '' }}>For Rent</option>
                                <option value="sale" {{ old('listing_type', $property->listing_type) == 'sale' ? 'selected' : '' }}>For Sale</option>
                            </select>
                            @error('listing_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <input
                                type="text"
                                name="address"
                                class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address', $property->address) }}"
                                required
                            >
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bedrooms</label>
                            <input
                                type="number"
                                min="0"
                                name="number_of_bedrooms"
                                class="form-control @error('number_of_bedrooms') is-invalid @enderror"
                                value="{{ old('number_of_bedrooms', $property->number_of_bedrooms) }}"
                            >
                            @error('number_of_bedrooms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bathrooms</label>
                            <input
                                type="number"
                                min="0"
                                name="number_of_baths"
                                class="form-control @error('number_of_baths') is-invalid @enderror"
                                value="{{ old('number_of_baths', $property->number_of_baths) }}"
                            >
                            @error('number_of_baths')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Max Guests</label>
                            <input
                                type="number"
                                min="0"
                                name="max_number_of_gusts"
                                class="form-control @error('max_number_of_gusts') is-invalid @enderror"
                                value="{{ old('max_number_of_gusts', $property->max_number_of_gusts) }}"
                            >
                            @error('max_number_of_gusts')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea
                                name="description"
                                rows="5"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Write a short property description"
                            >{{ old('description', $property->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2 me-2"></i>Update Property
                        </button>
                        <a href="{{ route('admin.properties.show', $property) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

