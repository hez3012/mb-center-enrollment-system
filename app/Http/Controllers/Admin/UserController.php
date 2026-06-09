<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Guardian;
use App\Models\AuditLog;
use App\Helpers\PhilippinesGeo;

class UserController extends Controller
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
        $users = User::with('role')->get();
        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with(['role', 'permissions', 'guardian'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function create(Request $request)
    {
        $roles           = Role::all();
        $permissions     = Permission::orderBy('category')->orderBy('permission_name')->get();
        $currentRoleName = Auth::user()->role->role_name;
        $preselectedRole = $request->query('role');

        $allowedRoles = match($currentRoleName) {
            'directress' => $roles->whereNotIn('role_name', ['directress']),
            'admin'      => $roles->whereNotIn('role_name', ['directress']),
            'teacher'    => $roles->whereIn('role_name', ['staff', 'guardian']),
            default      => $roles->whereIn('role_name', ['staff', 'guardian']),
        };

        $rolePermissions = Role::with('permissions')->get()
            ->mapWithKeys(fn($role) => [
                $role->role_id => $role->permissions->pluck('permission_id')->toArray()
            ]);

        return view('admin.users.create', compact(
            'allowedRoles', 'permissions', 'rolePermissions', 'preselectedRole'
        ) + $this->geoData());
    }

    public function store(Request $request)
    {
        $isGuardian = Role::find($request->role_id)?->role_name === 'guardian';

        $rules = [
            'first_name'       => 'required|string|max:50',
            'middle_name'      => 'nullable|string|max:50',
            'last_name'        => 'required|string|max:50',
            'birthdate'        => 'nullable|date',
            'contact_number_1' => 'nullable|string|max:20',
            'contact_number_2' => 'nullable|string|max:20',
            'region'           => 'nullable|string|max:100',
            'province'         => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'house_unit_no'    => 'nullable|string|max:100',
            'street'           => 'nullable|string|max:100',
            'barangay'         => 'nullable|string|max:100',
            'zip_code'         => 'nullable|string|max:10',
            'email'            => 'required|email|unique:users,email',
            'username'         => 'required|string|unique:users,username|max:50',
            'password'         => 'required|string|min:8|confirmed',
            'role_id'          => 'required|exists:roles,role_id',
            'permissions'      => 'array',
        ];

        if ($isGuardian) {
            $rules['contact_number_1'] = 'required|string|max:20';
            $rules['relationship']     = 'required|string|max:50';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $user = User::create([
                'first_name'       => $request->first_name,
                'middle_name'      => $request->middle_name,
                'last_name'        => $request->last_name,
                'birthdate'        => $request->birthdate,
                'contact_number_1' => $request->contact_number_1,
                'contact_number_2' => $request->contact_number_2,
                'region'           => $request->region,
                'province'         => $request->province,
                'city'             => $request->city,
                'house_unit_no'    => $request->house_unit_no,
                'street'           => $request->street,
                'barangay'         => $request->barangay,
                'zip_code'         => $request->zip_code,
                'email'            => $request->email,
                'username'         => $request->username,
                'password'         => Hash::make($request->password),
                'is_active'        => true,
                'failed_attempts'  => 0,
                'role_id'          => $request->role_id,
            ]);

            if ($isGuardian) {
                $guardianRole = Role::where('role_name', 'guardian')->first();
                if ($guardianRole) {
                    $user->permissions()->sync(
                        $guardianRole->permissions->pluck('permission_id')->toArray()
                    );
                }
                Guardian::create([
                    'user_id'        => $user->user_id,
                    'first_name'     => $request->first_name,
                    'middle_name'    => $request->middle_name,
                    'last_name'      => $request->last_name,
                    'contact_number' => $request->contact_number_1,
                    'relationship'   => $request->relationship,
                    'address'        => '',
                ]);
            } else {
                $user->permissions()->sync($request->permissions ?? []);
            }

            AuditLog::create([
                'user_id'    => Auth::user()->user_id,
                'action'     => 'CREATE',
                'table_name' => 'users',
                'record_id'  => $user->user_id,
                'changes'    => json_encode(['created_user' => $user->username]),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create user. Please try again.')->withInput();
        }

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->username} created successfully.");
    }

    public function edit($id)
    {
        $user            = User::with(['role', 'permissions', 'guardian'])->findOrFail($id);
        $roles           = Role::all();
        $permissions     = Permission::orderBy('category')->orderBy('permission_name')->get();
        $currentRoleName = Auth::user()->role->role_name;
        $userRoleName    = $user->role?->role_name;

        if ($userRoleName === 'guardian') {
            $allowedRoles = $roles->whereIn('role_name', ['guardian']);
        } else {
            $allowedRoles = match($currentRoleName) {
                'directress' => $roles->whereIn('role_name', ['admin', 'teacher', 'staff']),
                'admin'      => $roles->whereIn('role_name', ['admin', 'teacher', 'staff']),
                'teacher'    => $roles->whereIn('role_name', ['staff']),
                default      => $roles->whereIn('role_name', ['staff']),
            };
        }

        $rolePermissions = Role::with('permissions')->get()
            ->mapWithKeys(fn($role) => [
                $role->role_id => $role->permissions->pluck('permission_id')->toArray()
            ]);

        return view('admin.users.edit', compact(
            'user', 'allowedRoles', 'permissions', 'rolePermissions', 'userRoleName'
        ) + $this->geoData());
    }

    public function update(Request $request, $id)
    {
        $user         = User::with(['role', 'guardian'])->findOrFail($id);
        $userRoleName = $user->role?->role_name;
        $isGuardian   = $userRoleName === 'guardian';

        $rules = [
            'first_name'       => 'required|string|max:50',
            'middle_name'      => 'nullable|string|max:50',
            'last_name'        => 'required|string|max:50',
            'birthdate'        => 'nullable|date',
            'contact_number_1' => 'nullable|string|max:20',
            'contact_number_2' => 'nullable|string|max:20',
            'region'           => 'nullable|string|max:100',
            'province'         => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
            'house_unit_no'    => 'nullable|string|max:100',
            'street'           => 'nullable|string|max:100',
            'barangay'         => 'nullable|string|max:100',
            'zip_code'         => 'nullable|string|max:10',
            'email'            => 'required|email|unique:users,email,' . $id . ',user_id',
            'username'         => 'required|string|max:50|unique:users,username,' . $id . ',user_id',
            'password'         => 'nullable|string|min:8|confirmed',
            'role_id'          => 'required|exists:roles,role_id',
            'permissions'      => 'array',
        ];

        if ($isGuardian) {
            $rules['contact_number_1'] = 'required|string|max:20';
            $rules['relationship']     = 'required|string|max:50';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $user->fill([
                'first_name'       => $request->first_name,
                'middle_name'      => $request->middle_name,
                'last_name'        => $request->last_name,
                'birthdate'        => $request->birthdate,
                'contact_number_1' => $request->contact_number_1,
                'contact_number_2' => $request->contact_number_2,
                'region'           => $request->region,
                'province'         => $request->province,
                'city'             => $request->city,
                'house_unit_no'    => $request->house_unit_no,
                'street'           => $request->street,
                'barangay'         => $request->barangay,
                'zip_code'         => $request->zip_code,
                'email'            => $request->email,
                'username'         => $request->username,
                'role_id'          => $isGuardian ? $user->role_id : $request->role_id,
            ]);

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            if ($isGuardian) {
                Guardian::updateOrCreate(
                    ['user_id' => $user->user_id],
                    [
                        'first_name'     => $request->first_name,
                        'middle_name'    => $request->middle_name,
                        'last_name'      => $request->last_name,
                        'contact_number' => $request->contact_number_1,
                        'relationship'   => $request->relationship,
                        'address'        => '',
                    ]
                );
            } else {
                $user->permissions()->sync($request->permissions ?? []);
            }

            AuditLog::create([
                'user_id'    => Auth::user()->user_id,
                'action'     => 'UPDATE',
                'table_name' => 'users',
                'record_id'  => $user->user_id,
                'changes'    => json_encode(['updated_user' => $user->username]),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User update failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update user. Please try again.')->withInput();
        }

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->username} updated successfully.");
    }

    public function toggle($id)
    {
        $user            = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();
        $status = $user->is_active ? 'activated' : 'deactivated';

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'users',
            'record_id'  => $user->user_id,
            'changes'    => json_encode(['status' => $status]),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->username} has been {$status}.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->user_id === Auth::user()->user_id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->full_name;
        $user->delete();

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'DELETE',
            'table_name' => 'users',
            'record_id'  => $id,
            'changes'    => json_encode(['deleted_user' => $user->username]),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$name} has been deleted.");
    }
}