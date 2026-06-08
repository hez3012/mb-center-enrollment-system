<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'role_id';
    public $timestamps = false;

    protected $fillable = ['role_name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id',
            'role_id',
            'permission_id'
        );
    }
}