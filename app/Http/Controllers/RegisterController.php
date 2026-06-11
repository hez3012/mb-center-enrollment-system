<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;
use App\Models\Guardian;
use App\Models\AuditLog;

class RegisterController extends Controller
{
    public function showForm()
    {
        if (Auth::check()) {
            $role = Auth::user()->role?->role_name;
            return redirect()->route(
                $role === 'guardian' ? 'portal.dashboard' : 'admin.dashboard'
            );
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name'       => 'required|string|min:2|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|min:2|max:100',
            'sex'              => 'required|in:male,female,prefer_not_to_say,others',
            'sex_specify'      => 'nullable|string|max:100',
            'birthdate'        => 'nullable|date',
            'contact_number_1' => ['required', 'regex:/^09\d{9}$/'],
            'contact_number_2' => ['nullable', 'regex:/^09\d{9}$/'],
            'region'           => 'required|string|max:100',
            'province'         => 'required|string|max:100',
            'city'             => 'required|string|max:100',
            'barangay'         => 'required|string|min:4|max:100',
            'house_unit_no'    => 'required|string|min:1|max:100',
            'street'           => 'required|string|min:4|max:100',
            'zip_code'         => ['required', 'regex:/^\d{4}$/'],
            'email'            => 'required|email|unique:users,email',
            'username'         => 'required|string|min:4|max:50|unique:users,username|alpha_dash',
            'password'         => 'required|string|min:6|confirmed',
            'relationship'     => 'required|string',
        ], [
            'contact_number_1.regex' => 'Contact #1 must start with 09 and be exactly 11 digits.',
            'contact_number_2.regex' => 'Contact #2 must start with 09 and be exactly 11 digits.',
            'zip_code.regex'         => 'ZIP code must be exactly 4 digits.',
            'username.alpha_dash'    => 'Username may only contain letters, numbers, dashes, and underscores.',
            'username.unique'        => 'That username is already taken. Please choose another.',
            'email.unique'           => 'An account with that email already exists. Try logging in instead.',
        ]);

        $guardianRole = Role::where('role_name', 'guardian')->firstOrFail();

        $user = User::create([
            'first_name'       => $request->first_name,
            'middle_name'      => $request->middle_name,
            'last_name'        => $request->last_name,
            'sex'              => $request->sex,
            'sex_specify'      => $request->sex === 'others' ? $request->sex_specify : null,
            'birthdate'        => $request->birthdate,
            'contact_number_1' => $request->contact_number_1,
            'contact_number_2' => $request->contact_number_2,
            'region'           => $request->region,
            'province'         => $request->province,
            'city'             => $request->city,
            'barangay'         => $request->barangay,
            'house_unit_no'    => $request->house_unit_no,
            'street'           => $request->street,
            'zip_code'         => $request->zip_code,
            'email'            => $request->email,
            'username'         => $request->username,
            'password'         => Hash::make($request->password),
            'role_id'          => $guardianRole->role_id,
            'is_active'        => 1,
        ]);

        Guardian::create([
            'user_id'      => $user->user_id,
            'relationship' => $request->relationship,
        ]);

        // Assign default guardian permissions
        $defaultPermIds = DB::table('role_permissions')
            ->where('role_id', $guardianRole->role_id)
            ->pluck('permission_id');

        foreach ($defaultPermIds as $permId) {
            DB::table('user_permissions')->insertOrIgnore([
                'user_id'       => $user->user_id,
                'permission_id' => $permId,
            ]);
        }

        AuditLog::create([
            'user_id'    => $user->user_id,
            'action'     => 'CREATE',
            'table_name' => 'users',
            'record_id'  => $user->user_id,
            'changes'    => json_encode([
                'action'   => 'self_registration',
                'username' => $user->username,
            ]),
        ]);

        Log::info('Guardian self-registration', [
            'user_id'  => $user->user_id,
            'username' => $user->username,
        ]);

        Auth::login($user);

        return redirect()->route('portal.dashboard')
            ->with('success',
                'Welcome, ' . $user->first_name . '! Your guardian account has been created successfully.'
            );
    }
}