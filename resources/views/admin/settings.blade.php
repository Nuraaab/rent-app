@extends('admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">Application Settings</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Settings page - Coming soon
                </div>
                <p class="text-muted">This page will allow you to configure:</p>
                <ul class="text-muted">
                    <li>Application name and logo</li>
                    <li>Email templates</li>
                    <li>Notification preferences</li>
                    <li>API configurations</li>
                    <li>And more...</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

