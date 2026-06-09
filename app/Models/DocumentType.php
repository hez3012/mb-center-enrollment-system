<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use SoftDeletes;

    protected $table      = 'document_type';
    protected $primaryKey = 'document_type_id';
    public $timestamps    = false;

    protected $fillable = ['document_name', 'is_required'];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function enrollmentDocuments()
    {
        return $this->hasMany(EnrollmentDocument::class, 'document_type_id', 'document_type_id');
    }
}