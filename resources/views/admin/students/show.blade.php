@extends('admin.layouts.app')
@section('title', 'Student Profile')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Student Profile</h5>
    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary">
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
                        <td>{{ $student->full_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">M.I.</td>
                        <td>{{ $student->middle_initial ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Birthdate</td>
                        <td>{{ $student->birthdate->format('m/d/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Age</td>
                        <td>{{ $student->age }} years old</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sex</td>
                        <td>{{ $student->sex_display }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #1</td>
                        <td>{{ $student->contact_number_1 ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #2</td>
                        <td>{{ $student->contact_number_2 ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @php
                                $sc = ['active'=>'success','inactive'=>'secondary','withdrawn'=>'warning','completed'=>'primary'];
                            @endphp
                            <span class="badge bg-{{ $sc[$student->status] ?? 'secondary' }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-geo-alt me-1"></i>Address
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Region</td>
                        <td>{{ $student->region ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Province</td>
                        <td>{{ $student->province ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">City</td>
                        <td>{{ $student->city ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Barangay</td>
                        <td>{{ $student->barangay ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">House / Unit No.</td>
                        <td>{{ $student->house_unit_no ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Street</td>
                        <td>{{ $student->street ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ZIP Code</td>
                        <td>{{ $student->zip_code ?: '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person-heart me-1"></i>Guardian
            </div>
            <div class="card-body">
                @if($student->guardian)
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Name</td>
                        <td>{{ $student->guardian->full_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Relationship</td>
                        <td>{{ $student->guardian->relationship }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact</td>
                        <td>{{ $student->guardian->contact_number }}</td>
                    </tr>
                </table>
                @else
                <p class="text-muted mb-0">No guardian linked.</p>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-mortarboard me-1"></i>Academic Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%">Program Level</td>
                        <td>{{ $student->programLevel?->program_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dev. Pediatrician</td>
                        <td>{{ $student->developmentalPediatrician?->full_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dev. Ped. Document</td>
                        <td>
                            @if($student->dev_ped_document)
                                <a href="{{ Storage::url($student->dev_ped_document) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-file-earmark me-1"></i>View Document
                                </a>
                            @else
                                <span class="text-muted">No document uploaded</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Disabilities</td>
                        <td>
                            @forelse($student->disabilities as $d)
                                <span class="badge bg-info text-dark me-1">{{ $d->disability_name }}</span>
                            @empty
                                <span class="text-muted">None on record</span>
                            @endforelse
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection