<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolYear extends Model
{
    use SoftDeletes;

    protected $table      = 'school_year';
    protected $primaryKey = 'school_year_id';
    public $timestamps    = false;

    protected $fillable = ['year_label', 'start_date', 'end_date'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'school_year_id', 'school_year_id');
    }

    public static function current(): ?self
    {
        return static::whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first()
            ?? static::orderByDesc('start_date')->first();
    }
}