<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $table      = 'student';
    protected $primaryKey = 'student_id';

    protected $fillable = [
        'first_name', 'last_name', 'middle_name',
        'birthdate', 'sex', 'sex_specify',
        'profile_picture',
        'contact_number_1', 'contact_number_2',
        'house_unit_no', 'street', 'barangay',
        'city', 'province', 'region', 'zip_code', 'address',
        'status',
        'guardian_id',
        'dev_ped_id', 'dev_ped_document',
        'service_type_id',
        'disability_id',
        'disability_other',
        'program_level_id',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────
    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardian_id', 'guardian_id');
    }

    public function programLevel()
    {
        return $this->belongsTo(ProgramLevel::class, 'program_level_id', 'program_level_id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'service_type_id');
    }

    public function disability()
    {
        return $this->belongsTo(Disability::class, 'disability_id', 'disability_id');
    }

    public function developmentalPediatrician()
    {
        return $this->belongsTo(
            DevelopmentalPediatrician::class, 'dev_ped_id', 'dev_ped_id'
        );
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id', 'student_id');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        $m = $this->middle_name
            ? ' ' . mb_strtoupper(mb_substr($this->middle_name, 0, 1)) . '.'
            : '';
        return trim($this->first_name . $m . ' ' . $this->last_name);
    }

    public function getMiddleInitialAttribute(): string
    {
        return $this->middle_name
            ? mb_strtoupper(mb_substr($this->middle_name, 0, 1)) . '.'
            : '';
    }

    public function getListNameAttribute(): string
    {
        $m = $this->middle_name
            ? ' ' . mb_strtoupper(mb_substr($this->middle_name, 0, 1)) . '.'
            : '';
        return trim($this->last_name . ', ' . $this->first_name . $m);
    }

    public function getAgeAttribute(): int
    {
        return $this->birthdate ? $this->birthdate->age : 0;
    }

    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->house_unit_no,
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
            $this->region,
            $this->zip_code,
        ]));
    }
}