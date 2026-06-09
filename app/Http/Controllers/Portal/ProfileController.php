<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AuditLog;
use App\Helpers\PhilippinesGeo;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('portal.profile', [
            'user'      => Auth::user(),
            'regions'   => PhilippinesGeo::regions(),
            'provinces' => PhilippinesGeo::provinces(),
            'cities'    => PhilippinesGeo::cities(),
        ]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'first_name'      => 'required|string|max:50',
            'middle_name'     => 'nullable|string|max:50',
            'last_name'       => 'required|string|max:50',
            'birthdate'       => 'nullable|date',
            'contact_number_1'=> 'required|string|max:20',
            'contact_number_2'=> 'nullable|string|max:20',
            'region'          => 'nullable|string|max:100',
            'province'        => 'nullable|string|max:100',
            'city'            => 'nullable|string|max:100',
            'house_unit_no'   => 'nullable|string|max:100',
            'street'          => 'nullable|string|max:100',
            'barangay'        => 'nullable|string|max:100',
            'zip_code'        => 'nullable|string|max:10',
            'email'           => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'username'        => 'required|string|max:50|unique:users,username,' . $user->user_id . ',user_id',
            'password'        => 'nullable|string|min:8|confirmed',
        ]);

        $user->fill($request->except(['password', 'password_confirmation', '_token', '_method']));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        AuditLog::create([
            'user_id'    => $user->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'users',
            'record_id'  => $user->user_id,
            'changes'    => json_encode(['action' => 'portal_profile_updated']),
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }
}