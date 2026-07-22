<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'consultation_id',
        'customer_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'receipt_image',
        'status',
        'rejection_reason',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(
            Consultation::class
        );
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'customer_id'
        );
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(
            Invoice::class
        );
    }
}
