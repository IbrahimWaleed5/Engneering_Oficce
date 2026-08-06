<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentModeration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source_type',
        'source_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'file_hash',
        'status',
        'decision',
        'risk_level',
        'detected_categories',
        'category_scores',
        'reason',
        'provider',
        'model',
        'provider_response',
        'warning_issued',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'final_decision',
        'processed_at',
    ];

    protected $casts = [
        'source_id' => 'integer',
        'file_size' => 'integer',

        'detected_categories' => 'array',
        'category_scores' => 'array',
        'provider_response' => 'array',

        'warning_issued' => 'boolean',

        'reviewed_at' => 'datetime',
        'processed_at' => 'datetime',
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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(
            UserWarning::class,
            'content_moderation_id'
        );
    }

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |--------------------------------------------------------------------------
     */

    public function scopePending(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'pending'
        );
    }

    public function scopeProcessing(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'processing'
        );
    }

    public function scopeCompleted(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'completed'
        );
    }

    public function scopeFailed(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'failed'
        );
    }

    public function scopeApproved(
        Builder $query
    ): Builder {
        return $query->where(
            'decision',
            'approved'
        );
    }

    public function scopeRejected(
        Builder $query
    ): Builder {
        return $query->where(
            'decision',
            'rejected'
        );
    }

    public function scopeNeedsReview(
        Builder $query
    ): Builder {
        return $query->where(
            'decision',
            'needs_review'
        );
    }

    public function scopeHighRisk(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'risk_level',
            [
                'high',
                'critical',
            ]
        );
    }

    /*
     |--------------------------------------------------------------------------
     | Helpers
     |--------------------------------------------------------------------------
     */

    public function isApproved(): bool
    {
        return $this->decision === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->decision === 'rejected';
    }

    public function needsReview(): bool
    {
        return $this->decision === 'needs_review';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isHighRisk(): bool
    {
        return in_array(
            $this->risk_level,
            [
                'high',
                'critical',
            ],
            true
        );
    }
}
