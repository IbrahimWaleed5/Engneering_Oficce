<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConversationFileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | عرض أو تشغيل المرفق
    |--------------------------------------------------------------------------
    |
    | التسجيلات الصوتية والصور تُعرض داخل المتصفح.
    | الملفات الأخرى تُفتح أو تُحمّل حسب دعم المتصفح.
    |
    */

    public function show(
        Request $request,
        Conversation $conversation,
        ConversationMessage $message
    ): StreamedResponse|BinaryFileResponse {
        $this->authorize(
            'downloadAttachment',
            $conversation
        );

        $this->ensureMessageBelongsToConversation(
            $conversation,
            $message
        );

        abort_unless(
            $message->attachment_path,
            404,
            'لا يوجد مرفق لهذه الرسالة.'
        );

        $disk = Storage::disk('private');

        abort_unless(
            $disk->exists($message->attachment_path),
            404,
            'الملف غير موجود.'
        );

        $fileName = $this->safeFileName(
            $message
        );

        $mimeType = $message->attachment_mime
            ?: $disk->mimeType(
                $message->attachment_path
            )
            ?: 'application/octet-stream';

        return $disk->response(
            $message->attachment_path,
            $fileName,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' =>
                    $this->contentDisposition(
                        $message,
                        $fileName
                    ),

                'Accept-Ranges' => 'bytes',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' =>
                    'private, no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | تحميل المرفق إجباريًا
    |--------------------------------------------------------------------------
    */

    public function download(
        Request $request,
        Conversation $conversation,
        ConversationMessage $message
    ): StreamedResponse {
        $this->authorize(
            'downloadAttachment',
            $conversation
        );

        $this->ensureMessageBelongsToConversation(
            $conversation,
            $message
        );

        abort_unless(
            $message->attachment_path,
            404,
            'لا يوجد مرفق لهذه الرسالة.'
        );

        $disk = Storage::disk('private');

        abort_unless(
            $disk->exists($message->attachment_path),
            404,
            'الملف غير موجود.'
        );

        return $disk->download(
            $message->attachment_path,
            $this->safeFileName($message),
            [
                'Content-Type' =>
                    $message->attachment_mime
                    ?: 'application/octet-stream',

                'X-Content-Type-Options' =>
                    'nosniff',

                'Cache-Control' =>
                    'private, no-store, no-cache, must-revalidate',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | التأكد من تبعية الرسالة للمحادثة
    |--------------------------------------------------------------------------
    */

    private function ensureMessageBelongsToConversation(
        Conversation $conversation,
        ConversationMessage $message
    ): void {
        abort_unless(
            (int) $message->conversation_id
                === (int) $conversation->id,
            404
        );

        abort_if(
            $message->isDeleted(),
            404,
            'هذه الرسالة محذوفة.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | اسم ملف آمن
    |--------------------------------------------------------------------------
    */

    private function safeFileName(
        ConversationMessage $message
    ): string {
        $name = trim(
            (string) $message->attachment_name
        );

        if ($name === '') {
            $extension = pathinfo(
                (string) $message->attachment_path,
                PATHINFO_EXTENSION
            );

            $name = 'attachment-' . $message->id;

            if ($extension !== '') {
                $name .= '.' . $extension;
            }
        }

        return str_replace(
            [
                '/',
                '\\',
                "\0",
                "\r",
                "\n",
            ],
            '-',
            $name
        );
    }

    /*
    |--------------------------------------------------------------------------
    | طريقة عرض المرفق
    |--------------------------------------------------------------------------
    */

    private function contentDisposition(
        ConversationMessage $message,
        string $fileName
    ): string {
        $inlineTypes = [
            'voice',
            'image',
        ];

        $disposition = in_array(
            $message->message_type,
            $inlineTypes,
            true
        )
            ? 'inline'
            : 'attachment';

        $asciiName = preg_replace(
            '/[^\x20-\x7E]/',
            '_',
            $fileName
        ) ?: 'attachment';

        return sprintf(
            '%s; filename="%s"; filename*=UTF-8\'\'%s',
            $disposition,
            addcslashes($asciiName, '"\\'),
            rawurlencode($fileName)
        );
    }
}
