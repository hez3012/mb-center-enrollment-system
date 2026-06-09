<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Disability;
use App\Models\ProgramLevel;
use App\Models\DevelopmentalPediatrician;
use App\Models\AuditLog;
use App\Helpers\PhilippinesGeo;

class StudentController extends Controller
{
    private function geoData(): array
    {
        return [
            'regions'   => PhilippinesGeo::regions(),
            'provinces' => PhilippinesGeo::provinces(),
            'cities'    => PhilippinesGeo::cities(),
        ];
    }

    public function index()
    {
        $students      = Student::with(['guardian', 'programLevel', 'disabilities'])->get();
        $programLevels = ProgramLevel::all();
        $disabilities  = Disability::all();
        return view('admin.students.index', compact('students', 'programLevels', 'disabilities'));
    }

    public function create()
    {
        $guardians     = Guardian::all();
        $disabilities  = Disability::all();
        $programLevels = ProgramLevel::all();
        $devPeds       = DevelopmentalPediatrician::all();
        return view('admin.students.create', compact(
            'guardians', 'disabilities', 'programLevels', 'devPeds'
        ) + $this->geoData());
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'       => 'required|string|max:50',
            'middle_name'      => 'nullable|string|max:50',
            'last_name'        => 'required|string|max:50',
            'birthdate'        => 'required|date',
            'sex'              => 'required|in:male,female,others,prefer_not_to_say',
            'sex_specify'      => 'nullable|required_if:sex,others|string|max:100',
            'contact_number_1' => 'nullable|string|max:20',
            'contact_number_2' => 'nullable|string|max:20',
            'region'           => 'nullable|string|max:100',
            'province'         => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'house_unit_no'    => 'nullable|string|max:100',
            'street'           => 'nullable|string|max:100',
            'barangay'         => 'nullable|string|max:100',
            'zip_code'         => 'nullable|string|max:10',
            'status'           => 'required|in:active,inactive,withdrawn,completed',
            'guardian_id'      => 'required|exists:guardian,guardian_id',
            'program_level_id' => 'required|exists:program_level,program_level_id',
            'dev_ped_id'       => 'nullable|exists:developmental_pediatrician,dev_ped_id',
            'dev_ped_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'disabilities'     => 'array',
        ]);

        $data = $request->except(['disabilities', 'dev_ped_document', '_token']);

        if ($request->hasFile('dev_ped_document')) {
            $data['dev_ped_document'] = $request->file('dev_ped_document')
                ->store('dev_ped_documents', 'public');
        }

        $student = Student::create($data);

        if ($request->has('disabilities')) {
            $student->disabilities()->sync($request->disabilities);
        }

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'CREATE',
            'table_name' => 'student',
            'record_id'  => $student->student_id,
            'changes'    => json_encode(['created_student' => $student->full_name]),
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', "Student {$student->full_name} created successfully.");
    }

    public function show($id)
    {
        $student = Student::with([
            'guardian', 'programLevel',
            'disabilities', 'developmentalPediatrician'
        ])->findOrFail($id);
        return view('admin.students.show', compact('student'));
    }

    public function edit($id)
    {
        $student       = Student::with('disabilities')->findOrFail($id);
        $guardians     = Guardian::all();
        $disabilities  = Disability::all();
        $programLevels = ProgramLevel::all();
        $devPeds       = DevelopmentalPediatrician::all();
        return view('admin.students.edit', compact(
            'student', 'guardians', 'disabilities', 'programLevels', 'devPeds'
        ) + $this->geoData());
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'first_name'       => 'required|string|max:50',
            'middle_name'      => 'nullable|string|max:50',
            'last_name'        => 'required|string|max:50',
            'birthdate'        => 'required|date',
            'sex'              => 'required|in:male,female,others,prefer_not_to_say',
            'sex_specify'      => 'nullable|required_if:sex,others|string|max:100',
            'contact_number_1' => 'nullable|string|max:20',
            'contact_number_2' => 'nullable|string|max:20',
            'region'           => 'nullable|string|max:100',
            'province'         => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'house_unit_no'    => 'nullable|string|max:100',
            'street'           => 'nullable|string|max:100',
            'barangay'         => 'nullable|string|max:100',
            'zip_code'         => 'nullable|string|max:10',
            'status'           => 'required|in:active,inactive,withdrawn,completed',
            'guardian_id'      => 'required|exists:guardian,guardian_id',
            'program_level_id' => 'required|exists:program_level,program_level_id',
            'dev_ped_id'       => 'nullable|exists:developmental_pediatrician,dev_ped_id',
            'dev_ped_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'disabilities'     => 'array',
        ]);

        $data = $request->except(['disabilities', 'dev_ped_document', '_token', '_method']);

        if ($request->hasFile('dev_ped_document')) {
            if ($student->dev_ped_document) {
                Storage::disk('public')->delete($student->dev_ped_document);
            }
            $data['dev_ped_document'] = $request->file('dev_ped_document')
                ->store('dev_ped_documents', 'public');
        }

        $student->update($data);
        $student->disabilities()->sync($request->disabilities ?? []);

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'student',
            'record_id'  => $student->student_id,
            'changes'    => json_encode(['updated_student' => $student->full_name]),
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', "Student {$student->full_name} updated successfully.");
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $name    = $student->full_name;

        if ($student->dev_ped_document) {
            Storage::disk('public')->delete($student->dev_ped_document);
        }

        $student->delete();

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'DELETE',
            'table_name' => 'student',
            'record_id'  => $id,
            'changes'    => json_encode(['deleted_student' => $name]),
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', "Student {$name} deleted successfully.");
    }
}