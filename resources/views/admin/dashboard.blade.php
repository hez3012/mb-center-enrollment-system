@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
    <h5 class="fw-bold">Dashboard</h5>
    <p class="text-muted">Welcome back, {{ Auth::user()->full_name }}!</p>
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-1"></i>
        Phase 1 and Phase 3 complete — Authentication and User Management working! ✅
    </div>
@endsection