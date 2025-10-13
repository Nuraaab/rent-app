@extends('admin.layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">My Profile</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar bg-primary text-white rounded-circle mx-auto mb-3" 
                         style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 600;">
                        {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->email, 0, 1)) }}
                    </div>
                    <h4>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h4>
                    <p class="text-muted">{{ Auth::user()->email }}</p>
                    <span class="badge bg-danger">Administrator</span>
                </div>

                <hr>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Email</label>
                        <p class="mb-0">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Phone</label>
                        <p class="mb-0">{{ Auth::user()->phone_number ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Last Login</label>
                        <p class="mb-0">{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Member Since</label>
                        <p class="mb-0">{{ Auth::user()->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

