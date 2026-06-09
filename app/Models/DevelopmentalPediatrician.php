<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevelopmentalPediatrician extends Model
{
    use SoftDeletes;

    protected $table      = 'developmental_pediatrician';
    protected $primaryKey = 'dev_ped_id';
    public $timestamps    = false;

    protected $fillable = [
        'first_name', 'last_name',
        'clinic_hospital', 'contact_number',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'dev_ped_id', 'dev_ped_id');
    }

    public function getFullNameAttribute()
    {
        return 'Dr. ' . $this->first_name . ' ' . $this->last_name;
    }
}