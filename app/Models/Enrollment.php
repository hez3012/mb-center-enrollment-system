<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $table      = 'enrollment';
    protected $primaryKey = 'enrollment_id';

    protected $fillable = [
        'enrollment_date', 'status', 'enrollment_type',
        'waiver_signed', 'student_id', 'school_year_id',
        'program_level_id', 'processed_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function programLevel()
    {
        return $this->belongsTo(ProgramLevel::class, 'program_level_id', 'program_level_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }
}