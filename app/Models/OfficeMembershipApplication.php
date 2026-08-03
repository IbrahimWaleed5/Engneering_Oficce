<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeMembershipApplication extends Model
{
    protected $fillable = [
        'office_id',
        'engineer_id',
        'specialty_id',
        'requested_position',
        'years_of_experience',
        'cv_path',
        'certificate_path',
        'message',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'engineer_id'
        );
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(
            EngineeringSpecialty::class,
            'specialty_id'
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
