<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guardian;
use App\Models\AuditLog;
use App\Helpers\PhilippinesGeo;

class GuardianController extends Controller
{
    public function index()
    {
        $guardians = Guardian::with(['user','students.programLevel'])
            ->whereHas('user', fn($q) => $q->whereNull('deleted_at'))
            ->get();

        return view('admin.guardians.index', compact('guardians'));
    }

    public function show(string $id)
    {
        $guardian = Guardian::with(['user','students.programLevel','students.disabilities'])
            ->findOrFail($id);

        return view('admin.guardians.show', compact('guardian'));
    }

    public function edit(string $id)
    {
        $guardian  = Guardian::with('user')->findOrFail($id);
        $geo       = new PhilippinesGeo();
        $regions   = $geo->getRegions();
        $provinces = $geo->getProvinces($guardian->user->region ?? '');
        $cities    = $geo->getCities($guardian->user->province ?? '');

        return view('admin.guardians.edit', compact('guardian','regions','provinces','cities'));
    }

    public function update(Request $request, string $id)
    {
        $guardian = Guardian::findOrFail($id);
        $user     = $guardian->user;

        $request->validate([
            'relationship'     => 'required|string',
            'contact_number_1' => 'required|string|max:20',
            'contact_number_2' => 'nullable|string|max:20',
        ]);

        $guardian->update(['relationship' => $request->relationship]);

        $user->update([
            'contact_number_1' => $request->contact_number_1,
            'contact_number_2' => $request->contact_number_2,
        ]);

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'guardian',
            'record_id'  => $guardian->guardian_id,
            'changes'    => json_encode(['updated' => $user->full_name]),
        ]);

        return redirect()->route('admin.guardians.index')
            ->with('success','Guardian profile updated successfully.');
    }
}