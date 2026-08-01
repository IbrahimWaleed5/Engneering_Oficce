<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'consultation_id',
        'created_by',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | الاستشارة المرتبطة
    |--------------------------------------------------------------------------
    */

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(
            Consultation::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | منشئ المحادثة
    |--------------------------------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | المشاركون
    |--------------------------------------------------------------------------
    */

    public function participants(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                User::class,
                'conversation_participants'
            )
            ->withPivot([
                'last_read_at',
                'is_muted',
            ])
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | سجلات المشاركين
    |--------------------------------------------------------------------------
    */

    public function participantRecords(): HasMany
    {
        return $this->hasMany(
            ConversationParticipant::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | الرسائل
    |--------------------------------------------------------------------------
    */

    public function messages(): HasMany
    {
        return $this
            ->hasMany(
                ConversationMessage::class
            )
            ->orderBy('created_at');
    }

    /*
    |--------------------------------------------------------------------------
    | آخر رسالة
    |--------------------------------------------------------------------------
    */

    public function latestMessage()
    {
        return $this
            ->hasOne(
                ConversationMessage::class
            )
            ->latestOfMany();
    }

    /*
    |--------------------------------------------------------------------------
    | هل المستخدم مشارك؟
    |--------------------------------------------------------------------------
    */

    public function hasParticipant(
        int $userId
    ): bool {
        return $this
            ->participants()
            ->where(
                'users.id',
                $userId
            )
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | المستخدم الآخر
    |--------------------------------------------------------------------------
    */

    public function otherParticipant(
        int $currentUserId
    ): ?User {
        return $this
            ->participants
            ->first(
                fn (User $user) =>
                    (int) $user->id
                    !== $currentUserId
            );
    }

    /*
    |--------------------------------------------------------------------------
    | تحديث آخر نشاط
    |--------------------------------------------------------------------------
    */

    public function touchLastMessage(): void
    {
        $this->forceFill([
            'last_message_at' => now(),
        ])->save();
    }
}
