<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'message_type',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'audio_duration',
        'edited_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'attachment_size' => 'integer',
            'audio_duration' => 'integer',
            'edited_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | المحادثة
    |--------------------------------------------------------------------------
    */

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(
            Conversation::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | المرسل
    |--------------------------------------------------------------------------
    */

    public function sender(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'sender_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | هل الرسالة صوتية؟
    |--------------------------------------------------------------------------
    */

    public function isVoice(): bool
    {
        return $this->message_type === 'voice';
    }

    /*
    |--------------------------------------------------------------------------
    | هل الرسالة صورة؟
    |--------------------------------------------------------------------------
    */

    public function isImage(): bool
    {
        return $this->message_type === 'image';
    }

    /*
    |--------------------------------------------------------------------------
    | هل الرسالة ملف؟
    |--------------------------------------------------------------------------
    */

    public function isFile(): bool
    {
        return $this->message_type === 'file';
    }

    /*
    |--------------------------------------------------------------------------
    | هل الرسالة محذوفة؟
    |--------------------------------------------------------------------------
    */

    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | حذف الرسالة منطقيًا
    |--------------------------------------------------------------------------
    */

    public function markAsDeleted(): void
    {
        $this->forceFill([
            'message' => null,
            'attachment_path' => null,
            'attachment_name' => null,
            'attachment_mime' => null,
            'attachment_size' => null,
            'audio_duration' => null,
            'deleted_at' => now(),
        ])->save();
    }
}
