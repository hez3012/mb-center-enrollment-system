<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table      = 'document_type';
    protected $primaryKey = 'document_type_id';
    public    $timestamps = false;

    protected $fillable = [
        'document_name',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
    ];
}