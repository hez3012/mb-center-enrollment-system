@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')

<h5 class="fw-bold mb-4">Dashboard</h5>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="card-body">
                <i class="bi bi-people fs-2 text-primary"></i>
                <h3 class="fw-bold mt-2 mb-0">{{ $totalUsers }}</h3>
                <small class="text-muted">Total Users</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="card-body">
                <i class="bi bi-person-heart fs-2 text-success"></i>
                <h3 class="fw-bold mt-2 mb-0">{{ $totalGuardians }}</h3>
                <small class="text-muted">Total Guardians</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="card-body">
                <i class="bi bi-mortarboard fs-2 text-warning"></i>
                <h3 class="fw-bold mt-2 mb-0">{{ $totalStudents }}</h3>
                <small class="text-muted">Total Students</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="card-body">
                <i class="bi bi-check-circle fs-2 text-info"></i>
                <h3 class="fw-bold mt-2 mb-0">{{ $activeStudents }}</h3>
                <small class="text-muted">Active Students</small>
            </div>
        </div>
    </div>
</div>
@endsection