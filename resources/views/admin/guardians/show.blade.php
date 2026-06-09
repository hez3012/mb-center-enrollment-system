@extends('admin.layouts.app')
@section('title', 'Guardian Profile')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Guardian Profile</h5>
    <a href="{{ route('admin.guardians.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person-heart me-1"></i>Guardian Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Full Name</td>
                        <td>{{ $guardian->full_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">M.I.</td>
                        <td>{{ $guardian->middle_initial ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Relationship</td>
                        <td>{{ $guardian->relationship }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #</td>
                        <td>{{ $guardian->contact_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Date Created</td>
                        <td>{{ $guardian->created_at?->format('m/d/Y h:i A') ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-shield me-1"></i>Account Details
            </div>
            <div class="card-body">
                @if($guardian->user)
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Username</td>
                        <td>{{ $guardian->user->username }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $guardian->user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge bg-{{ $guardian->user->is_active ? 'success' : 'secondary' }}">
                                {{ $guardian->user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #1</td>
                        <td>{{ $guardian->user->contact_number_1 ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #2</td>
                        <td>{{ $guardian->user->contact_number_2 ?: '—' }}</td>
                    </tr>
                </table>
                @else
                <p class="text-muted mb-0">No user account linked.</p>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-people me-1"></i>Linked Students ({{ $guardian->students->count() }})
            </div>
            <div class="card-body p-0">
                @forelse($guardian->students as $student)
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <div>
                        <div class="fw-semibold small">{{ $student->list_name }}</div>
                        <div class="text-muted small">{{ $student->programLevel?->program_name ?? '—' }}</div>
                    </div>
                    <span class="badge bg-{{ $student->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($student->status) }}
                    </span>
                </div>
                @empty
                <p class="text-muted small px-3 py-2 mb-0">No students linked.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection