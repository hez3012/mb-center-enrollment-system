<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $table      = 'service_type';
    protected $primaryKey = 'service_type_id';
    public $timestamps    = false;

    protected $fillable = ['service_name'];

    public function disabilities()
    {
        return $this->hasMany(Disability::class, 'service_type_id', 'service_type_id');
    }
}