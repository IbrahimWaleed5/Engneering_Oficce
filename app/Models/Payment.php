<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'customer_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'receipt_image',
        'status',
        'paid_at',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function consultation()
    {
        return $this->belongsTo(
            Consultation::class
        );
    }

    public function customer()
    {
        return $this->belongsTo(
            User::class,
            'customer_id'
        );
    }
}
