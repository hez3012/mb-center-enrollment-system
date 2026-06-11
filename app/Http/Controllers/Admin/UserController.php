<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Guardian;
use App\Models\AuditLog;
use App\Helpers\PhilippinesGeo;

class UserController extends Controller
{
    public function index()
    {
        Log::info('User Management: index accessed', ['by' => Auth::user()->username]);
        $users = User::with('role')->whereNull('deleted_at')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create(Request $request)
    {
        $currentRole     = Auth::user()->role?->role_name;
        $preselectedRole = $request->query('role', '');

        $allowedRoleNames = match($currentRole) {
            'directress' => ['directress','admin','teacher','staff','guardian'],
            'admin'      => ['admin','teacher','staff','guardian'],
            'teacher'    => ['staff','guardian'],
            default      => ['guardian'],
        };

        $allowedRoles  = Role::whereIn('role_name', $allowedRoleNames)->get();
        $permissions   = Permission::orderBy('category')->orderBy('permission_name')->get();

        $rolePermsRaw = DB::table('role_permissions')
            ->join('roles','role_permissions.role_id','=','roles.role_id')
            ->select('roles.role_id','role_permissions.permission_id')
            ->get();

        $rolePermissions = [];
        foreach ($rolePermsRaw as $rp) {
            $rolePermissions[$rp->role_id][] = $rp->permission_id;
        }

        // view_audit_log permission ID (for auto-check)
        $viewAuditLogId = Permission::where('permission_name','view_audit_log')
            ->value('permission_id');

        $geo       = new PhilippinesGeo();
        $regions   = $geo->getRegions();
        $provinces = $geo->getProvinces('');
        $cities    = $geo->getCities('');

        return view('admin.users.create', compact(
            'allowedRoles','permissions','rolePermissions',
            'preselectedRole','regions','provinces','cities',
            'viewAuditLogId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id'          => 'required|exists:roles,role_id',
            'first_name'       => 'required|string|min:2|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|min:2|max:100',
            'sex'              => 'required|in:male,female,prefer_not_to_say,others',
            'sex_specify'      => 'nullable|string|max:100',
            'birthdate'        => 'nullable|date',
            'contact_number_1' => ['required','regex:/^09\d{9}$/'],
            'contact_number_2' => ['nullable','regex:/^09\d{9}$/'],
            'region'           => 'required|string|max:100',
            'province'         => 'required|string|max:100',
            'city'             => 'required|string|max:100',
            'barangay'         => 'required|string|min:4|max:100',
            'house_unit_no'    => 'required|string|min:1|max:100',
            'street'           => 'required|string|min:4|max:100',
            'zip_code'         => ['required','regex:/^\d{4}$/'],
            'email'            => 'required|email|unique:users,email',
            'username'         => 'required|string|min:4|max:50|unique:users,username',
            'password'         => 'required|string|min:6|confirmed',
            'profile_picture'  => 'nullable|image|mimes:jpg,jpeg,png|max:51200',
            'permissions'      => 'nullable|array',
        ]);

        $role = Role::find($request->role_id);

        if ($role && $role->role_name === 'guardian') {
            $request->validate(['relationship' => 'required|string']);
        }

        $picturePath = null;
        if ($request->hasFile('profile_picture')) {
            $picturePath = $request->file('profile_picture')
                ->store('profile_pictures/users','public');
        }

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
            'role_id'          => $request->role_id,
            'profile_picture'  => $picturePath,
            'is_active'        => 1,
        ]);

