<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $disability_id
 * @property string      $disability_name
 * @property string|null $description
 */
class Disability extends Model
{
    protected $table      = 'disability';
    protected $primaryKey = 'disability_id';

    public $timestamps = false;

    protected $fillable = [
        'disability_name',
        'description',
    ];

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'student_disability',
            'disability_id',
            'student_id'
        );
    }
}