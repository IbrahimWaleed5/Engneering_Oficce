<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content_moderation_id',
        'warning_number',
        'category',
        'reason',
        'issued_by_type',
        'issued_by',
        'status',
        'account_suspended',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'cancelled_at',
    ];

    protected $casts = [
        'warning_number' => 'integer',
        'account_suspended' => 'boolean',

        'reviewed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderation(): BelongsTo
    {
        return $this->belongsTo(
            ContentModeration::class,
            'content_moderation_id'
        );
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'issued_by'
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |--------------------------------------------------------------------------
     */

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            [
                'active',
                'confirmed',
            ]
        );
    }

    public function scopeCancelled(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'cancelled'
        );
    }

    public function scopeAppealed(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'appealed'
        );
    }

    /*
     |--------------------------------------------------------------------------
     | Helpers
     |--------------------------------------------------------------------------
     */

    public function isActive(): bool
    {
        return in_array(
            $this->status,
            [
                'active',
                'confirmed',
            ],
            true
        );
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function causedSuspension(): bool
    {
        return $this->account_suspended;
    }
}
