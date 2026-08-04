<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeApplication extends Model
{
    protected $fillable = [
        'user_id',
        'office_name',
        'email',
        'phone',
        'commercial_registration',
        'license_number',
        'country',
        'city',
        'address',
        'notes',
        'commercial_registration_path',
        'license_document_path',
        'payment_method',
        'payment_reference',
        'payment_receipt_path',
        'paid_at',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }
}
