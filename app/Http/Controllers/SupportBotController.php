<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Services\SupportAssignmentService;
use App\Services\SupportBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupportBotController extends Controller
{
    /**
     * إجابة المساعد للزائر دون إنشاء تذكرة دعم.
     */
    public function guestAsk(
        Request $request,
        SupportBotService $botService
    ): JsonResponse {
        $data = $request->validate([
            'message' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $message = trim($data['message']);
        $normalized = Str::lower($message);

        $employeePhrases = [
            'موظف',
            'الدعم الفني',
            'خدمة العملاء',
            'شخص حقيقي',
            'حولني',
            'تحويل لموظف',
            'تواصل مع الدعم',
            'اكلم الدعم',
            'أكلم الدعم',
        ];

        if (Str::contains($normalized, $employeePhrases)) {
            return response()->json([
                'success' => true,
                'handled_by' => 'login_required',
                'requires_login' => true,
                'login_url' => route('login'),
                'register_url' => route('register'),
                'message' => [
                    'sender_type' => 'bot',
                    'message' =>
                        'لتحويلك إلى موظف الدعم، يجب تسجيل الدخول أولًا حتى نحفظ المحادثة ونربطها بحسابك.',
                    'created_at' => now()->toISOString(),
                ],
            ]);
        }

        $result = $botService->findAnswer($message);

        if ($result) {
            return response()->json([
                'success' => true,
                'handled_by' => 'bot',
                'message' => [
                    'sender_type' => 'bot',
                    'message' => $result['answer'],
                    'created_at' => now()->toISOString(),
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'handled_by' => 'bot',
            'show_login_hint' => true,
            'login_url' => route('login'),
            'register_url' => route('register'),
            'message' => [
                'sender_type' => 'bot',
                'message' =>
                    'لم أجد إجابة مؤكدة. أعد صياغة السؤال، أو سجّل الدخول للتواصل مع موظف الدعم.',
                'created_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * فتح محادثة الدعم الحالية أو إنشاء محادثة جديدة.
     */
    public function start(Request $request): JsonResponse
    {
        $user = $request->user();

        /*
         * نعيد استخدام أي تذكرة دعم غير مغلقة،
         * سواء كانت مع البوت أو الموظف.
         */
        $ticket = SupportTicket::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                'open',
                'in_progress',
                'waiting_customer',
            ])
            ->whereIn('support_mode', [
                'bot',
                'waiting_employee',
                'employee',
            ])
            ->latest('last_message_at')
            ->latest('id')
            ->first();

        if (! $ticket) {
            $ticket = DB::transaction(function () use ($user) {
                $ticket = SupportTicket::create([
                    'ticket_number' =>
                        $this->generateTicketNumber(),

                    'user_id' => $user->id,
                    'assigned_employee_id' => null,

                    'subject' => 'محادثة دعم جديدة',
                    'category' => 'technical',
                    'priority' => 'medium',

                    'status' => 'open',
                    'support_mode' => 'bot',

                    'bot_resolved' => false,
                    'last_message_at' => now(),
                ]);

                SupportMessage::create([
                    'support_ticket_id' => $ticket->id,
                    'sender_id' => null,
                    'sender_type' => 'bot',

                    'message' =>
                        'مرحبًا بك في دعم الوليد الهندسي. كيف يمكنني مساعدتك؟',

                    'message_type' => 'text',
                    'is_internal' => false,
                ]);

                return $ticket;
            });
        }

        $messages = $ticket
            ->messages()
            ->where('is_internal', false)
            ->with('sender:id,name')
            ->orderBy('id')
            ->get();

        /*
         * نعتبر رسائل الموظف والنظام مقروءة عند فتح العميل للمحادثة.
         */
        $ticket->messages()
            ->where('is_internal', false)
            ->whereNull('read_at')
            ->whereIn('sender_type', [
                'employee',
                'admin',
                'system',
                'bot',
            ])
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,

            'ticket' => $this->ticketPayload($ticket),

            'messages' => $messages,

            'last_message_id' =>
                (int) ($messages->max('id') ?? 0),
        ]);
    }

    /**
     * إرسال رسالة من العميل.
     */
    public function send(
        Request $request,
        SupportBotService $botService
    ): JsonResponse {
        $data = $request->validate([
            'ticket_id' => [
                'required',
                'integer',
                'exists:support_tickets,id',
            ],

            'message' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $ticket = SupportTicket::query()
            ->where('id', $data['ticket_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (in_array(
            $ticket->status,
            ['resolved', 'closed'],
            true
        )) {
            return response()->json([
                'success' => false,
                'message' => 'هذه المحادثة مغلقة.',
            ], 422);
        }

        $customerMessage = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => $request->user()->id,
            'sender_type' => 'customer',

            'message' => $data['message'],
            'message_type' => 'text',
            'is_internal' => false,
        ]);

        $ticket->update([
            'last_message_at' => now(),

            /*
             * بعد رد العميل على الموظف تصبح التذكرة
             * قيد المعالجة بدل انتظار العميل.
             */
            'status' =>
                $ticket->support_mode === 'employee'
                    ? 'in_progress'
                    : $ticket->status,
        ]);

        /*
         * إذا تحولت التذكرة إلى موظف،
         * نحفظ الرسالة فقط ولا نجعل البوت يرد.
         */
        if ($ticket->support_mode !== 'bot') {
            return response()->json([
                'success' => true,
                'handled_by' => 'employee',

                /*
                 * لا نعيد كائن الرسالة حتى لا تظهر
                 * مرتين في واجهة العميل.
                 */
                'customer_message_id' =>
                    $customerMessage->id,

                'ticket' =>
                    $this->ticketPayload(
                        $ticket->fresh()
                    ),

                'notice' =>
                    $ticket->support_mode === 'waiting_employee'
                        ? 'تم إرسال رسالتك، والتذكرة بانتظار موظف الدعم.'
                        : 'تم إرسال رسالتك إلى موظف الدعم.',
            ]);
        }

        $result = $botService->findAnswer(
            $data['message']
        );

        if (! $result) {
            $botMessage = SupportMessage::create([
                'support_ticket_id' => $ticket->id,
                'sender_id' => null,
                'sender_type' => 'bot',

                'message' =>
                    'لم أتمكن من إيجاد حل مؤكد. هل ترغب بتحويل المحادثة إلى موظف الدعم؟',

                'message_type' => 'text',
                'is_internal' => false,
            ]);

            $ticket->update([
                'bot_confidence' => 0,
                'last_message_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'handled_by' => 'bot',

                'message' =>
                    $botMessage->load('sender:id,name'),

                'ticket' =>
                    $this->ticketPayload(
                        $ticket->fresh()
                    ),

                'show_transfer_button' => true,
            ]);
        }

        $botMessage = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => null,
            'sender_type' => 'bot',

            'message' => $result['answer'],

            'message_type' => 'text',
            'is_internal' => false,
        ]);

        $ticket->update([
            'bot_confidence' =>
                $result['confidence'],

            'last_message_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'handled_by' => 'bot',

            'message' =>
                $botMessage->load('sender:id,name'),

            'ticket' =>
                $this->ticketPayload(
                    $ticket->fresh()
                ),

            'confidence' =>
                $result['confidence'],

            'show_feedback_buttons' => true,
        ]);
    }

    /**
     * جلب الرسائل الجديدة دون إعادة تحميل الصفحة.
     */
    public function messages(
        Request $request,
        SupportTicket $ticket
    ): JsonResponse {
        $this->ensureTicketOwner(
            $request,
            $ticket
        );

        $data = $request->validate([
            'after_id' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $afterId = (int) (
            $data['after_id'] ?? 0
        );

        $messages = $ticket
            ->messages()
            ->where('is_internal', false)
            ->where('id', '>', $afterId)
            ->with('sender:id,name')
            ->orderBy('id')
            ->limit(100)
            ->get();

        /*
         * الرسائل التي ظهرت للعميل تعتبر مقروءة.
         */
        $messageIdsToMarkRead = $messages
            ->whereIn('sender_type', [
                'employee',
                'admin',
                'system',
                'bot',
            ])
            ->pluck('id');

        if ($messageIdsToMarkRead->isNotEmpty()) {
            SupportMessage::query()
                ->whereIn('id', $messageIdsToMarkRead)
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                ]);
        }

        $ticket->refresh();

        return response()->json([
            'success' => true,

            'ticket' =>
                $this->ticketPayload($ticket),

            'messages' => $messages,

            'last_message_id' =>
                (int) (
                    $messages->max('id')
                    ?? $afterId
                ),

            'conversation_closed' =>
                in_array(
                    $ticket->status,
                    ['resolved', 'closed'],
                    true
                ),
        ]);
    }

    /**
     * تأكيد العميل أن البوت حل المشكلة.
     */
    public function resolve(
        Request $request,
        SupportTicket $ticket
    ): JsonResponse {
        $this->ensureTicketOwner(
            $request,
            $ticket
        );

        if ($ticket->support_mode !== 'bot') {
            return response()->json([
                'success' => false,
                'message' =>
                    'التذكرة لم تعد تحت معالجة البوت.',
            ], 422);
        }

        if (in_array(
            $ticket->status,
            ['resolved', 'closed'],
            true
        )) {
            return response()->json([
                'success' => true,
                'message' =>
                    'تم إغلاق هذه المحادثة مسبقًا.',
            ]);
        }

        DB::transaction(function () use ($ticket) {
            $ticket->update([
                'status' => 'resolved',
                'bot_resolved' => true,
                'resolved_at' => now(),
                'last_message_at' => now(),
            ]);

            SupportMessage::create([
                'support_ticket_id' => $ticket->id,
                'sender_id' => null,
                'sender_type' => 'system',

                'message' =>
                    'تم إغلاق المحادثة بعد تأكيد العميل أن المشكلة حُلّت.',

                'message_type' => 'text',
                'is_internal' => false,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'سعداء بحل مشكلتك.',

            'ticket' =>
                $this->ticketPayload(
                    $ticket->fresh()
                ),
        ]);
    }

    /**
     * تحويل المحادثة إلى موظف الدعم.
     */
    public function transfer(
        Request $request,
        SupportTicket $ticket,
        SupportAssignmentService $assignmentService
    ): JsonResponse {
        $this->ensureTicketOwner(
            $request,
            $ticket
        );

        if (in_array(
            $ticket->status,
            ['resolved', 'closed'],
            true
        )) {
            return response()->json([
                'success' => false,
                'message' =>
                    'لا يمكن تحويل تذكرة مغلقة.',
            ], 422);
        }

        if ($ticket->support_mode === 'employee') {
            return response()->json([
                'success' => true,
                'assigned' => true,

                'employee' =>
                    $ticket->assignedEmployee
                        ? [
                            'id' =>
                                $ticket->assignedEmployee->id,

                            'name' =>
                                $ticket->assignedEmployee->name,
                        ]
                        : null,

                'ticket' =>
                    $this->ticketPayload($ticket),

                'message' =>
                    'تم تحويل التذكرة إلى موظف مسبقًا.',
            ]);
        }

        if (
            $ticket->support_mode === 'waiting_employee' &&
            ! $ticket->assigned_employee_id
        ) {
            return response()->json([
                'success' => true,
                'assigned' => false,
                'employee' => null,

                'ticket' =>
                    $this->ticketPayload($ticket),

                'message' =>
                    'التذكرة موجودة بالفعل في قائمة انتظار الدعم.',
            ]);
        }

        $employee = DB::transaction(
            function () use (
                $ticket,
                $assignmentService
            ) {
                $employee =
                    $assignmentService->assignEmployee(
                        $ticket
                    );

                SupportMessage::create([
                    'support_ticket_id' => $ticket->id,
                    'sender_id' => null,
                    'sender_type' => 'system',

                    'message' => $employee
                        ? "تم تحويل المحادثة إلى موظف الدعم {$employee->name}."
                        : 'تم تحويل التذكرة إلى قائمة انتظار الدعم.',

                    'message_type' => 'text',
                    'is_internal' => false,
                ]);

                $ticket->update([
                    'last_message_at' => now(),
                ]);

                return $employee;
            }
        );

        $ticket->refresh();

        return response()->json([
            'success' => true,

            'assigned' => $employee !== null,

            'employee' => $employee
                ? [
                    'id' => $employee->id,
                    'name' => $employee->name,
                ]
                : null,

            'ticket' =>
                $this->ticketPayload($ticket),

            'message' => $employee
                ? 'تم تحويلك إلى موظف الدعم.'
                : 'لا يوجد موظف متاح حاليًا، وتمت إضافة التذكرة لقائمة الانتظار.',
        ]);
    }

    /**
     * التأكد أن التذكرة تعود للمستخدم الحالي.
     */
    private function ensureTicketOwner(
        Request $request,
        SupportTicket $ticket
    ): void {
        abort_unless(
            (int) $ticket->user_id ===
                (int) $request->user()->id,
            403,
            'غير مصرح لك بالوصول إلى هذه التذكرة.'
        );
    }

    /**
     * بيانات التذكرة المرسلة إلى JavaScript.
     */
    private function ticketPayload(
        SupportTicket $ticket
    ): array {
        return [
            'id' => $ticket->id,

            'ticket_number' =>
                $ticket->ticket_number,

            'status' => $ticket->status,

            'support_mode' =>
                $ticket->support_mode,

            'assigned_employee_id' =>
                $ticket->assigned_employee_id,

            'is_closed' => in_array(
                $ticket->status,
                ['resolved', 'closed'],
                true
            ),
        ];
    }

    /**
     * إنشاء رقم فريد للتذكرة.
     */
    private function generateTicketNumber(): string
    {
        do {
            $ticketNumber =
                'SUP-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(Str::random(6));
        } while (
            SupportTicket::query()
                ->where(
                    'ticket_number',
                    $ticketNumber
                )
                ->exists()
        );

        return $ticketNumber;
    }
}
