<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ConversationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | قائمة محادثات المستخدم
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $user = $request->user();

        $this->authorize(
            'viewAny',
            Conversation::class
        );

        $query = Conversation::query()
            ->with([
                'participants:id,name,email,role,profile_photo',
                'latestMessage.sender:id,name,profile_photo',
                'consultation:id,consultation_number,title,customer_id,engineer_id,payment_status,status',
            ])
            ->withCount('messages')
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at');

        /*
        |--------------------------------------------------------------------------
        | المدير
        |--------------------------------------------------------------------------
        |
        | يرى محادثات الاستشارات، ويرى المحادثات المباشرة التي يكون مشاركًا بها.
        |
        */

        if ($user->role === 'admin') {
            $query->where(function ($conversationQuery) use ($user) {
                $conversationQuery
                    ->where('type', 'consultation')
                    ->orWhere(function ($directQuery) use ($user) {
                        $directQuery
                            ->where('type', 'direct')
                            ->whereHas(
                                'participants',
                                fn ($participantQuery) =>
                                    $participantQuery->where(
                                        'users.id',
                                        $user->id
                                    )
                            );
                    });
            });
        } else {
            /*
            |--------------------------------------------------------------------------
            | العميل والمهندس
            |--------------------------------------------------------------------------
            */

            $query->whereHas(
                'participants',
                fn ($participantQuery) =>
                    $participantQuery->where(
                        'users.id',
                        $user->id
                    )
            );
        }

        $conversations = $query->paginate(20);

        return view(
            'conversations.index',
            compact('conversations')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | فتح محادثة
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        Conversation $conversation
    ): View {
        $this->authorize(
            'view',
            $conversation
        );

        $conversation->load([
            'participants:id,name,email,role,profile_photo',
            'consultation.customer:id,name,email,profile_photo',
            'consultation.engineer:id,name,email,profile_photo',
            'messages' => fn ($query) =>
                $query
                    ->with(
                        'sender:id,name,role,profile_photo'
                    )
                    ->orderBy('created_at'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | تعليم المحادثة كمقروءة
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

        $otherParticipant = $conversation
            ->participants
            ->first(
                fn (User $participant) =>
                    (int) $participant->id
                    !== (int) $request->user()->id
            );

        return view(
            'conversations.show',
            compact(
                'conversation',
                'otherParticipant'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | بدء محادثة مباشرة
    |--------------------------------------------------------------------------
    |
    | المدير فقط يستطيع بدء محادثة مباشرة.
    |
    */

    public function startDirect(
        Request $request,
        User $user
    ): RedirectResponse {
        $admin = $request->user();

        $this->authorize(
            'createDirect',
            Conversation::class
        );

        abort_if(
            (int) $admin->id === (int) $user->id,
            422,
            'لا يمكنك بدء محادثة مع نفسك.'
        );

        abort_if(
            $user->role === 'admin',
            422,
            'لا يمكن بدء هذه المحادثة من هنا.'
        );

        abort_unless(
            in_array(
                $user->role,
                [
                    'engineer',
                    'customer',
                    'employee',
                ],
                true
            ),
            422,
            'نوع المستخدم غير مسموح به.'
        );

        $conversation = DB::transaction(
            function () use ($admin, $user) {
                /*
                |--------------------------------------------------------------------------
                | البحث عن محادثة مباشرة موجودة
                |--------------------------------------------------------------------------
                |
                | نتحقق أن المحادثة تحتوي المدير والمستخدم المطلوب فقط.
                |
                */

                $existingConversation = Conversation::query()
                    ->where('type', 'direct')
                    ->whereNull('consultation_id')
                    ->whereHas(
                        'participants',
                        fn ($query) =>
                            $query->where(
                                'users.id',
                                $admin->id
                            )
                    )
                    ->whereHas(
                        'participants',
                        fn ($query) =>
                            $query->where(
                                'users.id',
                                $user->id
                            )
                    )
                    ->withCount('participants')
                    ->get()
                    ->first(
                        fn (Conversation $conversation) =>
                            (int) $conversation->participants_count === 2
                    );

                if ($existingConversation) {
                    return $existingConversation;
                }

                /*
                |--------------------------------------------------------------------------
                | إنشاء محادثة جديدة
                |--------------------------------------------------------------------------
                */

                $conversation = Conversation::create([
                    'type' => 'direct',
                    'consultation_id' => null,
                    'created_by' => $admin->id,
                    'last_message_at' => null,
                ]);

                $conversation
                    ->participants()
                    ->attach([
                        $admin->id => [
                            'last_read_at' => now(),
                            'is_muted' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],

                        $user->id => [
                            'last_read_at' => null,
                            'is_muted' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);

                return $conversation;
            }
        );

        return redirect()
            ->route(
                'conversations.show',
                $conversation
            )
            ->with(
                'success',
                'تم فتح المحادثة بنجاح.'
            );
    }
}
