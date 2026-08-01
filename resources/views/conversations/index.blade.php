<x-app-layout>
    @php
        $currentUser = auth()->user();

        $roleLabels = [
            'admin' => 'مدير',
            'engineer' => 'مهندس',
            'customer' => 'عميل',
            'employee' => 'موظف',
        ];
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
                        rgba(37, 99, 235, .15),
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
            class="relative z-10 px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8"
        >
            {{-- رأس الصفحة --}}
            <div
                class="flex flex-col gap-5 mb-8 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <div
                        class="flex items-center gap-3 mb-3 text-sm text-slate-400"
                    >
                        <a
                            href="{{ route('dashboard') }}"
                            class="transition hover:text-white"
                        >
                            لوحة التحكم
                        </a>

                        <span>/</span>

                        <span class="text-blue-300">
                            المحادثات
                        </span>
                    </div>

                    <h1
                        class="text-3xl font-black tracking-tight text-white sm:text-4xl"
                    >
                        مركز المحادثات
                    </h1>

                    <p
                        class="max-w-2xl mt-3 text-sm leading-7 text-slate-400"
                    >
                        تابع جميع محادثاتك، والرسائل، والملفات،
                        والتسجيلات الصوتية من مكان واحد.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if ($currentUser->role === 'admin')
                        <a
                            href="{{ route('users.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-bold text-white transition border rounded-2xl border-white/10 bg-white/5 hover:bg-white/10"
                        >
                            <svg
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            >
                                <circle cx="9" cy="8" r="4"/>
                                <path d="M2 21a7 7 0 0 1 14 0"/>
                                <path d="M19 8v6M16 11h6"/>
                            </svg>

                            بدء محادثة جديدة
                        </a>
                    @endif

                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-bold text-[#06111f] transition bg-blue-300 rounded-2xl hover:bg-blue-200"
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

                        لوحة التحكم
                    </a>
                </div>
            </div>

            {{-- رسائل النظام --}}
            @if (session('success'))
                <div
                    class="p-4 mb-6 text-sm font-bold border text-emerald-200 rounded-2xl border-emerald-500/20 bg-emerald-500/10"
                >
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="p-4 mb-6 border rounded-2xl border-rose-500/20 bg-rose-500/10"
                >
                    <p class="mb-2 font-bold text-rose-200">
                        حدث خطأ:
                    </p>

                    <ul
                        class="space-y-1 text-sm list-disc list-inside text-rose-100"
                    >
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- الإحصائيات --}}
            <div
                class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-4"
            >
                <div
                    class="p-5 border rounded-3xl border-white/10 bg-white/[.04] backdrop-blur-xl"
                >
                    <div
                        class="flex items-center justify-between"
                    >
                        <div>
                            <p class="text-xs font-bold text-slate-400">
                                إجمالي المحادثات
                            </p>

                            <p class="mt-2 text-3xl font-black text-white">
                                {{ $conversations->total() }}
                            </p>
                        </div>

                        <div
                            class="flex items-center justify-center w-12 h-12 text-blue-300 rounded-2xl bg-blue-500/10"
                        >
                            <svg
                                class="w-6 h-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="p-5 border rounded-3xl border-white/10 bg-white/[.04] backdrop-blur-xl"
                >
                    <div
                        class="flex items-center justify-between"
                    >
                        <div>
                            <p class="text-xs font-bold text-slate-400">
                                محادثات مباشرة
                            </p>

                            <p class="mt-2 text-3xl font-black text-white">
                                {{
                                    $conversations
                                        ->getCollection()
                                        ->where('type', 'direct')
                                        ->count()
                                }}
                            </p>
                        </div>

                        <div
                            class="flex items-center justify-center w-12 h-12 text-violet-300 rounded-2xl bg-violet-500/10"
                        >
                            <svg
                                class="w-6 h-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="9" cy="8" r="4"/>
                                <path d="M2 21a7 7 0 0 1 14 0"/>
                                <circle cx="18" cy="8" r="3"/>
                                <path d="M17 14a6 6 0 0 1 5 6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="p-5 border rounded-3xl border-white/10 bg-white/[.04] backdrop-blur-xl"
                >
                    <div
                        class="flex items-center justify-between"
                    >
                        <div>
                            <p class="text-xs font-bold text-slate-400">
                                محادثات الاستشارات
                            </p>

                            <p class="mt-2 text-3xl font-black text-white">
                                {{
                                    $conversations
                                        ->getCollection()
                                        ->where('type', 'consultation')
                                        ->count()
                                }}
                            </p>
                        </div>

                        <div
                            class="flex items-center justify-center w-12 h-12 text-cyan-300 rounded-2xl bg-cyan-500/10"
                        >
                            <svg
                                class="w-6 h-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect x="5" y="4" width="14" height="17" rx="2"/>
                                <path d="M8 9h8M8 13h8M8 17h5"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="p-5 border rounded-3xl border-white/10 bg-white/[.04] backdrop-blur-xl"
                >
                    <div
                        class="flex items-center justify-between"
                    >
                        <div>
                            <p class="text-xs font-bold text-slate-400">
                                الرسائل في الصفحة
                            </p>

                            <p class="mt-2 text-3xl font-black text-white">
                                {{
                                    $conversations
                                        ->getCollection()
                                        ->sum('messages_count')
                                }}
                            </p>
                        </div>

                        <div
                            class="flex items-center justify-center w-12 h-12 text-rose-300 rounded-2xl bg-rose-500/10"
                        >
                            <svg
                                class="w-6 h-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 5h16v12H8l-4 3V5Z"/>
                                <path d="M8 9h8M8 13h5"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- قائمة المحادثات --}}
            <section
                class="overflow-hidden border rounded-3xl border-white/10 bg-[#0d1829]/85 shadow-2xl backdrop-blur-xl"
            >
                <div
                    class="flex flex-col gap-4 p-5 border-b border-white/10 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h2 class="text-lg font-black text-white">
                            محادثاتك
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            أحدث المحادثات تظهر أولًا.
                        </p>
                    </div>

                    <div
                        class="flex items-center gap-2 px-4 py-2 border rounded-2xl border-white/10 bg-white/5"
                    >
                        <svg
                            class="w-4 h-4 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.2-3.2"/>
                        </svg>

                        <input
                            id="conversationSearch"
                            type="search"
                            placeholder="ابحث في المحادثات..."
                            class="w-full text-sm text-white bg-transparent border-0 sm:w-72 placeholder:text-slate-500 focus:ring-0"
                        >
                    </div>
                </div>

                <div
                    id="conversationList"
                    class="divide-y divide-white/5"
                >
                    @forelse ($conversations as $conversation)
                        @php
                            $otherParticipant = $conversation
                                ->participants
                                ->first(
                                    fn ($participant) =>
                                        (int) $participant->id
                                        !== (int) $currentUser->id
                                );

                            $title = $conversation->type === 'consultation'
                                ? (
                                    $conversation->consultation?->title
                                    ?? 'محادثة استشارة'
                                )
                                : (
                                    $otherParticipant?->name
                                    ?? 'محادثة مباشرة'
                                );

                            $subtitle = $conversation->type === 'consultation'
                                ? (
                                    $conversation->consultation
                                        ? 'استشارة رقم '
                                            . $conversation
                                                ->consultation
                                                ->consultation_number
                                        : 'محادثة استشارة'
                                )
                                : (
                                    $roleLabels[
                                        $otherParticipant?->role
                                    ] ?? 'مستخدم'
                                );

                            $lastMessage =
                                $conversation->latestMessage;

                            $lastMessageText = match (
                                $lastMessage?->message_type
                            ) {
                                'voice' => '🎤 تسجيل صوتي',
                                'image' => '🖼️ صورة',
                                'file' => '📎 ملف مرفق',
                                default =>
                                    $lastMessage?->message
                                    ?: 'لا توجد رسائل بعد',
                            };
                        @endphp

                        <a
                            href="{{ route(
                                'conversations.show',
                                $conversation
                            ) }}"
                            class="conversation-item group flex flex-col gap-4 p-5 transition hover:bg-white/[.04] sm:flex-row sm:items-center"
                            data-search="{{ mb_strtolower(
                                $title
                                . ' '
                                . $subtitle
                                . ' '
                                . ($otherParticipant?->name ?? '')
                            ) }}"
                        >
                            <div
                                class="flex items-center min-w-0 gap-4"
                            >
                                <div
                                    class="relative flex-none"
                                >
                                    <div
                                        class="overflow-hidden border w-14 h-14 rounded-2xl border-white/10 bg-gradient-to-br from-blue-500 to-violet-600"
                                    >
                                        @if ($otherParticipant?->profile_photo)
                                            <img
                                                src="{{ asset(
                                                    'storage/'
                                                    . $otherParticipant
                                                        ->profile_photo
                                                ) }}"
                                                alt="{{ $otherParticipant->name }}"
                                                class="object-cover w-full h-full"
                                            >
                                        @else
                                            <div
                                                class="flex items-center justify-center w-full h-full text-xl font-black text-white"
                                            >
                                                {{
                                                    mb_substr(
                                                        $title,
                                                        0,
                                                        1
                                                    )
                                                }}
                                            </div>
                                        @endif
                                    </div>

                                    <span
                                        class="absolute w-4 h-4 border-2 rounded-full -bottom-1 -left-1 border-[#0d1829] bg-slate-500"
                                    ></span>
                                </div>

                                <div class="min-w-0">
                                    <div
                                        class="flex flex-wrap items-center gap-2"
                                    >
                                        <h3
                                            class="font-black text-white truncate transition group-hover:text-blue-300"
                                        >
                                            {{ $title }}
                                        </h3>

                                        <span
                                            class="rounded-full px-2.5 py-1 text-[10px] font-bold
                                                {{
                                                    $conversation->type
                                                        === 'direct'
                                                        ? 'bg-violet-500/10 text-violet-300'
                                                        : 'bg-cyan-500/10 text-cyan-300'
                                                }}"
                                        >
                                            {{
                                                $conversation->type
                                                    === 'direct'
                                                    ? 'مباشرة'
                                                    : 'استشارة'
                                            }}
                                        </span>
                                    </div>

                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        {{ $subtitle }}
                                    </p>

                                    <p
                                        class="max-w-xl mt-2 text-sm truncate text-slate-300"
                                    >
                                        {{ $lastMessageText }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between gap-4 sm:mr-auto sm:flex-col sm:items-end"
                            >
                                <span
                                    class="text-xs text-slate-500"
                                >
                                    {{
                                        optional(
                                            $conversation->last_message_at
                                            ?? $conversation->updated_at
                                        )->diffForHumans()
                                    }}
                                </span>

                                <div class="flex items-center gap-2">
                                    <span
                                        class="rounded-full bg-white/5 px-3 py-1 text-[11px] font-bold text-slate-300"
                                    >
                                        {{ $conversation->messages_count }}
                                        رسالة
                                    </span>

                                    <span
                                        class="flex items-center justify-center w-9 h-9 text-blue-300 transition rounded-full bg-blue-500/10 group-hover:bg-blue-400 group-hover:text-[#06111f]"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path d="m9 18 6-6-6-6"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div
                            class="flex flex-col items-center justify-center px-5 py-24 text-center"
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
                                لا توجد محادثات حتى الآن
                            </h3>

                            <p
                                class="max-w-md mt-3 text-sm leading-7 text-slate-400"
                            >
                                ستظهر محادثات الاستشارات هنا بعد
                                تأكيد الدفع، ويمكن للمدير بدء محادثة
                                مباشرة من صفحة المستخدمين.
                            </p>

                            @if ($currentUser->role === 'admin')
                                <a
                                    href="{{ route('users.index') }}"
                                    class="inline-flex items-center gap-2 px-5 py-3 mt-6 text-sm font-bold text-[#06111f] bg-blue-300 rounded-2xl hover:bg-blue-200"
                                >
                                    اختيار مستخدم للمحادثة
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Pagination --}}
            @if ($conversations->hasPages())
                <div class="mt-8">
                    {{ $conversations->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {
                const searchInput =
                    document.getElementById(
                        'conversationSearch'
                    );

                const items =
                    document.querySelectorAll(
                        '.conversation-item'
                    );

                searchInput?.addEventListener(
                    'input',
                    function () {
                        const term =
                            this.value
                                .trim()
                                .toLocaleLowerCase('ar');

                        items.forEach((item) => {
                            const searchable =
                                (
                                    item.dataset.search
                                    || ''
                                ).toLocaleLowerCase('ar');

                            item.classList.toggle(
                                'hidden',
                                term
                                && ! searchable.includes(term)
                            );
                        });
                    }
                );
            }
        );
    </script>
</x-app-layout>
