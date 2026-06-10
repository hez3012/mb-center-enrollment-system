<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int         $dev_ped_id
 * @property string      $first_name
 * @property string      $last_name
 * @property string      $clinic_hospital
 * @property string      $contact_number
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read string $name
 */
class DevelopmentalPediatrician extends Model
{
    use SoftDeletes;

    protected $table      = 'developmental_pediatrician';
    protected $primaryKey = 'dev_ped_id';

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'clinic_hospital',
        'contact_number',
    ];

    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'dev_ped_id');
    }
}