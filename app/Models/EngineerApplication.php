<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineerApplication extends Model
{
    protected $fillable = [
        'user_id',
        'specialty_id',
        'certificate_file',
        'cv_file',
        'payment_receipt',
        'amount',
        'payment_status',
        'status',
        'admin_note',
        'application_type',
        'membership_days',
        'membership_started_at',
        'membership_expires_at',
        'approved_at',
    ];
protected $casts = [
    'amount' => 'decimal:2',
    'membership_days' => 'integer',
    'membership_started_at' => 'datetime',
    'membership_expires_at' => 'datetime',
    'approved_at' => 'datetime',
];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(
            EngineeringSpecialty::class,
            'specialty_id'
        );
    }
}
