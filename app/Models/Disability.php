<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disability extends Model
{
    use SoftDeletes;

    protected $table      = 'disability';
    protected $primaryKey = 'disability_id';
    public $timestamps    = false;

    protected $fillable = ['disability_name', 'description'];

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'student_disability',
            'disability_id',
            'student_id',
            'disability_id',
            'student_id'
        );
    }
}