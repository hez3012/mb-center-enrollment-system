@extends('admin.layouts.app')
@section('title', 'User Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">User Details</h5>
    <div class="d-flex gap-2">
        @if(Auth::user()->hasPermission('edit_user'))
            <a href="{{ route('admin.users.edit',$user->user_id) }}"
               class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        @endif
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                @include('partials.avatar',[
                    'name'  => $user->list_name,
                    'image' => $user->profile_picture,
                    'size'  => 80,
                ])
                <div class="fw-bold mt-3">{{ $user->list_name }}</div>
                <div class="text-muted small">{{ $user->username }}</div>
                @php $roleColors=['directress'=>'danger','admin'=>'primary','teacher'=>'success','staff'=>'info','guardian'=>'secondary']; @endphp
                <span class="badge bg-{{ $roleColors[$user->role?->role_name] ?? 'secondary' }} mt-1">
                    {{ ucfirst($user->role?->role_name) }}
                </span>
                <br>
                <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }} mt-1">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person me-1"></i>Personal Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:35%">Full Name</td>
                        <td>{{ $user->full_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Birthdate</td>
                        <td>{{ $user->birthdate?->format('m/d/Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Age</td>
                        <td>{{ $user->age !== null ? $user->age . ' years old' : '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #1</td>
                        <td>{{ $user->contact_number_1 ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #2</td>
                        <td>{{ $user->contact_number_2 ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Address</td>
                        <td>{{ $user->full_address ?? '—' }}</td>
                    </tr>
                    @if($user->role?->role_name === 'guardian' && $user->guardian)
                    <tr>
                        <td class="text-muted">Relationship</td>
                        <td>{{ $user->guardian->relationship ?? '—' }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-shield me-1"></i>Permissions
            </div>
            <div class="card-body">
                @if($user->permissions->isEmpty())
                    <p class="text-muted small mb-0">No permissions assigned.</p>
                @else
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($user->permissions as $perm)
                            <span class="badge bg-light text-dark border">
                                {{ $perm->permission_name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection