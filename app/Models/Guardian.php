<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int              $guardian_id
 * @property int              $user_id
 * @property string|null      $relationship
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\User|null                          $user
 * @property-read \Illuminate\Database\Eloquent\Collection       $students
 * @property-read string                                         $full_name
 */
class Guardian extends Model
{
    use SoftDeletes;

    protected $table      = 'guardian';
    protected $primaryKey = 'guardian_id';

    protected $fillable = [
        'user_id',
        'relationship',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->user?->full_name ?? '';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'guardian_id');
    }
}