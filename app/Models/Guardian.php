<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Guardian extends Model
{
    use SoftDeletes;

    protected $table      = 'guardian';
    protected $primaryKey = 'guardian_id';
    public $timestamps    = true;

    protected $fillable = [
        'user_id', 'first_name', 'middle_name', 'last_name',
        'contact_number', 'relationship', 'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'guardian_id', 'guardian_id');
    }

    // "Juan D. Dela Cruz"
    public function getFullNameAttribute(): string
    {
        $mi = $this->middle_name
            ? ' ' . strtoupper(substr($this->middle_name, 0, 1)) . '. '
            : ' ';
        return $this->first_name . $mi . $this->last_name;
    }

    // "Dela Cruz, Juan D."
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
}