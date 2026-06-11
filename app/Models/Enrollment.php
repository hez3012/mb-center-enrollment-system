<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                 $enrollment_id
 * @property int                 $student_id
 * @property int                 $school_year_id
 * @property int                 $program_level_id
 * @property \Carbon\Carbon      $enrollment_date
 * @property string              $enrollment_type
 * @property string              $status
 * @property bool                $waiver_signed
 * @property string|null         $rejection_reason
 * @property string|null         $remarks
 * @property int|null            $processed_by
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read string         $type_label
 * @property-read string         $status_label
 * @property-read string         $status_badge
 */
class Enrollment extends Model
{
    use SoftDeletes;

    protected $table      = 'enrollment';
    protected $primaryKey = 'enrollment_id';

    protected $fillable = [
        'student_id',
        'school_year_id',
        'program_level_id',
        'enrollment_date',
        'enrollment_type',
        'status',
        'waiver_signed',
        'rejection_reason',
        'remarks',
        'processed_by',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'waiver_signed'   => 'boolean',
        'deleted_at'      => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getTypeLabelAttribute(): string
    {
        return match($this->enrollment_type) {
            'walk_in' => 'Walk-in',
            'online'  => 'Online',
            default   => ucfirst((string) ($this->enrollment_type ?? '')),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'           => 'Pending Review',
            'pending_payment'   => 'Pending Payment',
            'payment_confirmed' => 'Payment Confirmed',
            'enrolled'          => 'Enrolled — Payment Confirmed',
            'rejected'          => 'Rejected',
            'withdrawn'         => 'Withdrawn',
            'completed'         => 'Completed',
            default             => ucfirst((string) ($this->status ?? '')),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'           => 'warning text-dark',
            'pending_payment'   => 'info text-dark',
            'payment_confirmed' => 'primary',
            'enrolled'          => 'success',
            'rejected'          => 'danger',
            'withdrawn'         => 'secondary',
            'completed'         => 'dark',
            default             => 'secondary',
        };
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

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
        return $this->hasMany(
            EnrollmentDocument::class,
            'enrollment_id',
            'enrollment_id'
        );
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'enrollment_id', 'enrollment_id');
    }
}