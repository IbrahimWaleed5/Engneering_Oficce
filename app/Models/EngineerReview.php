<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineerReview extends Model
{
    protected $fillable = [
        'consultation_id',
        'customer_id',
        'engineer_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
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

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'engineer_id'
        );
    }
}
