@extends('admin.layouts.app')

@section('title', 'Edit Job')
@section('page-title', 'Edit Job')

@section('content')
<div class="row">
    <div class="col-xl-9 col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-briefcase me-2"></i>Edit Job Opening
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.jobs.update', $job) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Title</label>
                            <input
                                type="text"
                                name="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $job->title) }}"
                                required
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Salary</label>
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                name="job_salary"
                                class="form-control @error('job_salary') is-invalid @enderror"
                                value="{{ old('job_salary', $job->job_salary) }}"
                            >
                            @error('job_salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company Name</label>
                            <input
                                type="text"
                                name="company_name"
                                class="form-control @error('company_name') is-invalid @enderror"
                                value="{{ old('company_name', $job->company_name) }}"
                            >
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Location</label>
                            <input
                                type="text"
                                name="location"
                                class="form-control @error('location') is-invalid @enderror"
                                value="{{ old('location', $job->location) }}"
                            >
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Job Type</label>
                            <input
                                type="text"
                                name="job_type"
                                class="form-control @error('job_type') is-invalid @enderror"
                                value="{{ old('job_type', $job->job_type) }}"
                                placeholder="Full-time, Part-time..."
                            >
                            @error('job_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Employment Type</label>
                            <input
                                type="text"
                                name="employment_type"
                                class="form-control @error('employment_type') is-invalid @enderror"
                                value="{{ old('employment_type', $job->employment_type) }}"
                                placeholder="Permanent, Contract..."
                            >
                            @error('employment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Work Modality</label>
                            <select name="work_modality" class="form-select @error('work_modality') is-invalid @enderror">
                                <option value="">Select modality</option>
                                <option value="Remote" {{ old('work_modality', $job->work_modality) == 'Remote' ? 'selected' : '' }}>Remote</option>
                                <option value="Onsite" {{ old('work_modality', $job->work_modality) == 'Onsite' ? 'selected' : '' }}>Onsite</option>
                                <option value="Hybrid" {{ old('work_modality', $job->work_modality) == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                            </select>
                            @error('work_modality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Deadline</label>
                            <input
                                type="date"
                                name="deadline"
                                class="form-control @error('deadline') is-invalid @enderror"
                                value="{{ old('deadline', $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('Y-m-d') : '') }}"
                            >
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea
                                name="description"
                                rows="5"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Write job description"
                            >{{ old('description', $job->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2 me-2"></i>Update Job
                        </button>
                        <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

