@extends('portal.layouts.app')
@section('title', 'Dashboard')
@section('content')

<h5 class="fw-bold mb-4">Welcome, {{ Auth::user()->first_name }}!</h5>

@if($guardian)
<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="card-body">
                <i class="bi bi-mortarboard fs-2 text-primary"></i>
                <h3 class="fw-bold mt-2 mb-0">{{ $students->count() }}</h3>
                <small class="text-muted">Linked Students</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="card-body">
                <i class="bi bi-check-circle fs-2 text-success"></i>
                <h3 class="fw-bold mt-2 mb-0">
                    {{ $students->where('status', 'active')->count() }}
                </h3>
                <small class="text-muted">Active Students</small>
            </div>
        </div>
    </div>
</div>

@if($students->count() > 0)
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-people me-1"></i>My Students
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Full Name</th>
                    <th>Service Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td>{{ $student->list_name }}</td>
                    <td>
                        @if($student->serviceType)
                            <span class="badge bg-info text-dark">
                                {{ $student->serviceType->service_name }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $sc = [
                                'active'    => 'success',
                                'inactive'  => 'secondary',
                                'withdrawn' => 'warning',
                                'completed' => 'primary',
                            ];
                        @endphp
                        <span class="badge bg-{{ $sc[$student->status] ?? 'secondary' }}">
                            {{ ucfirst($student->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-1"></i>
    Your guardian profile is not fully set up yet.
    Please contact the administrator.
</div>
@endif
@endsection