@extends('admin.layouts.app')
@section('title', 'Guardian Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Guardian Details</h5>
    <div class="d-flex gap-2">
        @if(Auth::user()->hasPermission('edit_guardian'))
            <a href="{{ route('admin.guardians.edit',$guardian->guardian_id) }}"
               class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        @endif
        <a href="{{ route('admin.guardians.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                @include('partials.avatar',[
                    'name'  => optional($guardian->user)->list_name ?? '?',
                    'image' => optional($guardian->user)->profile_picture ?? null,
                    'size'  => 80,
                ])
                <div class="fw-bold mt-3">{{ optional($guardian->user)->list_name ?? '—' }}</div>
                <div class="text-muted small">{{ optional($guardian->user)->username }}</div>
                <span class="badge bg-secondary mt-1">Guardian</span>
                <br>
                <span class="badge bg-{{ optional($guardian->user)->is_active ? 'success' : 'secondary' }} mt-1">
                    {{ optional($guardian->user)->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person-heart me-1"></i>Guardian Information
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:35%">Full Name</td>
                        <td>{{ optional($guardian->user)->full_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ optional($guardian->user)->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #1</td>
                        <td>{{ optional($guardian->user)->contact_number_1 ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Contact #2</td>
                        <td>{{ optional($guardian->user)->contact_number_2 ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Relationship</td>
                        <td>{{ $guardian->relationship ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Address</td>
                        <td>{{ optional($guardian->user)->full_address ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-mortarboard me-1"></i>Linked Students
            </div>
            <div class="card-body p-0">
                @forelse($guardian->students as $student)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            @include('partials.avatar',[
                                'name'  => $student->list_name,
                                'image' => $student->profile_picture,
                                'size'  => 32,
                            ])
                            <div>
                                <div class="fw-semibold small">{{ $student->list_name }}</div>
                                <div class="text-muted small">
                                    {{ optional($student->programLevel)->program_name ?? '—' }}
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->hasPermission('view_student'))
                            <a href="{{ route('admin.students.show',$student->student_id) }}"
                               class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-muted small px-3 py-2 mb-0">No students linked yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection