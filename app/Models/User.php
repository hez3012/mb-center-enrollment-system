<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'first_name', 'last_name', 'email',
        'username', 'password', 'is_active',
        'failed_attempts', 'locked_until', 'role_id',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'is_active'       => 'boolean',
        'locked_until'    => 'datetime',
        'failed_attempts' => 'integer',
    ];

    public function getRememberTokenName()
    {
        return null;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'user_permissions',
            'user_id',
            'permission_id',
            'user_id',
            'permission_id'
        );
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id', 'user_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->role_name === $roleName;
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->role_name, $roles);
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions->contains('permission_name', $permissionName);
    }
}