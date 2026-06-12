<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthLog extends Model
{
    protected $table      = 'auth_log';
    protected $primaryKey = 'auth_log_id';
    public $timestamps    = false;

    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}