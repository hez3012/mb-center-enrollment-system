@extends('admin.layouts.app')
@section('title', 'View User')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">User Profile</h5>
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person me-1"></i>Personal Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Full Name</td>
                        <td>{{ $user->full_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">M.I.</td>
                        <td>{{ $user->middle_initial ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Birthdate</td>
                        <td>{{ $user->birthdate ? $user->birthdate->format('m/d/Y') : '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Age</td>
                        <td>{{ $user->age !== null ? $user->age . ' years old' : '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #1</td>
                        <td>{{ $user->contact_number_1 ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #2</td>
                        <td>{{ $user->contact_number_2 ?: '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-shield me-1"></i>Account Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Username</td>
                        <td>{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Role</td>
                        <td>
                            <span class="badge bg-primary">{{ ucfirst($user->role?->role_name) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Date Created</td>
                        <td>{{ $user->created_at?->format('m/d/Y h:i A') ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-geo-alt me-1"></i>Address
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Region</td>
                        <td>{{ $user->region ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Province</td>
                        <td>{{ $user->province ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">City</td>
                        <td>{{ $user->city ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Barangay</td>
                        <td>{{ $user->barangay ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">House / Unit No.</td>
                        <td>{{ $user->house_unit_no ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Street</td>
                        <td>{{ $user->street ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ZIP Code</td>
                        <td>{{ $user->zip_code ?: '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if($user->guardian)
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-person-heart me-1"></i>Guardian Profile
    </div>
    <div class="card-body">
        <table class="table table-sm mb-0">
            <tr>
                <td class="text-muted" style="width:25%">Relationship</td>
                <td>{{ $user->guardian->relationship }}</td>
            </tr>
            <tr>
                <td class="text-muted">Contact</td>
                <td>{{ $user->guardian->contact_number }}</td>
            </tr>
            <tr>
                <td class="text-muted">Linked Students</td>
                <td>{{ $user->guardian->students->count() }} student(s)</td>
            </tr>
        </table>
    </div>
</div>
@endif
@endsection