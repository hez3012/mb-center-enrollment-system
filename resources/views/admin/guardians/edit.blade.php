@extends('admin.layouts.app')
@section('title', 'Edit Guardian Profile')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Edit Guardian — {{ $guardian->list_name }}</h5>
    <a href="{{ route('admin.guardians.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="alert alert-info small">
    <i class="bi bi-info-circle me-1"></i>
    To update the guardian's name, email, username, or password, go to
    <a href="{{ route('admin.users.edit', $guardian->user_id) }}" class="alert-link">User Management</a>.
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.guardians.update', $guardian->guardian_id) }}">
            @csrf @method('PUT')

            {{-- Personal Information — Display Only --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-person me-1"></i>Personal Information
                <span class="text-muted fw-normal ms-1">(read-only — edit via User Management)</span>
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">First Name</label>
                    <input type="text" class="form-control bg-light" readonly
                           value="{{ $guardian->first_name }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Middle Name <span class="text-muted small fw-normal">(editable)</span>
                    </label>
                    <input type="text" name="middle_name" id="middleNameInput"
                           class="form-control @error('middle_name') is-invalid @enderror"
                           value="{{ old('middle_name', $guardian->middle_name) }}">
                    @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Last Name</label>
                    <input type="text" class="form-control bg-light" readonly
                           value="{{ $guardian->last_name }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">M.I.</label>
                    <input type="text" id="miDisplay" class="form-control bg-light" readonly
                           value="{{ $guardian->middle_initial }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Birthdate</label>
                    <input type="text" class="form-control bg-light" readonly
                           value="{{ $guardian->user?->birthdate?->format('m/d/Y') ?? '—' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Age</label>
                    <input type="text" class="form-control bg-light" readonly
                           value="{{ $guardian->user?->age !== null ? $guardian->user->age . ' years old' : '—' }}">
                </div>
            </div>

            {{-- Editable Contact Information --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-telephone me-1"></i>Contact Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contact #1</label>
                    <input type="text" name="contact_number_1"
                           class="form-control @error('contact_number_1') is-invalid @enderror"
                           value="{{ old('contact_number_1', $guardian->user?->contact_number_1 ?? $guardian->contact_number) }}"
                           required>
                    @error('contact_number_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Contact #2 <span class="text-muted small fw-normal">(optional)</span>
                    </label>
                    <input type="text" name="contact_number_2"
                           class="form-control @error('contact_number_2') is-invalid @enderror"
                           value="{{ old('contact_number_2', $guardian->user?->contact_number_2) }}">
                    @error('contact_number_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Relationship to Student</label>
                    <select name="relationship"
                            class="form-select @error('relationship') is-invalid @enderror" required>
                        <option value="">-- Select --</option>
                        @foreach(['Mother','Father','Grandparent','Aunt/Uncle','Sibling','Legal Guardian','Other'] as $rel)
                            <option value="{{ $rel }}"
                                    {{ old('relationship', $guardian->relationship) == $rel ? 'selected' : '' }}>
                                {{ $rel }}
                            </option>
                        @endforeach
                    </select>
                    @error('relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Address — Editable --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-geo-alt me-1"></i>Address
            </p>
            <div class="mb-4">
                @include('partials.address-fields', [
                    'fieldPrefix' => '',
                    'data' => [
                        'region'        => old('region',        $guardian->user?->region        ?? ''),
                        'province'      => old('province',      $guardian->user?->province      ?? ''),
                        'city'          => old('city',          $guardian->user?->city          ?? ''),
                        'barangay'      => old('barangay',      $guardian->user?->barangay      ?? ''),
                        'house_unit_no' => old('house_unit_no', $guardian->user?->house_unit_no ?? ''),
                        'street'        => old('street',        $guardian->user?->street        ?? ''),
                        'zip_code'      => old('zip_code',      $guardian->user?->zip_code      ?? ''),
                    ],
                    'regions'   => $regions,
                    'provinces' => $provinces,
                    'cities'    => $cities,
                ])
            </div>

            {{-- Account Credentials — Display Only --}}
            <p class="fw-semibold text-primary small mb-2">
                <i class="bi bi-shield me-1"></i>Account Credentials
                <span class="text-muted fw-normal ms-1">(read-only — edit via User Management)</span>
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="text" class="form-control bg-light" readonly
                           value="{{ $guardian->user?->email ?? '—' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Username</label>
                    <input type="text" class="form-control bg-light" readonly
                           value="{{ $guardian->user?->username ?? '—' }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Save Changes
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('middleNameInput').addEventListener('input', function () {
    const mi = this.value.trim();
    document.getElementById('miDisplay').value = mi ? mi[0].toUpperCase() + '.' : '';
});
</script>
@endsection