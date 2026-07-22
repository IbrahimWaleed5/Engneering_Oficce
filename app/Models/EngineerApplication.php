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
