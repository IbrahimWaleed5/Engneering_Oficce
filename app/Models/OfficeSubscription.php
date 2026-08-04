<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeSubscription extends Model
{
    protected $fillable = [
        'office_id',
        'amount',
        'currency',
        'duration_value',
        'duration_unit',
        'starts_at',
        'ends_at',
        'status',
        'payment_method',
        'payment_reference',
        'receipt_path',
        'paid_at',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'duration_value' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function durationLabel(): string
    {
        $unit = match ($this->duration_unit) {
            'day' => $this->duration_value === 1 ? 'يوم' : 'أيام',
            'month' => $this->duration_value === 1 ? 'شهر' : 'أشهر',
            'year' => $this->duration_value === 1 ? 'سنة' : 'سنوات',
            default => 'مدة',
        };

        return $this->duration_value . ' ' . $unit;
    }
}
