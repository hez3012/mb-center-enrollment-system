<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int              $student_id
 * @property string           $first_name
 * @property string|null      $middle_name
 * @property string           $last_name
 * @property \Carbon\Carbon|null $birthdate
 * @property string           $sex
 * @property string|null      $sex_specify
 * @property string           $status
 * @property string|null      $region
 * @property string|null      $province
 * @property string|null      $city
 * @property string|null      $barangay
 * @property string|null      $house_unit_no
 * @property string|null      $street
 * @property string|null      $zip_code
 * @property string|null      $profile_picture
 * @property string|null      $dev_ped_document
 * @property int|null         $guardian_id
 * @property int|null         $program_level_id
 * @property int|null         $dev_ped_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read string      $full_name
 * @property-read string      $list_name
 * @property-read string      $middle_initial
 * @property-read string      $full_address
 * @property-read int|null    $age
 * @property-read string      $sex_display
 * @property-read \App\Models\Guardian|null                $guardian
 * @property-read \App\Models\ProgramLevel|null            $programLevel
 * @property-read \App\Models\DevelopmentalPediatrician|null $devPed
 * @property-read \Illuminate\Database\Eloquent\Collection $disabilities
 */
class Student extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'student_id';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'sex',
        'sex_specify',
        'status',
        'profile_picture',
        'dev_ped_document',
        'region',
        'province',
        'city',
        'barangay',
        'house_unit_no',
        'street',
        'zip_code',
        'guardian_id',
        'program_level_id',
        'dev_ped_id',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    public function getListNameAttribute(): string
    {
        $mi = $this->middle_initial;
        return trim(implode(', ', array_filter([
            $this->last_name,
            $this->first_name . ($mi ? ' ' . $mi : ''),
        ])));
    }

    public function getMiddleInitialAttribute(): string
    {
        return $this->middle_name
            ? strtoupper(substr(trim((string) $this->middle_name), 0, 1)) . '.'
            : '';
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

    public function getAgeAttribute(): ?int
    {
        return $this->birthdate ? $this->birthdate->age : null;
    }

    public function getSexDisplayAttribute(): string
    {
        return match($this->sex) {
            'male'              => 'Male',
            'female'            => 'Female',
            'others'            => $this->sex_specify ?: 'Others',
            'prefer_not_to_say' => 'Prefer not to say',
            default             => ucfirst((string) ($this->sex ?? '')),
        };
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    public function programLevel()
    {
        return $this->belongsTo(ProgramLevel::class, 'program_level_id');
    }

    public function devPed()
    {
        return $this->belongsTo(DevelopmentalPediatrician::class, 'dev_ped_id');
    }

    public function disabilities()
    {
        return $this->belongsToMany(
            Disability::class,
            'student_disability',
            'student_id',
            'disability_id'
        );
    }
}