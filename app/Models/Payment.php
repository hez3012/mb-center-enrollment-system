<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                 $payment_id
 * @property int                 $enrollment_id
 * @property float               $amount
 * @property \Carbon\Carbon      $payment_date
 * @property string              $payment_method
 * @property string              $or_number
 * @property string|null         $notes
 * @property int                 $recorded_by
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Payment extends Model
{
    use SoftDeletes;

    protected $table      = 'payment';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'enrollment_id',
        'amount',
        'payment_date',
        'payment_method',
        'or_number',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
        'deleted_at'   => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }

    // -------------------------------------------------------------------------
    // Accessor — human-readable payment method label
    // -------------------------------------------------------------------------

    public function getMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash'          => 'Cash',
            'gcash'         => 'GCash',
            'bank_transfer' => 'Bank Transfer',
            default         => 'Other',
        };
    }
}