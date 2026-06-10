<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $program_level_id
 * @property string $program_name
 * @property string|null $description
 */
class ProgramLevel extends Model
{
    protected $table      = 'program_level';
    protected $primaryKey = 'program_level_id';

    public $timestamps = false;

    protected $fillable = [
        'program_name',
        'description',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'program_level_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'program_level_id');
    }
}