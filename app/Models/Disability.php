<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disability extends Model
{
    protected $table      = 'disability';
    protected $primaryKey = 'disability_id';
    public $timestamps    = false;

    protected $fillable = ['service_type_id', 'disability_name'];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'service_type_id');
    }
}