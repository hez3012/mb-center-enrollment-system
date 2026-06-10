@extends('admin.layouts.app')
@section('title', 'Student Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Student Details</h5>
    <div class="d-flex gap-2">
        @if(Auth::user()->hasPermission('edit_student'))
            <a href="{{ route('admin.students.edit',$student->student_id) }}"
               class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        @endif
        <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                @include('partials.avatar',[
                    'name'  => $student->list_name,
                    'image' => $student->profile_picture,
                    'size'  => 80,
                ])
                <div class="fw-bold mt-3">{{ $student->list_name }}</div>
                @php $sc=['active'=>'success','inactive'=>'secondary','withdrawn'=>'warning','completed'=>'primary']; @endphp
                <span class="badge bg-{{ $sc[$student->status] ?? 'secondary' }} mt-1">
                    {{ ucfirst($student->status) }}
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
                        <td>{{ $student->full_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Birthdate</td>
                        <td>{{ $student->birthdate?->format('m/d/Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Age</td>
                        <td>{{ $student->age !== null ? $student->age . ' years old' : '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sex</td>
                        <td>{{ $student->sex_display ?? ucfirst($student->sex) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Address</td>
                        <td>{{ $student->full_address ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-mortarboard me-1"></i>School Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:35%">Guardian</td>
                        <td>{{ optional($student->guardian)->full_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Program Level</td>
                        <td>{{ optional($student->programLevel)->program_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dev. Pediatrician</td>
                        <td>{{ optional($student->devPed)->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Disabilities</td>
                        <td>
                            @forelse($student->disabilities as $d)
                                <span class="badge bg-info text-dark">{{ $d->disability_name }}</span>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection