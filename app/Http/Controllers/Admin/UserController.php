<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AuditLog;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles       = Role::all();
        $permissions = Permission::orderBy('category')->orderBy('permission_name')->get();
        $currentRole = Auth::user()->role->role_name;

        $allowedRoles = match($currentRole) {
            'directress' => $roles->whereNotIn('role_name', ['directress']),
            default      => $roles->whereIn('role_name', ['teacher', 'staff', 'guardian']),
        };

        $rolePermissions = Role::with('permissions')->get()
            ->mapWithKeys(fn($role) => [
                $role->role_id => $role->permissions->pluck('permission_id')->toArray()
            ]);

        return view('admin.users.create', compact('allowedRoles', 'permissions', 'rolePermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'  => 'required|string|max:50',
            'last_name'   => 'required|string|max:50',
            'email'       => 'required|email|unique:users,email',
            'username'    => 'required|string|unique:users,username|max:50',
            'password'    => 'required|string|min:8|confirmed',
            'role_id'     => 'required|exists:roles,role_id',
            'permissions' => 'array',
        ]);

        $user = User::create([
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'email'           => $request->email,
            'username'        => $request->username,
            'password'        => Hash::make($request->password),
            'is_active'       => true,
            'failed_attempts' => 0,
            'role_id'         => $request->role_id,
        ]);

        if ($request->has('permissions')) {
            $user->permissions()->sync($request->permissions);
        }

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'CREATE',
            'table_name' => 'users',
            'record_id'  => $user->user_id,
            'changes'    => json_encode(['created_user' => $user->username]),
        ]);

        Log::info('New user created', [
            'created_by' => Auth::user()->username,
            'new_user'   => $user->username,
            'role'       => $user->role->role_name,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->username} created successfully.");
    }

    public function edit($id)
    {
        $user        = User::with(['role', 'permissions'])->findOrFail($id);
        $roles       = Role::all();
        $permissions = Permission::orderBy('category')->orderBy('permission_name')->get();
        $currentRole = Auth::user()->role->role_name;

        $allowedRoles = match($currentRole) {
            'directress' => $roles->whereNotIn('role_name', ['directress']),
            default      => $roles->whereIn('role_name', ['teacher', 'staff', 'guardian']),
        };

        $rolePermissions = Role::with('permissions')->get()
            ->mapWithKeys(fn($role) => [
                $role->role_id => $role->permissions->pluck('permission_id')->toArray()
            ]);

        return view('admin.users.edit', compact('user', 'allowedRoles', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name'  => 'required|string|max:50',
            'last_name'   => 'required|string|max:50',
            'email'       => 'required|email|unique:users,email,' . $id . ',user_id',
            'username'    => 'required|string|max:50|unique:users,username,' . $id . ',user_id',
            'password'    => 'nullable|string|min:8|confirmed',
            'role_id'     => 'required|exists:roles,role_id',
            'permissions' => 'array',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->username   = $request->username;
        $user->role_id    = $request->role_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->permissions()->sync($request->permissions ?? []);

        AuditLog::create([
            'user_id'    => Auth::user()->user_id,
            'action'     => 'UPDATE',
            'table_name' => 'users',
            'record_id'  => $user->user_id,
            'changes'    => json_encode(['updated_user' => $user->username]),
        ]);

        Log::info('User updated', [
            'updated_by' => Auth::user()->username,
            'user'       => $user->username,
        ]);

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

        Log::info("User {$status}", [
            'by'   => Auth::user()->username,
            'user' => $user->username,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->username} has been {$status}.");
    }
}