<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'payment_id',
        'consultation_id',
        'customer_id',
        'consultation_number',
        'customer_name',
        'service_name',
        'engineer_name',
        'amount',
        'payment_method',
        'currency',
        'office_name',
        'office_logo',
        'issued_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            Payment::class
        );
    }

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
}
