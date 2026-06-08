<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'permission_id';
    public $timestamps = false;

    protected $fillable = ['permission_name', 'category', 'description'];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_permissions',
            'permission_id',
            'role_id',
            'permission_id',
            'role_id'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_permissions',
            'permission_id',
            'user_id',
            'permission_id',
            'user_id'
        );
    }
}