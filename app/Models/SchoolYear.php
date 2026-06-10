<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $school_year_id
 * @property string      $year_label
 * @property string|null $start_date
 * @property string|null $end_date
 */
class SchoolYear extends Model
{
    protected $table      = 'school_year';
    protected $primaryKey = 'school_year_id';

    public $timestamps = false;

    protected $fillable = [
        'year_label',
        'start_date',
        'end_date',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'school_year_id');
    }
}