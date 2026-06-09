<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnrollmentDocument extends Model
{
    use SoftDeletes;

    protected $table      = 'enrollment_document';
    protected $primaryKey = 'enrollment_doc_id';
    public $timestamps    = false;

    protected $fillable = [
        'enrollment_id',
        'document_type_id',
        'submission_status',
        'file_path',
        'submission_date',
        'notes',
    ];

    protected $casts = [
        'submission_date' => 'date',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id', 'enrollment_id');
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id', 'document_type_id');
    }
}