        if ($role && $role->role_name === 'guardian') {
            Guardian::create([
                'user_id'      => $user->user_id,
                'relationship' => $request->relationship,
            ]);
            $permIds = DB::table('role_permissions')
                ->where('role_id', $role->role_id)
                ->pluck('permission_id');
            foreach ($permIds as $permId) {
                DB::table('user_permissions')->insertOrIgnore([
                    'user_id'       => $user->user_id,
                    'permission_id' => $permId,
                ]);
            }
        } else {
            foreach ($request->input('permissions',[]) as $permId) {
                DB::table('user_permissions')->insertOrIgnore([
                    'user_id'       => $user->user_id,
                    'permission_id' => $permId,
                ]);
            }
        }

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'CREATE',
            'table_name' => 'users',
            'record_id'  => $user->user_id,
            'changes'    => json_encode(['username' => $user->username, 'role' => $role?->role_name]),
        ]);

        Log::info('User created', [
            'by'       => Auth::user()->username,
            'new_user' => $user->username,
            'role'     => $role?->role_name,
        ]);

        if ($role && $role->role_name === 'guardian') {
            return redirect()->route('admin.guardians.index')
                ->with('success','Guardian account created successfully.');
        }

        return redirect()->route('admin.users.index')
            ->with('success','User created successfully.');
    }

    public function show(string $id)
    {
        $user           = User::with(['role','permissions','guardian'])->findOrFail($id);
        $allPermissions = Permission::orderBy('category')->orderBy('permission_name')->get();

        Log::info('User Management: viewing user', [
            'by'      => Auth::user()->username,
            'user_id' => $id,
        ]);

        return view('admin.users.show', compact('user','allPermissions'));
    }

    public function edit(string $id)
    {
        $user         = User::with(['role','permissions','guardian'])->findOrFail($id);
        $currentRole  = Auth::user()->role?->role_name;
        $userRoleName = $user->role?->role_name;

        $allowedRoleNames = match($currentRole) {
            'directress' => ['directress','admin','teacher','staff','guardian'],
            'admin'      => ['admin','teacher','staff','guardian'],
            'teacher'    => ['staff','guardian'],
            default      => ['guardian'],
        };

        if ($userRoleName === 'guardian') {
            $allowedRoles = Role::where('role_name','guardian')->get();
        } else {
            $allowedRoles = Role::whereIn('role_name', $allowedRoleNames)
                ->where('role_name','!=','guardian')->get();
        }

        $permissions  = Permission::orderBy('category')->orderBy('permission_name')->get();

        $rolePermsRaw = DB::table('role_permissions')
            ->join('roles','role_permissions.role_id','=','roles.role_id')
            ->select('roles.role_id','role_permissions.permission_id')
            ->get();

        $rolePermissions = [];
        foreach ($rolePermsRaw as $rp) {
            $rolePermissions[$rp->role_id][] = $rp->permission_id;
        }

        $viewAuditLogId = Permission::where('permission_name','view_audit_log')
            ->value('permission_id');

        $geo       = new PhilippinesGeo();
        $regions   = $geo->getRegions();
        $provinces = $geo->getProvinces($user->region ?? '');
        $cities    = $geo->getCities($user->province ?? '');

        return view('admin.users.edit', compact(
            'user','userRoleName','allowedRoles','permissions',
            'rolePermissions','regions','provinces','cities',
            'viewAuditLogId'
        ));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name'       => 'required|string|min:2|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|min:2|max:100',
            'sex'              => 'required|in:male,female,prefer_not_to_say,others',
            'sex_specify'      => 'nullable|string|max:100',
            'birthdate'        => 'nullable|date',
            'contact_number_1' => ['required','regex:/^09\d{9}$/'],
            'contact_number_2' => ['nullable','regex:/^09\d{9}$/'],
            'region'           => 'required|string|max:100',
            'province'         => 'required|string|max:100',
            'city'             => 'required|string|max:100',
            'barangay'         => 'required|string|min:4|max:100',
            'house_unit_no'    => 'required|string|min:1|max:100',
            'street'           => 'required|string|min:4|max:100',
            'zip_code'         => ['required','regex:/^\d{4}$/'],
            'email'            => 'required|email|unique:users,email,'.$user->user_id.',user_id',
            'username'         => 'required|string|min:4|max:50|unique:users,username,'.$user->user_id.',user_id',
            'password'         => 'nullable|string|min:6|confirmed',
            'profile_picture'  => 'nullable|image|mimes:jpg,jpeg,png|max:51200',
            'permissions'      => 'nullable|array',
        ]);

        $role = $user->role;
        if ($role && $role->role_name === 'guardian') {
            $request->validate(['relationship' => 'required|string']);
        }

        $picturePath = $user->profile_picture;
        if ($request->hasFile('profile_picture')) {
            if ($picturePath) Storage::disk('public')->delete($picturePath);
            $picturePath = $request->file('profile_picture')
                ->store('profile_pictures/users','public');
        }

        $data = [
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
            'profile_picture'  => $picturePath,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($role && $role->role_name === 'guardian' && $user->guardian) {
            $user->guardian->update(['relationship' => $request->relationship]);
        }

        if ($role && $role->role_name !== 'guardian' && $request->has('permissions')) {
            DB::table('user_permissions')->where('user_id',$user->user_id)->delete();
            foreach ($request->input('permissions',[]) as $permId) {
                DB::table('user_permissions')->insertOrIgnore([
                    'user_id'       => $user->user_id,
                    'permission_id' => $permId,
                ]);
            }
        }

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'users',
            'record_id'  => $user->user_id,
            'changes'    => json_encode(['updated' => $user->username]),
        ]);

        Log::info('User updated', [
            'by'      => Auth::user()->username,
            'user_id' => $user->user_id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success','User updated successfully.');
    }

    public function toggle(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'users',
            'record_id'  => $user->user_id,
            'changes'    => json_encode(['is_active' => $user->is_active]),
        ]);

        Log::info('User toggled', [
            'by'        => Auth::user()->username,
            'user_id'   => $user->user_id,
            'is_active' => $user->is_active,
        ]);

        return back()->with('success','User status updated.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'DELETE',
            'table_name' => 'users',
            'record_id'  => $id,
            'changes'    => json_encode(['deleted' => $user->username]),
        ]);

        Log::info('User deleted', [
            'by'       => Auth::user()->username,
            'user_id'  => $id,
            'username' => $user->username,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success','User deleted successfully.');
    }
}