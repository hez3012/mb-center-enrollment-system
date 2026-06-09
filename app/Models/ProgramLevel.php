<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramLevel extends Model
{
    use SoftDeletes;

    protected $table      = 'program_level';
    protected $primaryKey = 'program_level_id';
    public $timestamps    = false;

    protected $fillable = ['program_name', 'description', 'max_capacity'];

    public function students()
    {
        return $this->hasMany(Student::class, 'program_level_id', 'program_level_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'program_level_id', 'program_level_id');
    }
}