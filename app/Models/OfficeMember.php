<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeMember extends Model
{
    protected $fillable = [
        'office_id',
        'user_id',
        'specialty_id',
        'position',
        'office_role',
        'status',
        'approved_by',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function isManager(): bool
    {
        return in_array(
            $this->office_role,
            ['owner', 'manager'],
            true
        );
    }
}
