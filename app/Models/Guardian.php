<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $table      = 'guardian';
    protected $primaryKey = 'guardian_id';

    protected $fillable = [
        'user_id',
        'relationship',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'guardian_id', 'guardian_id');
    }
}