<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Student extends Model
{
    use SoftDeletes;

    protected $table      = 'student';
    protected $primaryKey = 'student_id';
    public $timestamps    = true;

    protected $fillable = [
        'first_name', 'middle_name', 'last_name',
        'birthdate', 'sex', 'sex_specify',
        'contact_number_1', 'contact_number_2',
        'region', 'province', 'city',
        'house_unit_no', 'street', 'barangay', 'zip_code',
        'address', 'status',
        'guardian_id', 'dev_ped_id', 'dev_ped_document', 'program_level_id',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardian_id', 'guardian_id');
    }

    public function disabilities()
    {
        return $this->belongsToMany(
            Disability::class,
            'student_disability',
            'student_id', 'disability_id',
            'student_id', 'disability_id'
        );
    }

    public function programLevel()
    {
        return $this->belongsTo(ProgramLevel::class, 'program_level_id', 'program_level_id');
    }

    public function developmentalPediatrician()
    {
        return $this->belongsTo(DevelopmentalPediatrician::class, 'dev_ped_id', 'dev_ped_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id', 'student_id');
    }

    public function getFullNameAttribute(): string
    {
        $mi = $this->middle_name
            ? ' ' . strtoupper(substr($this->middle_name, 0, 1)) . '. '
            : ' ';
        return $this->first_name . $mi . $this->last_name;
    }

    public function getListNameAttribute(): string
    {
        $mi = $this->middle_name
            ? ' ' . strtoupper(substr($this->middle_name, 0, 1)) . '.'
            : '';
        return $this->last_name . ', ' . $this->first_name . $mi;
    }

    public function getMiddleInitialAttribute(): string
    {
        return $this->middle_name
            ? strtoupper(substr($this->middle_name, 0, 1)) . '.'
            : '';
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birthdate ? Carbon::parse($this->birthdate)->age : null;
    }

    public function getSexDisplayAttribute(): string
    {
        return match($this->sex) {
            'male'              => 'Male',
            'female'            => 'Female',
            'others'            => 'Others' . ($this->sex_specify ? ' (' . $this->sex_specify . ')' : ''),
            'prefer_not_to_say' => 'Prefer not to say',
            default             => ucfirst($this->sex),
        };
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->house_unit_no,
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
            $this->region,
            $this->zip_code,
        ])->filter()->implode(', ');
    }
}