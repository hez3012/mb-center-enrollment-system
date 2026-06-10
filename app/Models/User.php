<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property int                 $user_id
 * @property string              $first_name
 * @property string|null         $middle_name
 * @property string              $last_name
 * @property \Carbon\Carbon|null $birthdate
 * @property string|null         $contact_number_1
 * @property string|null         $contact_number_2
 * @property string|null         $region
 * @property string|null         $province
 * @property string|null         $city
 * @property string|null         $barangay
 * @property string|null         $house_unit_no
 * @property string|null         $street
 * @property string|null         $zip_code
 * @property string              $email
 * @property string              $username
 * @property string              $password
 * @property int                 $role_id
 * @property string|null         $profile_picture
 * @property bool                $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read string         $full_name
 * @property-read string         $list_name
 * @property-read string         $middle_initial
 * @property-read string         $full_address
 * @property-read int|null       $age
 * @property-read \App\Models\Role|null                    $role
 * @property-read \Illuminate\Database\Eloquent\Collection $permissions
 * @property-read \App\Models\Guardian|null                $guardian
 */
class User extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'contact_number_1',
        'contact_number_2',
        'region',
        'province',
        'city',
        'barangay',
        'house_unit_no',
        'street',
        'zip_code',
        'email',
        'username',
        'password',
        'role_id',
        'profile_picture',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birthdate'  => 'date',
        'is_active'  => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    public function getListNameAttribute(): string
    {
        $mi = $this->middle_initial;
        return trim(implode(', ', array_filter([
            $this->last_name,
            $this->first_name . ($mi ? ' ' . $mi : ''),
        ])));
    }

    public function getMiddleInitialAttribute(): string
    {
        return $this->middle_name
            ? strtoupper(substr(trim((string) $this->middle_name), 0, 1)) . '.'
            : '';
    }

    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->house_unit_no,
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
            $this->region,
            $this->zip_code,
        ]));
    }

    public function getAgeAttribute(): ?int
    {
        $bd = $this->birthdate;
        if ($bd === null) {
            return null;
        }
        return $bd->age;
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'user_permissions',
            'user_id',
            'permission_id'
        );
    }

    public function guardian()
    {
        return $this->hasOne(Guardian::class, 'user_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains('permission_name', $permission);
    }

    // -------------------------------------------------------------------------
    // Auth interface overrides (custom primary key)
    // -------------------------------------------------------------------------

    public function getAuthIdentifierName(): string
    {
        return 'user_id';
    }

    public function getAuthIdentifier(): int
    {
        return (int) $this->user_id;
    }

    public function getAuthPassword(): string
    {
        return (string) $this->password;
    }

    public function getRememberToken(): string
    {
        return (string) ($this->remember_token ?? '');
    }

    public function setRememberToken($value): void
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}