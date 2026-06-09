<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'first_name', 'middle_name', 'last_name',
        'birthdate', 'contact_number_1', 'contact_number_2',
        'region', 'province', 'city',
        'house_unit_no', 'street', 'barangay', 'zip_code',
        'email', 'username', 'password',
        'is_active', 'failed_attempts', 'locked_until', 'role_id',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'is_active'       => 'boolean',
        'locked_until'    => 'datetime',
        'failed_attempts' => 'integer',
        'birthdate'       => 'date',
    ];

    public function getRememberTokenName() { return null; }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'user_permissions',
            'user_id', 'permission_id',
            'user_id', 'permission_id'
        );
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id', 'user_id');
    }

    public function guardian()
    {
        return $this->hasOne(Guardian::class, 'user_id', 'user_id');
    }

    // "Juan D. Dela Cruz"
    public function getFullNameAttribute(): string
    {
        $mi = $this->middle_name
            ? ' ' . strtoupper(substr($this->middle_name, 0, 1)) . '. '
            : ' ';
        return $this->first_name . $mi . $this->last_name;
    }

    // "Dela Cruz, Juan D."
    public function getListNameAttribute(): string
    {
        $mi = $this->middle_name
            ? ' ' . strtoupper(substr($this->middle_name, 0, 1)) . '.'
            : '';
        return $this->last_name . ', ' . $this->first_name . $mi;
    }

    // "D."
    public function getMiddleInitialAttribute(): string
    {
        return $this->middle_name
            ? strtoupper(substr($this->middle_name, 0, 1)) . '.'
            : '';
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birthdate ? Carbon::parse($this->birthdate)->age : null;
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->house_unit_no,
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
            $this->region,
            $this->zip_code,
        ])->filter()->implode(', ');
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