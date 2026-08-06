<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModerationAppeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_warning_id',
        'status',
        'message',
        'attachment_path',
        'attachment_original_name',
        'attachment_mime_type',
        'admin_response',
        'reviewed_by',
        'reviewed_at',
        'resolved_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function warning(): BelongsTo
    {
        return $this->belongsTo(
            UserWarning::class,
            'user_warning_id'
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function scopePending(
        Builder $query
    ): Builder {
        return $query->whereIn('status', [
            'pending',
            'under_review',
        ]);
    }

    public function isPending(): bool
    {
        return in_array(
            $this->status,
            [
                'pending',
                'under_review',
            ],
            true
        );
    }
}
