<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailTwoFactorCodeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $code,
        public readonly int $minutes = 10
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('رمز التحقق بخطوتين - الوليد الهندسي')
            ->greeting('مرحبًا ' . $notifiable->name)
            ->line('رمز التحقق الخاص بتسجيل الدخول هو:')
            ->line($this->code)
            ->line("ينتهي هذا الرمز خلال {$this->minutes} دقائق.")
            ->line('إذا لم تحاول تسجيل الدخول، غيّر كلمة المرور وتواصل مع الدعم الفني.')
            ->salutation('الوليد الهندسي');
    }
}
