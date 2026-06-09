<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Guardian;
use App\Models\AuditLog;
use App\Helpers\PhilippinesGeo;

class GuardianController extends Controller
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
        $guardians = Guardian::with(['user', 'students'])->get();
        return view('admin.guardians.index', compact('guardians'));
    }

    public function show($id)
    {
        $guardian = Guardian::with(['user', 'students.programLevel'])->findOrFail($id);
        return view('admin.guardians.show', compact('guardian'));
    }

    public function edit($id)
    {
        $guardian = Guardian::with('user')->findOrFail($id);
        return view('admin.guardians.edit', compact('guardian') + $this->geoData());
    }

    public function update(Request $request, $id)
    {
        $guardian = Guardian::with('user')->findOrFail($id);

        $request->validate([
            'middle_name'      => 'nullable|string|max:50',
            'contact_number_1' => 'required|string|max:20',
            'contact_number_2' => 'nullable|string|max:20',
            'relationship'     => 'required|string|max:50',
            'region'           => 'nullable|string|max:100',
            'province'         => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'house_unit_no'    => 'nullable|string|max:100',
            'street'           => 'nullable|string|max:100',
            'barangay'         => 'nullable|string|max:100',
            'zip_code'         => 'nullable|string|max:10',
        ]);

        // Update user account (address + contact + middle name)
        if ($guardian->user) {
            $guardian->user->update($request->only([
                'middle_name',
                'contact_number_1',
                'contact_number_2',
                'region',
                'province',
                'city',
                'house_unit_no',
                'street',
                'barangay',
                'zip_code',
            ]));
        }

        // Update guardian profile
        $guardian->update([
            'middle_name'    => $request->middle_name,
            'contact_number' => $request->contact_number_1,
            'relationship'   => $request->relationship,
        ]);

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'guardian',
            'record_id'  => $guardian->guardian_id,
            'changes'    => json_encode(['updated_guardian' => $guardian->full_name]),
        ]);

        Log::info('Guardian updated', [
            'by'       => Auth::user()->username,
            'guardian' => $guardian->full_name,
        ]);

        return redirect()->route('admin.guardians.index')
            ->with('success', "Guardian {$guardian->full_name} updated successfully.");
    }
}