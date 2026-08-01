<?php

namespace App\Http\Controllers;

use App\Events\ConversationMessageSent;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ConversationMessageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | إرسال رسالة
    |--------------------------------------------------------------------------
    |
    | يدعم:
    | - رسالة نصية
    | - صورة
    | - ملف
    | - تسجيل صوتي
    |
    */

    public function store(
        Request $request,
        Conversation $conversation
    ): JsonResponse {
        $this->authorize(
            'sendMessage',
            $conversation
        );

        $validated = $request->validate(
            [
                'message' => [
                    'nullable',
                    'string',
                    'max:5000',
                    'required_without_all:attachment,voice_message',
                ],

                'attachment' => [
                    'nullable',
                    'file',
                    'mimes:pdf,jpg,jpeg,png,webp,dwg,doc,docx,xls,xlsx,zip',
                    'max:20480',
                    'required_without_all:message,voice_message',
                ],

                'voice_message' => [
                    'nullable',
                    'file',
                    'mimetypes:audio/webm,audio/ogg,audio/mpeg,audio/mp4,audio/x-m4a,audio/wav,audio/x-wav,video/webm',
                    'max:20480',
                    'required_without_all:message,attachment',
                ],

                'audio_duration' => [
                    'nullable',
                    'integer',
                    'min:1',
                    'max:600',
                    'required_with:voice_message',
                ],
            ],
            [
                'message.required_without_all' =>
                    'اكتب رسالة أو أرفق ملفًا أو سجّل رسالة صوتية.',

                'attachment.required_without_all' =>
                    'اكتب رسالة أو أرفق ملفًا أو سجّل رسالة صوتية.',

                'voice_message.required_without_all' =>
                    'اكتب رسالة أو أرفق ملفًا أو سجّل رسالة صوتية.',

                'voice_message.mimetypes' =>
                    'صيغة التسجيل الصوتي غير مدعومة.',

                'voice_message.max' =>
                    'حجم التسجيل الصوتي يجب ألا يتجاوز 20 ميجابايت.',

                'audio_duration.required_with' =>
                    'مدة التسجيل الصوتي مطلوبة.',

                'audio_duration.max' =>
                    'مدة التسجيل الصوتي يجب ألا تتجاوز 10 دقائق.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | حماية إضافية لمحادثات الاستشارة
        |--------------------------------------------------------------------------
        */

        if ($conversation->type === 'consultation') {
            $consultation = $conversation->consultation;

            if (! $consultation) {
                throw ValidationException::withMessages([
                    'conversation' =>
                        'الاستشارة المرتبطة بهذه المحادثة غير موجودة.',
                ]);
            }

            if ($consultation->payment_status !== 'paid') {
                throw ValidationException::withMessages([
                    'conversation' =>
                        'لا يمكن إرسال رسائل قبل تأكيد دفع الاستشارة.',
                ]);
            }
        }

        $message = DB::transaction(
            function () use (
                $request,
                $validated,
                $conversation
            ) {
                $messageType = 'text';
                $attachmentPath = null;
                $attachmentName = null;
                $attachmentMime = null;
                $attachmentSize = null;
                $audioDuration = null;

                /*
                |--------------------------------------------------------------------------
                | التسجيل الصوتي
                |--------------------------------------------------------------------------
                */

                if ($request->hasFile('voice_message')) {
                    $voiceFile = $request->file(
                        'voice_message'
                    );

                    $attachmentPath = $voiceFile->store(
                        'conversation-attachments/voice',
                        'private'
                    );

                    $attachmentName =
                        $voiceFile->getClientOriginalName();

                    $attachmentMime =
                        $voiceFile->getMimeType();

                    $attachmentSize =
                        $voiceFile->getSize();

                    $audioDuration =
                        (int) $validated['audio_duration'];

                    $messageType = 'voice';
                }

                /*
                |--------------------------------------------------------------------------
                | الملفات والصور
                |--------------------------------------------------------------------------
                */

                elseif ($request->hasFile('attachment')) {
                    $file = $request->file(
                        'attachment'
                    );

                    $attachmentPath = $file->store(
                        'conversation-attachments/files',
                        'private'
                    );

                    $attachmentName =
                        $file->getClientOriginalName();

                    $attachmentMime =
                        $file->getMimeType();

                    $attachmentSize =
                        $file->getSize();

                    $messageType = str_starts_with(
                        (string) $attachmentMime,
                        'image/'
                    )
                        ? 'image'
                        : 'file';
                }

                /*
                |--------------------------------------------------------------------------
                | إنشاء الرسالة
                |--------------------------------------------------------------------------
                */

                $message = ConversationMessage::create([
                    'conversation_id' =>
                        $conversation->id,

                    'sender_id' =>
                        $request->user()->id,

                    'message' =>
                        $validated['message'] ?? null,

                    'message_type' =>
                        $messageType,

                    'attachment_path' =>
                        $attachmentPath,

                    'attachment_name' =>
                        $attachmentName,

                    'attachment_mime' =>
                        $attachmentMime,

                    'attachment_size' =>
                        $attachmentSize,

                    'audio_duration' =>
                        $audioDuration,
                ]);

                /*
                |--------------------------------------------------------------------------
                | تحديث آخر نشاط
                |--------------------------------------------------------------------------
                */

                $conversation->forceFill([
                    'last_message_at' =>
                        $message->created_at ?? now(),
                ])->save();

                /*
                |--------------------------------------------------------------------------
                | تعليم المحادثة مقروءة للمرسل
                |--------------------------------------------------------------------------
                */

                $conversation
                    ->participantRecords()
                    ->where(
                        'user_id',
                        $request->user()->id
                    )
                    ->update([
                        'last_read_at' => now(),
                        'updated_at' => now(),
                    ]);

                return $message;
            }
        );

        $message->load(
            'sender:id,name,role,profile_photo'
        );

        /*
        |--------------------------------------------------------------------------
        | إرسال الحدث الفوري عبر Reverb
        |--------------------------------------------------------------------------
        */

        broadcast(
            new ConversationMessageSent($message)
        )->toOthers();

        return response()->json([
            'success' => true,
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
                                    $conversation->id,

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
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | حذف رسالة منطقيًا
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        Conversation $conversation,
        ConversationMessage $message
    ): JsonResponse {
        $this->authorize(
            'sendMessage',
            $conversation
        );

        abort_unless(
            (int) $message->conversation_id
                === (int) $conversation->id,
            404
        );

        abort_unless(
            (int) $message->sender_id
                === (int) $request->user()->id
                || $request->user()->role === 'admin',
            403,
            'غير مسموح بحذف هذه الرسالة.'
        );

        DB::transaction(
            function () use ($message) {
                if ($message->attachment_path) {
                    Storage::disk('private')->delete(
                        $message->attachment_path
                    );
                }

                $message->markAsDeleted();
            }
        );

        return response()->json([
            'success' => true,
            'message' =>
                'تم حذف الرسالة بنجاح.',
        ]);
    }
}
