<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\ProgramLevel;
use App\Models\ServiceType;
use App\Models\Disability;
use App\Models\DevelopmentalPediatrician;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────────────────────
    private function spedId(): int
    {
        $match = ServiceType::all()->first(function ($st) {
            return stripos($st->service_name, 'sped') !== false
                || stripos($st->service_name, 'special education') !== false;
        });
        return (int) $match?->service_type_id;
    }

    private function isOthers(?int $disabilityId): bool
    {
        return Disability::find($disabilityId)?->disability_name === 'Others';
    }

    private function rules(bool $isSpED, bool $isOthers): array
    {
        return [
            'first_name'        => 'required|string|min:2|max:50',
            'last_name'         => 'required|string|min:2|max:50',
            'middle_name'       => 'nullable|string|max:50',
            'birthdate'         => 'required|date|before:today',
            'sex'               => 'required|in:male,female,others,prefer_not_to_say',
            'sex_specify'       => 'nullable|string|max:100',
            'profile_picture'   => 'nullable|image|max:51200',
            'contact_number_1'  => ['nullable', 'regex:/^09\d{9}$/'],
            'contact_number_2'  => ['nullable', 'regex:/^09\d{9}$/'],
            'house_unit_no'     => 'nullable|string|max:100',
            'street'            => 'nullable|string|min:4|max:100',
            'barangay'          => 'nullable|string|min:4|max:100',
            'city'              => 'nullable|string|max:100',
            'province'          => 'nullable|string|max:100',
            'region'            => 'nullable|string|max:100',
            'zip_code'          => ['nullable', 'regex:/^\d{4}$/'],
            'status'            => 'required|in:active,inactive,withdrawn,completed',
            'guardian_id'       => 'required|exists:guardian,guardian_id',
            'dev_ped_id'        => 'nullable|exists:developmental_pediatrician,dev_ped_id',
            'dev_ped_document'  => 'nullable|file|max:51200',
            'service_type_id'   => 'required|exists:service_type,service_type_id',
            'disability_id'     => 'required|exists:disability,disability_id',
            'disability_other'  => $isOthers
                ? 'required|string|max:255'
                : 'nullable|string|max:255',
            'program_level_id'  => $isSpED
                ? 'required|exists:program_level,program_level_id'
                : 'nullable|exists:program_level,program_level_id',
        ];
    }

    // ── Index ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Student::with(['guardian.user', 'serviceType', 'programLevel'])
            ->orderBy('last_name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name',  'like', "%{$s}%");
            });
        }

        $students = $query->paginate(15)->withQueryString();

        $lockedStudentIds = \App\Models\Enrollment::where('enrollment_type', 'online')
            ->where('status', 'pending')
            ->pluck('student_id')
            ->toArray();

        $serviceTypes = ServiceType::all();
        return view('admin.students.index', compact('students', 'lockedStudentIds', 'serviceTypes'));
    }

    // ── Create ────────────────────────────────────────────────────────────────
    public function create()
    {
        return view('admin.students.create', [
            'serviceTypes'  => ServiceType::all(),
            'disabilities'  => Disability::all(),
            'programLevels' => ProgramLevel::all(),
            'devPeds'       => DevelopmentalPediatrician::all(),
            'guardians'     => Guardian::with('user')->get(),
            'spedId'        => $this->spedId(),
        ]);
    }

    // ── Store ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $isSpED   = (int) $request->service_type_id === $this->spedId();
        $isOthers = $this->isOthers($request->disability_id);

        $validated = $request->validate($this->rules($isSpED, $isOthers));

        if (!$isSpED) {
            $validated['program_level_id'] = null;
        }
        if (!$isOthers) {
            $validated['disability_other']  = null;
        }

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')
                ->store('profile_pictures/students', 'public');
        }

        if ($request->hasFile('dev_ped_document')) {
            $validated['dev_ped_document'] = $request->file('dev_ped_document')
                ->store('dev_ped_documents', 'public');
        }

        $student = Student::create($validated);

        AuditLog::create([
            'action'       => 'create',
            'table_name'   => 'student',
            'record_id'    => $student->student_id,
            'description'  => 'Created student: ' . $student->full_name,
            'user_id'      => Auth::id(),
            'performed_at' => now(),
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student record created successfully.');
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show(Request $request, $id)
    {
        $student = Student::with([
            'guardian.user',
            'serviceType',
            'disability',
            'programLevel',
            'developmentalPediatrician',
        ])->findOrFail($id);

        $fromGuardian = $request->query('from_guardian');

        return view('admin.students.show', compact('student', 'fromGuardian'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────────
    public function edit($id)
    {
        $student  = Student::findOrFail($id);
        $isLocked = $student->enrollments()
            ->where('enrollment_type', 'online')
            ->where('status', 'pending')
            ->exists();

        if ($isLocked) {
            return redirect()
                ->route('admin.students.show', $student->student_id)
                ->with(
                    'error',
                    'This student has a pending online enrollment and cannot be edited.'
                );
        }

        return view('admin.students.edit', [
            'student'       => $student,
            'serviceTypes'  => ServiceType::all(),
            'disabilities'  => Disability::all(),
            'programLevels' => ProgramLevel::all(),
            'devPeds'       => DevelopmentalPediatrician::all(),
            'guardians'     => Guardian::with('user')->get(),
            'spedId'        => $this->spedId(),
        ]);
    }

    // ── Update ────────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $student  = Student::findOrFail($id);
        $isSpED   = (int) $request->service_type_id === $this->spedId();
        $isOthers = $this->isOthers($request->disability_id);

        $validated = $request->validate($this->rules($isSpED, $isOthers));

        if (!$isSpED) {
            $validated['program_level_id'] = null;
        }
        if (!$isOthers) {
            $validated['disability_other']  = null;
        }

        if ($request->hasFile('profile_picture')) {
            if ($student->profile_picture) {
                Storage::disk('public')->delete($student->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')
                ->store('profile_pictures/students', 'public');
        }

        if ($request->hasFile('dev_ped_document')) {
            if ($student->dev_ped_document) {
                Storage::disk('public')->delete($student->dev_ped_document);
            }
            $validated['dev_ped_document'] = $request->file('dev_ped_document')
                ->store('dev_ped_documents', 'public');
        }

        $student->update($validated);

        AuditLog::create([
            'action'       => 'update',
            'table_name'   => 'student',
            'record_id'    => $student->student_id,
            'description'  => 'Updated student: ' . $student->full_name,
            'user_id'      => Auth::id(),
            'performed_at' => now(),
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student record updated successfully.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        if ($student->enrollments()->exists()) {
            return back()->with(
                'error',
                'Cannot delete a student with existing enrollment records.'
            );
        }

        if ($student->profile_picture) {
            Storage::disk('public')->delete($student->profile_picture);
        }

        $name = $student->full_name;
        $id   = $student->student_id;
        $student->delete();

        AuditLog::create([
            'action'       => 'delete',
            'table_name'   => 'student',
            'record_id'    => $id,
            'description'  => 'Deleted student: ' . $name,
            'user_id'      => Auth::id(),
            'performed_at' => now(),
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student record deleted successfully.');
    }
}
