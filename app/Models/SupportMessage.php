<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    protected $fillable = [
        'support_ticket_id',
        'sender_id',
        'sender_type',
        'message',
        'message_type',
        'is_internal',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'read_at',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(
            SupportTicket::class,
            'support_ticket_id'
        );
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'sender_id'
        );
    }

    public function hasAttachment(): bool
    {
        return filled($this->attachment_path);
    }

    public function isFromBot(): bool
    {
        return $this->sender_type === 'bot';
    }

    public function isFromCustomer(): bool
    {
        return $this->sender_type === 'customer';
    }

    public function isFromEmployee(): bool
    {
        return in_array(
            $this->sender_type,
            ['employee', 'admin'],
            true
        );
    }
}
