<x-app-layout>
    @php
        $currentUser = auth()->user();

        $roleLabels = [
            'admin' => 'مدير',
            'engineer' => 'مهندس',
            'customer' => 'عميل',
            'employee' => 'موظف',
        ];

        $conversationTitle =
            $conversation->type === 'consultation'
                ? (
                    $conversation->consultation?->title
                    ?? 'محادثة استشارة'
                )
                : (
                    $otherParticipant?->name
                    ?? 'محادثة مباشرة'
                );
    @endphp

    <div
        class="min-h-screen bg-[#07111f] text-slate-100"
        dir="rtl"
    >
        <div
            class="fixed inset-0 pointer-events-none"
            style="
                background:
                    radial-gradient(
                        circle at top right,
                        rgba(37, 99, 235, .14),
                        transparent 30%
                    ),
                    radial-gradient(
                        circle at bottom left,
                        rgba(124, 58, 237, .12),
                        transparent 32%
                    );
            "
        ></div>

        <div
            class="relative z-10 flex flex-col min-h-screen"
        >
            {{-- الشريط العلوي --}}
            <header
                class="sticky top-0 z-40 border-b border-white/10 bg-[#091525]/90 backdrop-blur-xl"
            >
                <div
                    class="flex items-center justify-between gap-4 px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8"
                >
                    <div class="flex items-center min-w-0 gap-3">
                        <a
                            href="{{ route('conversations.index') }}"
                            class="flex items-center justify-center flex-none w-10 h-10 transition border rounded-xl border-white/10 bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white"
                            title="العودة للمحادثات"
                        >
                            <svg
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                        </a>

                        <div
                            class="flex items-center justify-center flex-none w-12 h-12 overflow-hidden font-black text-white border rounded-2xl border-white/10 bg-gradient-to-br from-blue-500 to-violet-600"
                        >
                            @if ($otherParticipant?->profile_photo)
                                <img
                                    src="{{ asset(
                                        'storage/'
                                        . $otherParticipant->profile_photo
                                    ) }}"
                                    alt="{{ $otherParticipant->name }}"
                                    class="object-cover w-full h-full"
                                >
                            @else
                                {{
                                    mb_substr(
                                        $conversationTitle,
                                        0,
                                        1
                                    )
                                }}
                            @endif
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h1
                                    class="font-black text-white truncate"
                                >
                                    {{ $conversationTitle }}
                                </h1>

                                <span
                                    class="rounded-full px-2.5 py-1 text-[10px] font-bold
                                        {{
                                            $conversation->type === 'direct'
                                                ? 'bg-violet-500/10 text-violet-300'
                                                : 'bg-cyan-500/10 text-cyan-300'
                                        }}"
                                >
                                    {{
                                        $conversation->type === 'direct'
                                            ? 'مباشرة'
                                            : 'استشارة'
                                    }}
                                </span>
                            </div>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                @if ($conversation->type === 'consultation')
                                    استشارة رقم
                                    {{
                                        $conversation
                                            ->consultation
                                            ?->consultation_number
                                    }}
                                @else
                                    {{
                                        $roleLabels[
                                            $otherParticipant?->role
                                        ] ?? 'مستخدم'
                                    }}
                                @endif

                                <span
                                    id="presenceStatus"
                                    class="mr-2"
                                >
                                    غير متصل
                                </span>

                                <span
                                    id="typingStatus"
                                    class="hidden mr-2 text-blue-300"
                                >
                                    يكتب الآن...
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if (
                            $conversation->type === 'consultation'
                            && $conversation->consultation
                        )
                            <a
                                href="{{ route(
                                    'consultations.messages.index',
                                    $conversation->consultation
                                ) }}"
                                class="hidden px-4 py-2 text-xs font-bold transition border sm:inline-flex rounded-xl border-white/10 bg-white/5 text-slate-300 hover:bg-white/10"
                            >
                                صفحة الاستشارة
                            </a>
                        @endif

                        <a
                            href="{{ route('dashboard') }}"
                            class="flex items-center justify-center w-10 h-10 transition border rounded-xl border-white/10 bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white"
                            title="لوحة التحكم"
                        >
                            <svg
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                                <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                                <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                                <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </header>

            {{-- المحتوى --}}
            <main
                class="flex flex-1 w-full px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8"
            >
                <section
                    class="flex flex-col w-full overflow-hidden border shadow-2xl rounded-3xl border-white/10 bg-[#0d1829]/90 backdrop-blur-xl"
                >
                    {{-- رسائل النظام --}}
                    @if (session('success'))
                        <div
                            class="p-3 mx-4 mt-4 text-sm font-bold border text-emerald-200 rounded-2xl border-emerald-500/20 bg-emerald-500/10"
                        >
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div
                            class="p-3 mx-4 mt-4 text-sm border text-rose-100 rounded-2xl border-rose-500/20 bg-rose-500/10"
                        >
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- الرسائل --}}
                    <div
                        id="messagesContainer"
                        class="flex-1 h-[62vh] min-h-[420px] overflow-y-auto p-4 sm:p-6"
                    >
                        <div
                            id="messagesList"
                            class="space-y-4"
                        >
                            @forelse ($conversation->messages as $message)
                                @php
                                    $isMine =
                                        (int) $message->sender_id
                                        === (int) $currentUser->id;

                                    $sender = $message->sender;
                                @endphp

                                <div
                                    data-message-id="{{ $message->id }}"
                                    class="flex items-end gap-3 {{
                                        $isMine
                                            ? 'flex-row-reverse'
                                            : ''
                                    }}"
                                >
                                    <div
                                        class="flex items-center justify-center flex-none overflow-hidden text-xs font-black text-white border rounded-full w-9 h-9 border-white/10 bg-gradient-to-br from-blue-500 to-violet-600"
                                    >
                                        @if ($sender?->profile_photo)
                                            <img
                                                src="{{ asset(
                                                    'storage/'
                                                    . $sender->profile_photo
                                                ) }}"
                                                alt="{{ $sender->name }}"
                                                class="object-cover w-full h-full"
                                            >
                                        @else
                                            {{
                                                mb_substr(
                                                    $sender?->name ?? 'م',
                                                    0,
                                                    1
                                                )
                                            }}
                                        @endif
                                    </div>

                                    <div
                                        class="max-w-[84%] sm:max-w-[68%]"
                                    >
                                        <div
                                            class="px-4 py-3 shadow-lg rounded-2xl
                                                {{
                                                    $isMine
                                                        ? 'rounded-br-md bg-gradient-to-br from-blue-600 to-violet-600 text-white'
                                                        : 'rounded-bl-md border border-white/5 bg-[#17243a] text-slate-100'
                                                }}"
                                        >
                                            <div
                                                class="flex items-center justify-between gap-4 mb-2"
                                            >
                                                <p
                                                    class="text-xs font-black"
                                                >
                                                    {{
                                                        $isMine
                                                            ? 'أنت'
                                                            : (
                                                                $sender?->name
                                                                ?? 'مستخدم'
                                                            )
                                                    }}
                                                </p>

                                                <span
                                                    class="text-[10px] opacity-60"
                                                >
                                                    {{
                                                        $message
                                                            ->created_at
                                                            ?->format('H:i')
                                                    }}
                                                </span>
                                            </div>

                                            @if ($message->isDeleted())
                                                <p
                                                    class="text-sm italic opacity-60"
                                                >
                                                    تم حذف هذه الرسالة
                                                </p>
                                            @else
                                                @if ($message->message)
                                                    <p
                                                        class="text-sm leading-7 whitespace-pre-line"
                                                    >
                                                        {{ $message->message }}
                                                    </p>
                                                @endif

                                                @if (
                                                    $message->message_type
                                                    === 'voice'
                                                    && $message->attachment_path
                                                )
                                                    <div
                                                        class="p-3 mt-3 border rounded-2xl border-white/10 bg-black/15"
                                                    >
                                                        <audio
                                                            controls
                                                            preload="metadata"
                                                            class="w-full min-w-[220px]"
                                                        >
                                                            <source
                                                                src="{{ route(
                                                                    'conversations.messages.attachment',
                                                                    [
                                                                        $conversation,
                                                                        $message,
                                                                    ]
                                                                ) }}"
                                                                type="{{ $message->attachment_mime }}"
                                                            >
                                                        </audio>

                                                        @if ($message->audio_duration)
                                                            <p
                                                                class="mt-2 text-[10px] opacity-60"
                                                            >
                                                                المدة:
                                                                {{
                                                                    gmdate(
                                                                        'i:s',
                                                                        $message->audio_duration
                                                                    )
                                                                }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                @elseif (
                                                    $message->message_type
                                                    === 'image'
                                                    && $message->attachment_path
                                                )
                                                    <a
                                                        href="{{ route(
                                                            'conversations.messages.attachment',
                                                            [
                                                                $conversation,
                                                                $message,
                                                            ]
                                                        ) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="block mt-3 overflow-hidden border rounded-2xl border-white/10"
                                                    >
                                                        <img
                                                            src="{{ route(
                                                                'conversations.messages.attachment',
                                                                [
                                                                    $conversation,
                                                                    $message,
                                                                ]
                                                            ) }}"
                                                            alt="صورة مرفقة"
                                                            class="object-cover w-full max-h-72"
                                                        >
                                                    </a>
                                                @elseif (
                                                    $message->attachment_path
                                                )
                                                    <a
                                                        href="{{ route(
                                                            'conversations.messages.download',
                                                            [
                                                                $conversation,
                                                                $message,
                                                            ]
                                                        ) }}"
                                                        class="flex items-center justify-between gap-3 p-3 mt-3 border rounded-2xl border-white/10 bg-black/15"
                                                    >
                                                        <div
                                                            class="flex items-center min-w-0 gap-3"
                                                        >
                                                            <div
                                                                class="flex items-center justify-center flex-none w-10 h-10 text-xs font-black rounded-xl bg-violet-500/20 text-violet-200"
                                                            >
                                                                FILE
                                                            </div>

                                                            <div class="min-w-0">
                                                                <p
                                                                    class="text-sm font-bold truncate"
                                                                >
                                                                    {{
                                                                        $message
                                                                            ->attachment_name
                                                                        ?? 'ملف مرفق'
                                                                    }}
                                                                </p>

                                                                <p
                                                                    class="mt-1 text-[10px] opacity-60"
                                                                >
                                                                    اضغط للتحميل
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <svg
                                                            class="w-5 h-5"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                        >
                                                            <path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div
                                    id="emptyConversation"
                                    class="flex flex-col items-center justify-center h-[420px] text-center"
                                >
                                    <div
                                        class="flex items-center justify-center w-20 h-20 mb-5 text-blue-300 rounded-full bg-blue-500/10"
                                    >
                                        <svg
                                            class="w-9 h-9"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.6"
                                        >
                                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                                        </svg>
                                    </div>

                                    <h3
                                        class="text-xl font-black text-white"
                                    >
                                        لا توجد رسائل حتى الآن
                                    </h3>

                                    <p
                                        class="mt-3 text-sm text-slate-400"
                                    >
                                        ابدأ المحادثة بإرسال أول رسالة.
                                    </p>
                                </div>
                            @endforelse
                        </div>

                        <div
                            id="typingIndicator"
                            class="hidden mt-4 text-sm font-bold text-blue-300"
                        >
                            يكتب الآن...
                        </div>
                    </div>

                    {{-- نموذج الإرسال --}}
                    <div
                        class="p-4 border-t border-white/10 bg-[#0a1525]"
                    >
                        <form
                            id="chatForm"
                            action="{{ route(
                                'conversations.messages.store',
                                $conversation
                            ) }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="space-y-3"
                        >
                            @csrf

                            <input
                                id="voiceMessage"
                                type="file"
                                name="voice_message"
                                class="hidden"
                            >

                            <input
                                id="audioDuration"
                                type="hidden"
                                name="audio_duration"
                            >

                            <div
                                class="flex items-end gap-2 p-2 border rounded-3xl border-white/10 bg-white/[.04]"
                            >
                                <textarea
                                    id="message"
                                    name="message"
                                    rows="1"
                                    placeholder="اكتب رسالتك هنا..."
                                    class="min-h-[48px] max-h-32 flex-1 resize-none border-0 bg-transparent px-3 py-3 text-sm text-white placeholder:text-slate-500 focus:ring-0"
                                ></textarea>

                                <label
                                    for="attachment"
                                    class="flex items-center justify-center flex-none transition cursor-pointer w-11 h-11 rounded-xl bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white"
                                    title="إرفاق ملف"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.9"
                                    >
                                        <path d="m20 11.5-8.5 8.5a6 6 0 0 1-8.5-8.5l9-9a4 4 0 0 1 5.7 5.7l-9 9a2 2 0 0 1-2.8-2.8l8.3-8.3"/>
                                    </svg>
                                </label>

                                <input
                                    id="attachment"
                                    type="file"
                                    name="attachment"
                                    class="hidden"
                                    accept=".pdf,.dwg,.jpg,.jpeg,.png,.webp,.doc,.docx,.xls,.xlsx,.zip"
                                >

                                <button
                                    id="recordVoiceButton"
                                    type="button"
                                    class="flex items-center justify-center flex-none transition rounded-xl w-11 h-11 bg-white/5 text-slate-300 hover:bg-rose-500/20 hover:text-rose-200"
                                    title="تسجيل صوتي"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.9"
                                    >
                                        <path d="M12 3a3 3 0 0 0-3 3v6a3 3 0 0 0 6 0V6a3 3 0 0 0-3-3Z"/>
                                        <path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/>
                                    </svg>
                                </button>

                                <button
                                    id="sendButton"
                                    type="submit"
                                    class="flex items-center justify-center flex-none text-white transition rounded-full shadow-lg w-11 h-11 bg-gradient-to-br from-blue-500 to-violet-600 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                                    aria-label="إرسال"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.9"
                                    >
                                        <path d="m22 2-7 20-4-9-9-4 20-7Z"/>
                                        <path d="M22 2 11 13"/>
                                    </svg>
                                </button>
                            </div>

                            <div
                                id="attachmentPreview"
                                class="hidden items-center justify-between gap-3 p-3 border rounded-2xl border-white/10 bg-white/[.04]"
                            >
                                <span
                                    id="attachmentName"
                                    class="text-xs truncate text-slate-300"
                                ></span>

                                <button
                                    id="removeAttachment"
                                    type="button"
                                    class="text-xs font-bold text-rose-300"
                                >
                                    إزالة
                                </button>
                            </div>

                            <div
                                id="voicePreview"
                                class="items-center hidden gap-3 p-3 border rounded-2xl border-rose-500/20 bg-rose-500/10"
                            >
                                <span
                                    id="recordingIndicator"
                                    class="w-3 h-3 rounded-full bg-rose-500 animate-pulse"
                                ></span>

                                <span
                                    id="recordingTimer"
                                    class="text-sm font-black text-rose-200"
                                >
                                    00:00
                                </span>

                                <audio
                                    id="recordedAudio"
                                    controls
                                    class="flex-1 hidden"
                                ></audio>

                                <button
                                    id="deleteVoiceButton"
                                    type="button"
                                    class="px-3 py-2 text-xs font-bold rounded-xl bg-rose-500/15 text-rose-200"
                                >
                                    حذف
                                </button>
                            </div>

                            <p
                                id="formError"
                                class="hidden text-sm font-bold text-rose-300"
                            ></p>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {
                const conversationId =
                    @json($conversation->id);

                const currentUserId =
                    @json($currentUser->id);

                const currentUserName =
                    @json($currentUser->name);

                const currentUserPhoto =
                    @json(
                        $currentUser->profile_photo
                            ? asset(
                                'storage/'
                                . $currentUser->profile_photo
                            )
                            : null
                    );

                const form =
                    document.getElementById('chatForm');

                const textarea =
                    document.getElementById('message');

                const attachment =
                    document.getElementById('attachment');

                const attachmentPreview =
                    document.getElementById(
                        'attachmentPreview'
                    );

                const attachmentName =
                    document.getElementById(
                        'attachmentName'
                    );

                const removeAttachment =
                    document.getElementById(
                        'removeAttachment'
                    );

                const sendButton =
                    document.getElementById('sendButton');

                const messagesContainer =
                    document.getElementById(
                        'messagesContainer'
                    );

                const messagesList =
                    document.getElementById(
                        'messagesList'
                    );

                const emptyConversation =
                    document.getElementById(
                        'emptyConversation'
                    );

                const typingIndicator =
                    document.getElementById(
                        'typingIndicator'
                    );

                const typingStatus =
                    document.getElementById(
                        'typingStatus'
                    );

                const presenceStatus =
                    document.getElementById(
                        'presenceStatus'
                    );

                const formError =
                    document.getElementById('formError');

                const recordVoiceButton =
                    document.getElementById(
                        'recordVoiceButton'
                    );

                const voiceMessageInput =
                    document.getElementById(
                        'voiceMessage'
                    );

                const audioDurationInput =
                    document.getElementById(
                        'audioDuration'
                    );

                const voicePreview =
                    document.getElementById(
                        'voicePreview'
                    );

                const recordedAudio =
                    document.getElementById(
                        'recordedAudio'
                    );

                const recordingTimer =
                    document.getElementById(
                        'recordingTimer'
                    );

                const recordingIndicator =
                    document.getElementById(
                        'recordingIndicator'
                    );

                const deleteVoiceButton =
                    document.getElementById(
                        'deleteVoiceButton'
                    );

                let channel = null;
                let typingTimer = null;
                let mediaRecorder = null;
                let audioChunks = [];
                let recordingStartedAt = null;
                let recordingInterval = null;
                let recordedAudioUrl = null;

                const scrollToBottom = () => {
                    if (messagesContainer) {
                        messagesContainer.scrollTop =
                            messagesContainer.scrollHeight;
                    }
                };

                const escapeHtml = (value) => {
                    const div =
                        document.createElement('div');

                    div.textContent = value ?? '';

                    return div.innerHTML;
                };

                const formatTime = (seconds) => {
                    const minutes =
                        Math.floor(seconds / 60);

                    const remainingSeconds =
                        seconds % 60;

                    return `${
                        String(minutes).padStart(2, '0')
                    }:${
                        String(remainingSeconds).padStart(
                            2,
                            '0'
                        )
                    }`;
                };

                const messageExists = (id) => {
                    return document.querySelector(
                        `[data-message-id="${id}"]`
                    ) !== null;
                };

                const appendMessage = (
                    message,
                    mine
                ) => {
                    if (
                        ! messagesList
                        || messageExists(message.id)
                    ) {
                        return;
                    }

                    emptyConversation?.remove();

                    const wrapper =
                        document.createElement('div');

                    wrapper.dataset.messageId =
                        message.id;

                    wrapper.className =
                        `flex items-end gap-3 ${
                            mine ? 'flex-row-reverse' : ''
                        }`;

                    const senderName =
                        mine
                            ? 'أنت'
                            : (
                                message.sender_name
                                || 'مستخدم'
                            );

                    const initial =
                        escapeHtml(
                            senderName.charAt(0)
                        );

                    let avatarHtml = `
                        <div class="flex items-center justify-center flex-none overflow-hidden text-xs font-black text-white border rounded-full w-9 h-9 border-white/10 bg-gradient-to-br from-blue-500 to-violet-600">
                            ${
                                message.sender_profile_photo_url
                                    ? `<img
                                        src="${escapeHtml(
                                            message.sender_profile_photo_url
                                        )}"
                                        alt="${escapeHtml(
                                            senderName
                                        )}"
                                        class="object-cover w-full h-full"
                                    >`
                                    : initial
                            }
                        </div>
                    `;

                    let attachmentHtml = '';

                    if (
                        message.message_type === 'voice'
                        && message.attachment_url
                    ) {
                        attachmentHtml = `
                            <div class="p-3 mt-3 border rounded-2xl border-white/10 bg-black/15">
                                <audio
                                    controls
                                    preload="metadata"
                                    class="w-full min-w-[220px]"
                                    src="${escapeHtml(
                                        message.attachment_url
                                    )}"
                                ></audio>

                                ${
                                    message.audio_duration
                                        ? `<p class="mt-2 text-[10px] opacity-60">
                                            المدة:
                                            ${formatTime(
                                                Number(
                                                    message.audio_duration
                                                )
                                            )}
                                        </p>`
                                        : ''
                                }
                            </div>
                        `;
                    } else if (
                        message.message_type === 'image'
                        && message.attachment_url
                    ) {
                        attachmentHtml = `
                            <a
                                href="${escapeHtml(
                                    message.attachment_url
                                )}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="block mt-3 overflow-hidden border rounded-2xl border-white/10"
                            >
                                <img
                                    src="${escapeHtml(
                                        message.attachment_url
                                    )}"
                                    alt="صورة مرفقة"
                                    class="object-cover w-full max-h-72"
                                >
                            </a>
                        `;
                    } else if (
                        message.attachment_url
                    ) {
                        attachmentHtml = `
                            <a
                                href="${escapeHtml(
                                    message.attachment_url
                                )}"
                                class="flex items-center justify-between gap-3 p-3 mt-3 border rounded-2xl border-white/10 bg-black/15"
                            >
                                <div class="min-w-0">
                                    <p class="text-sm font-bold truncate">
                                        ${escapeHtml(
                                            message.attachment_name
                                            || 'ملف مرفق'
                                        )}
                                    </p>

                                    <p class="mt-1 text-[10px] opacity-60">
                                        اضغط لفتح الملف
                                    </p>
                                </div>

                                <span>↓</span>
                            </a>
                        `;
                    }

                    wrapper.innerHTML = `
                        ${avatarHtml}

                        <div class="max-w-[84%] sm:max-w-[68%]">
                            <div class="px-4 py-3 shadow-lg rounded-2xl ${
                                mine
                                    ? 'rounded-br-md bg-gradient-to-br from-blue-600 to-violet-600 text-white'
                                    : 'rounded-bl-md border border-white/5 bg-[#17243a] text-slate-100'
                            }">
                                <div class="flex items-center justify-between gap-4 mb-2">
                                    <p class="text-xs font-black">
                                        ${escapeHtml(senderName)}
                                    </p>

                                    <span class="text-[10px] opacity-60">
                                        ${escapeHtml(
                                            message.time || ''
                                        )}
                                    </span>
                                </div>

                                ${
                                    message.body
                                        ? `<p class="text-sm leading-7 whitespace-pre-line">
                                            ${escapeHtml(
                                                message.body
                                            )}
                                        </p>`
                                        : ''
                                }

                                ${attachmentHtml}
                            </div>
                        </div>
                    `;

                    messagesList.appendChild(wrapper);

                    scrollToBottom();
                };

                const resetAttachment = () => {
                    if (attachment) {
                        attachment.value = '';
                    }

                    attachmentName.textContent = '';

                    attachmentPreview.classList.add(
                        'hidden'
                    );

                    attachmentPreview.classList.remove(
                        'flex'
                    );
                };

                attachment?.addEventListener(
                    'change',
                    () => {
                        const file =
                            attachment.files?.[0];

                        if (! file) {
                            resetAttachment();
                            return;
                        }

                        attachmentName.textContent =
                            file.name;

                        attachmentPreview.classList.remove(
                            'hidden'
                        );

                        attachmentPreview.classList.add(
                            'flex'
                        );
                    }
                );

                removeAttachment?.addEventListener(
                    'click',
                    resetAttachment
                );

                const resetVoiceRecording = () => {
                    clearInterval(recordingInterval);

                    mediaRecorder = null;
                    audioChunks = [];
                    recordingStartedAt = null;

                    voiceMessageInput.value = '';
                    audioDurationInput.value = '';

                    if (recordedAudioUrl) {
                        URL.revokeObjectURL(
                            recordedAudioUrl
                        );

                        recordedAudioUrl = null;
                    }

                    recordedAudio.removeAttribute(
                        'src'
                    );

                    recordedAudio.classList.add(
                        'hidden'
                    );

                    recordingIndicator.classList.remove(
                        'hidden'
                    );

                    recordingTimer.textContent =
                        '00:00';

                    voicePreview.classList.add(
                        'hidden'
                    );

                    voicePreview.classList.remove(
                        'flex'
                    );

                    recordVoiceButton.classList.remove(
                        'bg-rose-500',
                        'text-white',
                        'animate-pulse'
                    );
                };

                const stopVoiceRecording = () => {
                    if (
                        mediaRecorder
                        && mediaRecorder.state
                            === 'recording'
                    ) {
                        mediaRecorder.stop();
                    }
                };

                recordVoiceButton?.addEventListener(
                    'click',
                    async () => {
                        if (
                            mediaRecorder
                            && mediaRecorder.state
                                === 'recording'
                        ) {
                            stopVoiceRecording();
                            return;
                        }

                        try {
                            const stream =
                                await navigator
                                    .mediaDevices
                                    .getUserMedia({
                                        audio: true,
                                    });

                            const preferredMime =
                                MediaRecorder
                                    .isTypeSupported(
                                        'audio/webm;codecs=opus'
                                    )
                                    ? 'audio/webm;codecs=opus'
                                    : '';

                            mediaRecorder =
                                preferredMime
                                    ? new MediaRecorder(
                                        stream,
                                        {
                                            mimeType:
                                                preferredMime,
                                        }
                                    )
                                    : new MediaRecorder(
                                        stream
                                    );

                            audioChunks = [];

                            mediaRecorder
                                .addEventListener(
                                    'dataavailable',
                                    (event) => {
                                        if (
                                            event.data.size
                                            > 0
                                        ) {
                                            audioChunks.push(
                                                event.data
                                            );
                                        }
                                    }
                                );

                            mediaRecorder
                                .addEventListener(
                                    'stop',
                                    () => {
                                        clearInterval(
                                            recordingInterval
                                        );

                                        const duration =
                                            Math.max(
                                                1,
                                                Math.round(
                                                    (
                                                        Date.now()
                                                        - recordingStartedAt
                                                    ) / 1000
                                                )
                                            );

                                        const blob =
                                            new Blob(
                                                audioChunks,
                                                {
                                                    type:
                                                        mediaRecorder
                                                            .mimeType
                                                        || 'audio/webm',
                                                }
                                            );

                                        const extension =
                                            blob.type.includes(
                                                'ogg'
                                            )
                                                ? 'ogg'
                                                : (
                                                    blob.type.includes(
                                                        'mp4'
                                                    )
                                                        ? 'm4a'
                                                        : 'webm'
                                                );

                                        const file =
                                            new File(
                                                [blob],
                                                `voice-${Date.now()}.${extension}`,
                                                {
                                                    type:
                                                        blob.type,
                                                }
                                            );

                                        const transfer =
                                            new DataTransfer();

                                        transfer.items.add(
                                            file
                                        );

                                        voiceMessageInput.files =
                                            transfer.files;

                                        audioDurationInput.value =
                                            duration;

                                        recordedAudioUrl =
                                            URL.createObjectURL(
                                                blob
                                            );

                                        recordedAudio.src =
                                            recordedAudioUrl;

                                        recordedAudio.classList.remove(
                                            'hidden'
                                        );

                                        recordingIndicator.classList.add(
                                            'hidden'
                                        );

                                        recordingTimer.textContent =
                                            formatTime(
                                                duration
                                            );

                                        recordVoiceButton.classList.remove(
                                            'bg-rose-500',
                                            'text-white',
                                            'animate-pulse'
                                        );

                                        stream
                                            .getTracks()
                                            .forEach(
                                                (track) =>
                                                    track.stop()
                                            );
                                    }
                                );

                            mediaRecorder.start();

                            recordingStartedAt =
                                Date.now();

                            voicePreview.classList.remove(
                                'hidden'
                            );

                            voicePreview.classList.add(
                                'flex'
                            );

                            recordVoiceButton.classList.add(
                                'bg-rose-500',
                                'text-white',
                                'animate-pulse'
                            );

                            recordingInterval =
                                setInterval(
                                    () => {
                                        const seconds =
                                            Math.floor(
                                                (
                                                    Date.now()
                                                    - recordingStartedAt
                                                ) / 1000
                                            );

                                        recordingTimer.textContent =
                                            formatTime(
                                                seconds
                                            );

                                        if (
                                            seconds >= 600
                                        ) {
                                            stopVoiceRecording();
                                        }
                                    },
                                    1000
                                );
                        } catch (error) {
                            formError.textContent =
                                'تعذر الوصول إلى الميكروفون. اسمح للموقع باستخدام الميكروفون.';

                            formError.classList.remove(
                                'hidden'
                            );
                        }
                    }
                );

                deleteVoiceButton?.addEventListener(
                    'click',
                    resetVoiceRecording
                );

                scrollToBottom();

                if (window.Echo) {
                    channel = window.Echo.join(
                        `conversation.${conversationId}`
                    )
                        .here((users) => {
                            const online =
                                users.some(
                                    (user) =>
                                        Number(user.id)
                                        !== Number(
                                            currentUserId
                                        )
                                );

                            presenceStatus.textContent =
                                online
                                    ? 'نشط الآن'
                                    : 'غير متصل';

                            presenceStatus.classList.toggle(
                                'text-emerald-300',
                                online
                            );
                        })
                        .joining((user) => {
                            if (
                                Number(user.id)
                                !== Number(currentUserId)
                            ) {
                                presenceStatus.textContent =
                                    'نشط الآن';

                                presenceStatus.classList.add(
                                    'text-emerald-300'
                                );
                            }
                        })
                        .leaving((user) => {
                            if (
                                Number(user.id)
                                !== Number(currentUserId)
                            ) {
                                presenceStatus.textContent =
                                    'غير متصل';

                                presenceStatus.classList.remove(
                                    'text-emerald-300'
                                );
                            }
                        })
                        .listen(
                            '.conversation.message.sent',
                            (event) => {
                                appendMessage(
                                    event.message,
                                    Number(
                                        event.message.sender_id
                                    )
                                    === Number(currentUserId)
                                );
                            }
                        )
                        .listenForWhisper(
                            'typing',
                            (event) => {
                                if (
                                    Number(event.user_id)
                                    === Number(currentUserId)
                                ) {
                                    return;
                                }

                                typingIndicator.classList.remove(
                                    'hidden'
                                );

                                typingStatus.classList.remove(
                                    'hidden'
                                );

                                clearTimeout(typingTimer);

                                typingTimer =
                                    setTimeout(
                                        () => {
                                            typingIndicator.classList.add(
                                                'hidden'
                                            );

                                            typingStatus.classList.add(
                                                'hidden'
                                            );
                                        },
                                        1500
                                    );
                            }
                        );
                }

                textarea?.addEventListener(
                    'input',
                    () => {
                        channel?.whisper(
                            'typing',
                            {
                                user_id:
                                    currentUserId,
                            }
                        );

                        textarea.style.height =
                            'auto';

                        textarea.style.height =
                            Math.min(
                                textarea.scrollHeight,
                                128
                            ) + 'px';
                    }
                );

                form?.addEventListener(
                    'submit',
                    async (event) => {
                        event.preventDefault();

                        formError.classList.add(
                            'hidden'
                        );

                        formError.textContent = '';

                        const hasMessage =
                            textarea.value
                                .trim()
                                .length > 0;

                        const hasAttachment =
                            attachment.files?.length
                            > 0;

                        const hasVoice =
                            voiceMessageInput.files
                                ?.length > 0;

                        if (
                            ! hasMessage
                            && ! hasAttachment
                            && ! hasVoice
                        ) {
                            formError.textContent =
                                'اكتب رسالة أو أرفق ملفًا أو سجّل رسالة صوتية.';

                            formError.classList.remove(
                                'hidden'
                            );

                            return;
                        }

                        sendButton.disabled = true;

                        const formData =
                            new FormData(form);

                        try {
                            const response =
                                await fetch(
                                    form.action,
                                    {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN':
                                                document
                                                    .querySelector(
                                                        'meta[name="csrf-token"]'
                                                    )
                                                    .content,

                                            'Accept':
                                                'application/json',
                                        },
                                        body: formData,
                                    }
                                );

                            const data =
                                await response.json();

                            if (! response.ok) {
                                throw new Error(
                                    data.message
                                    || Object
                                        .values(
                                            data.errors
                                            || {}
                                        )
                                        .flat()
                                        .join(' ')
                                    || 'تعذر إرسال الرسالة.'
                                );
                            }

                            appendMessage(
                                {
                                    ...data.message,
                                    sender_name:
                                        currentUserName,
                                    sender_profile_photo_url:
                                        currentUserPhoto,
                                },
                                true
                            );

                            form.reset();
                            textarea.style.height =
                                'auto';

                            resetAttachment();
                            resetVoiceRecording();
                        } catch (error) {
                            formError.textContent =
                                error.message;

                            formError.classList.remove(
                                'hidden'
                            );
                        } finally {
                            sendButton.disabled = false;
                        }
                    }
                );
            }
        );
    </script>
</x-app-layout>
