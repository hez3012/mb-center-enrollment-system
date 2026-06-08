<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\AuditLog;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'email'      => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'username'   => 'required|string|max:50|unique:users,username,' . $user->user_id . ',user_id',
            'password'   => 'nullable|string|min:8|confirmed',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->username   = $request->username;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        AuditLog::create([
            'user_id'    => $user->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'users',
            'record_id'  => $user->user_id,
            'changes'    => json_encode(['action' => 'profile_updated']),
        ]);

        Log::info('Profile updated', ['user' => $user->username]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function deactivate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Directress cannot deactivate own account
        if ($user->role->role_name === 'directress') {
            return back()->with('error', 'The Directress account cannot be self-deactivated.');
        }

        $user->is_active = false;
        $user->save();

        AuditLog::create([
            'user_id'    => $user->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'users',
            'record_id'  => $user->user_id,
            'changes'    => json_encode(['action' => 'self_deactivated']),
        ]);

        Log::info('User self-deactivated', ['user' => $user->username]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Your account has been deactivated. Please contact your administrator to reactivate it.');
    }
}