<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $log_id
 * @property int         $user_id
 * @property string      $action
 * @property string      $table_name
 * @property int|null    $record_id
 * @property string|null $changes
 * @property string|null $timestamp
 * @property-read \App\Models\User|null $user
 */
class AuditLog extends Model
{
    protected $table      = 'audit_log';
    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'changes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}