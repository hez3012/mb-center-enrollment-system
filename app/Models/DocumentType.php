<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $document_type_id
 * @property string      $document_name
 * @property bool        $is_required
 */
class DocumentType extends Model
{
    protected $table      = 'document_type';
    protected $primaryKey = 'document_type_id';

    public $timestamps = false;

    protected $fillable = [
        'document_name',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];
}