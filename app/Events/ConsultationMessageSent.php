<?php

namespace App\Events;

use App\Models\ConsultationMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsultationMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public ConsultationMessage $consultationMessage
    ) {
        $this->consultationMessage->loadMissing('sender');
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel(
                'consultation.'
                . $this->consultationMessage->consultation_id
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'consultation.message.sent';
    }

    public function broadcastWith(): array
    {
        $message = $this->consultationMessage;
        $sender = $message->sender;

        $extension = $message->attachment
            ? strtolower(pathinfo(
                $message->attachment,
                PATHINFO_EXTENSION
            ))
            : null;

        $isImage = in_array(
            $extension,
            ['jpg', 'jpeg', 'png', 'webp'],
            true
        );

        return [
            'message' => [
                'id' => $message->id,
                'consultation_id' => $message->consultation_id,
                'sender_id' => $message->sender_id,
                'sender_name' => $sender?->name ?? 'المستخدم',
                'sender_role' => $sender?->role,
                'sender_profile_photo_url' =>
                    $sender?->profile_photo
                        ? asset(
                            'storage/'
                            . $sender->profile_photo
                        )
                        : null,
                'body' => $message->message,
                'attachment_url' =>
                    $message->attachment
                        ? route(
                            'consultations.messages.attachment',
                            [
                                $message->consultation_id,
                                $message->id,
                            ]
                        )
                        : null,
                'attachment_name' =>
                    $message->attachment
                        ? basename($message->attachment)
                        : null,
                'attachment_extension' => $extension,
                'attachment_is_image' => $isImage,
                'created_at' =>
                    $message->created_at?->toISOString(),
                'time' =>
                    $message->created_at?->format('H:i'),
            ],
        ];
    }
}
