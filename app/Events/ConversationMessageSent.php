<?php

namespace App\Events;

use App\Models\ConversationMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public ConversationMessage $conversationMessage
    ) {
        $this->conversationMessage->loadMissing(
            'sender:id,name,role,profile_photo'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | قناة المحادثة
    |--------------------------------------------------------------------------
    */

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel(
                'conversation.'
                . $this->conversationMessage->conversation_id
            ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | اسم الحدث داخل JavaScript
    |--------------------------------------------------------------------------
    */

    public function broadcastAs(): string
    {
        return 'conversation.message.sent';
    }

    /*
    |--------------------------------------------------------------------------
    | بيانات الرسالة المرسلة
    |--------------------------------------------------------------------------
    */

    public function broadcastWith(): array
    {
        $message = $this->conversationMessage;

        return [
            'message' => [
                'id' => $message->id,

                'conversation_id' =>
                    $message->conversation_id,

                'sender_id' =>
                    $message->sender_id,

                'sender_name' =>
                    $message->sender?->name,

                'sender_role' =>
                    $message->sender?->role,

                'sender_profile_photo_url' =>
                    $message->sender?->profile_photo
                        ? asset(
                            'storage/'
                            . $message->sender->profile_photo
                        )
                        : null,

                'body' =>
                    $message->message,

                'message_type' =>
                    $message->message_type,

                'attachment_name' =>
                    $message->attachment_name,

                'attachment_mime' =>
                    $message->attachment_mime,

                'attachment_size' =>
                    $message->attachment_size,

                'audio_duration' =>
                    $message->audio_duration,

                'attachment_url' =>
                    $message->attachment_path
                        ? route(
                            'conversations.messages.attachment',
                            [
                                'conversation' =>
                                    $message->conversation_id,

                                'message' =>
                                    $message->id,
                            ]
                        )
                        : null,

                'time' =>
                    $message->created_at?->format(
                        'H:i'
                    ),

                'created_at' =>
                    $message->created_at?->toISOString(),
            ],
        ];
    }
}
