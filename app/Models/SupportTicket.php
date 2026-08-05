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
        'category',
        'priority',
        'status',
        'support_mode',

        'bot_confidence',
        'bot_resolved',
        'transferred_to_employee_at',
        'first_response_at',

        'last_message_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'is_escalated' => 'boolean',
        'bot_resolved' => 'boolean',
        'bot_confidence' => 'decimal:4',

        'last_message_at' => 'datetime',
        'escalated_at' => 'datetime',
        'transferred_to_employee_at' => 'datetime',
        'first_response_at' => 'datetime',
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
            return $query;
        }

        if ($user->role === 'employee') {
            return $query->where(function (Builder $builder) use ($user) {
                $builder
                    ->where('assigned_employee_id', $user->id)
                    ->orWhere(function (Builder $query) {
                        $query
                            ->whereNull('assigned_employee_id')
                            ->where('support_mode', 'waiting_employee');
                    });
            });
        }

        return $query->where('user_id', $user->id);
    }

    public function isOpen(): bool
    {
        return in_array(
            $this->status,
            [
                'open',
                'in_progress',
                'waiting_customer',
            ],
            true
        );
    }

    public function isHandledByBot(): bool
    {
        return $this->support_mode === 'bot';
    }

    public function isWaitingForEmployee(): bool
    {
        return $this->support_mode === 'waiting_employee';
    }

    public function isHandledByEmployee(): bool
    {
        return $this->support_mode === 'employee';
    }
}
