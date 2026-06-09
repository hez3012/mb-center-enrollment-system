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
        'enrollment_date',
        'status',
        'enrollment_type',
        'waiver_signed',
        'rejection_reason',
        'remarks',
        'student_id',
        'school_year_id',
        'program_level_id',
        'processed_by',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'waiver_signed'   => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id', 'school_year_id');
    }

    public function programLevel()
    {
        return $this->belongsTo(ProgramLevel::class, 'program_level_id', 'program_level_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(EnrollmentDocument::class, 'enrollment_id', 'enrollment_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'           => 'warning',
            'pending_payment'   => 'info',
            'payment_confirmed' => 'primary',
            'enrolled'          => 'success',
            'rejected'          => 'danger',
            'withdrawn'         => 'secondary',
            'completed'         => 'dark',
            default             => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'           => 'Pending Review',
            'pending_payment'   => 'Pending Payment',
            'payment_confirmed' => 'Payment Confirmed',
            'enrolled'          => 'Enrolled',
            'rejected'          => 'Rejected',
            'withdrawn'         => 'Withdrawn',
            'completed'         => 'Completed',
            default             => ucfirst($this->status),
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->enrollment_type === 'walk_in' ? 'Walk-in' : 'Online';
    }
}