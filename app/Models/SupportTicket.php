<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'assigned_employee_id',
        'is_escalated',
        'escalated_by',
        'escalated_at',
        'escalation_reason',
        'subject',
        'priority',
        'status',
        'last_message_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'is_escalated' => 'boolean',
        'last_message_at' => 'datetime',
        'escalated_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_employee_id'
        );
    }

    public function escalatedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'escalated_by'
        );
    }

    public function messages(): HasMany
    {
        return $this->hasMany(
            SupportMessage::class,
            'support_ticket_id'
        );
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(
            SupportMessage::class,
            'support_ticket_id'
        )->latestOfMany();
    }

    public function scopeVisibleTo(
        Builder $query,
        User $user
    ): Builder {
        if ($user->role === 'admin') {
            return $query->where(
                'is_escalated',
                true
            );
        }

        return $query->where(
            function (Builder $builder) use ($user) {
                $builder
                    ->where('user_id', $user->id)
                    ->orWhere(
                        'assigned_employee_id',
                        $user->id
                    );
            }
        );
    }

    public function isOpen(): bool
    {
        return in_array(
            $this->status,
            [
                'open',
                'in_progress',
            ],
            true
        );
    }
}
