<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolYear extends Model
{
    use SoftDeletes;

    protected $table      = 'school_year';
    protected $primaryKey = 'school_year_id';
    public    $timestamps = false;

    protected $fillable = [
        'year_label',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Return the currently active school year.
     */
    public static function current(): ?self
    {
        return static::where('is_active', 1)->first();
    }
}