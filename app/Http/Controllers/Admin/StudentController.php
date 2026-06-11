<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\ProgramLevel;
use App\Models\Disability;
use App\Models\DevelopmentalPediatrician;
use App\Models\AuditLog;
use App\Helpers\PhilippinesGeo;

class StudentController extends Controller
{
    public function index()
    {
        Log::info('Student Management: index accessed', ['by' => Auth::user()->username]);

        $students      = Student::with(['guardian.user', 'programLevel', 'disabilities'])
            ->whereNull('deleted_at')->get();
        $programLevels = ProgramLevel::all();
        $disabilities  = Disability::all();

        // Student IDs that have a pending online enrollment — these cannot be edited
        $lockedStudentIds = \App\Models\Enrollment::where('enrollment_type', 'online')
            ->where('status', 'pending')
            ->whereNull('deleted_at')
            ->pluck('student_id')
            ->toArray();

        return view('admin.students.index', compact(
            'students',
            'programLevels',
            'disabilities',
            'lockedStudentIds'
        ));
    }

    public function create()
    {
        $guardians     = Guardian::with('user')
            ->whereHas('user', fn($q) => $q->whereNull('deleted_at'))->get();
        $programLevels = ProgramLevel::all();
        $disabilities  = Disability::all();
        $devPeds       = DevelopmentalPediatrician::all();

        $geo       = new PhilippinesGeo();
        $regions   = $geo->getRegions();
        $provinces = $geo->getProvinces('');
        $cities    = $geo->getCities('');

        return view('admin.students.create', compact(
            'guardians',
            'programLevels',
            'disabilities',
            'devPeds',
            'regions',
            'provinces',
            'cities'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'       => 'required|string|min:2|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|min:2|max:100',
            'birthdate'        => 'required|date',
            'sex'              => 'required|in:male,female,others,prefer_not_to_say',
            'sex_specify'      => 'nullable|string|max:100',
            'status'           => 'required|in:active,inactive,withdrawn,completed',
            'program_level_id' => 'required|exists:program_level,program_level_id',
            'guardian_id'      => 'required|exists:guardian,guardian_id',
            'dev_ped_id'       => 'nullable|exists:developmental_pediatrician,dev_ped_id',
            'dev_ped_document' => 'nullable|string|max:255',
            'region'           => 'required|string|max:100',
            'province'         => 'required|string|max:100',
            'city'             => 'required|string|max:100',
            'barangay'         => 'required|string|min:4|max:100',
            'house_unit_no'    => 'required|string|min:1|max:100',
            'street'           => 'required|string|min:4|max:100',
            'zip_code'         => ['required', 'regex:/^\d{4}$/'],
            'profile_picture'  => 'nullable|image|mimes:jpg,jpeg,png|max:51200',
            'disabilities'     => 'required|array|min:1',
            'disability_other' => 'nullable|string|max:255',
        ]);

        $picturePath = null;
        if ($request->hasFile('profile_picture')) {
            $picturePath = $request->file('profile_picture')
                ->store('profile_pictures/students', 'public');
        }

        $student = Student::create([
            'first_name'       => $request->first_name,
            'middle_name'      => $request->middle_name,
            'last_name'        => $request->last_name,
            'birthdate'        => $request->birthdate,
            'sex'              => $request->sex,
            'sex_specify'      => $request->sex === 'others' ? $request->sex_specify : null,
            'status'           => $request->status,
            'profile_picture'  => $picturePath,
            'disability_other' => $request->disability_other,
            'dev_ped_document' => $request->dev_ped_document,
            'region'           => $request->region,
            'province'         => $request->province,
            'city'             => $request->city,
            'barangay'         => $request->barangay,
            'house_unit_no'    => $request->house_unit_no,
            'street'           => $request->street,
            'zip_code'         => $request->zip_code,
            'guardian_id'      => $request->guardian_id,
            'program_level_id' => $request->program_level_id,
            'dev_ped_id'       => $request->dev_ped_id,
        ]);

        $student->disabilities()->sync($request->disabilities ?? []);

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'CREATE',
            'table_name' => 'student',
            'record_id'  => $student->student_id,
            'changes'    => json_encode(['name' => $student->full_name]),
        ]);

        Log::info('Student created', [
            'by'         => Auth::user()->username,
            'student_id' => $student->student_id,
            'name'       => $student->full_name,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    public function show(string $id)
    {
        Log::info('Student Management: viewing student', [
            'by'         => Auth::user()->username,
            'student_id' => $id,
        ]);

        $student = Student::with(['guardian.user', 'programLevel', 'disabilities', 'devPed'])
            ->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    public function edit(string $id)
    {
        $student = Student::with(['disabilities'])->findOrFail($id);

        // Block edit if the student has a pending digital (online) enrollment
        $isLocked = \App\Models\Enrollment::where('student_id', $id)
            ->where('enrollment_type', 'online')
            ->where('status', 'pending')
            ->whereNull('deleted_at')
            ->exists();

        if ($isLocked) {
            return redirect()->route('admin.students.show', $id)
                ->with('error', 'This student cannot be edited while their digital enrollment is pending review.');
        }

        $guardians     = Guardian::with('user')
            ->whereHas('user', fn($q) => $q->whereNull('deleted_at'))->get();
        $programLevels = ProgramLevel::all();
        $disabilities  = Disability::all();
        $devPeds       = DevelopmentalPediatrician::all();

        $geo       = new PhilippinesGeo();
        $regions   = $geo->getRegions();
        $provinces = $geo->getProvinces($student->region ?? '');
        $cities    = $geo->getCities($student->province ?? '');

        return view('admin.students.edit', compact(
            'student',
            'guardians',
            'programLevels',
            'disabilities',
            'devPeds',
            'regions',
            'provinces',
            'cities'
        ));
    }

    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        // Security: block update if student has a pending digital enrollment
        $isLocked = \App\Models\Enrollment::where('student_id', $id)
            ->where('enrollment_type', 'online')
            ->where('status', 'pending')
            ->whereNull('deleted_at')
            ->exists();

        if ($isLocked) {
            return redirect()->route('admin.students.show', $id)
                ->with('error', 'This student cannot be edited while their digital enrollment is pending review.');
        }

        $request->validate([
            'first_name'       => 'required|string|min:2|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|min:2|max:100',
            'birthdate'        => 'required|date',
            'sex'              => 'required|in:male,female,others,prefer_not_to_say',
            'sex_specify'      => 'nullable|string|max:100',
            'status'           => 'required|in:active,inactive,withdrawn,completed',
            'program_level_id' => 'required|exists:program_level,program_level_id',
            'guardian_id'      => 'required|exists:guardian,guardian_id',
            'dev_ped_id'       => 'nullable|exists:developmental_pediatrician,dev_ped_id',
            'dev_ped_document' => 'nullable|string|max:255',
            'region'           => 'required|string|max:100',
            'province'         => 'required|string|max:100',
            'city'             => 'required|string|max:100',
            'barangay'         => 'required|string|min:4|max:100',
            'house_unit_no'    => 'required|string|min:1|max:100',
            'street'           => 'required|string|min:4|max:100',
            'zip_code'         => ['required', 'regex:/^\d{4}$/'],
            'profile_picture'  => 'nullable|image|mimes:jpg,jpeg,png|max:51200',
            'disabilities'     => 'required|array|min:1',
            'disability_other' => 'nullable|string|max:255',
        ]);

        $picturePath = $student->profile_picture;
        if ($request->hasFile('profile_picture')) {
            if ($picturePath) Storage::disk('public')->delete($picturePath);
            $picturePath = $request->file('profile_picture')
                ->store('profile_pictures/students', 'public');
        }

        $student->update([
            'first_name'       => $request->first_name,
            'middle_name'      => $request->middle_name,
            'last_name'        => $request->last_name,
            'birthdate'        => $request->birthdate,
            'sex'              => $request->sex,
            'sex_specify'      => $request->sex === 'others' ? $request->sex_specify : null,
            'status'           => $request->status,
            'profile_picture'  => $picturePath,
            'disability_other' => $request->disability_other,
            'dev_ped_document' => $request->dev_ped_document,
            'region'           => $request->region,
            'province'         => $request->province,
            'city'             => $request->city,
            'barangay'         => $request->barangay,
            'house_unit_no'    => $request->house_unit_no,
            'street'           => $request->street,
            'zip_code'         => $request->zip_code,
            'guardian_id'      => $request->guardian_id,
            'program_level_id' => $request->program_level_id,
            'dev_ped_id'       => $request->dev_ped_id,
        ]);

        $student->disabilities()->sync($request->input('disabilities', []));

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'student',
            'record_id'  => $student->student_id,
            'changes'    => json_encode(['name' => $student->full_name]),
        ]);

        Log::info('Student updated', [
            'by'         => Auth::user()->username,
            'student_id' => $student->student_id,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);

        // Block if student has any enrollment records
        if ($student->enrollments()->exists()) {
            return back()->with(
                'error',
                '"' . $student->full_name . '" cannot be deleted because they have existing ' .
                    'enrollment record(s). Delete the related enrollment(s) first.'
            );
        }

        $student->delete();

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'DELETE',
            'table_name' => 'student',
            'record_id'  => $id,
            'changes'    => json_encode(['deleted' => $id]),
        ]);

        Log::info('Student deleted', [
            'by'         => Auth::user()->username,
            'student_id' => $id,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }
}
