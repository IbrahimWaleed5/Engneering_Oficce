<x-app-layout>
    @php
        $currentUser = auth()->user();
        $messages = $consultation->messages?->sortBy('created_at') ?? collect();
        $otherUser = $currentUser->role === 'customer'
            ? $consultation->engineer
            : $consultation->customer;

        $statusLabels = [
            'waiting_payment' => 'بانتظار الدفع',
            'pending' => 'قيد الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغاة',
        ];

        $canReviewEngineer =
            (int) $currentUser->id === (int) $consultation->customer_id
            && $consultation->engineer_id
            && $consultation->status === 'completed'
            && $consultation->payment_status === 'paid';
    @endphp

    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @endpush

    <style>
        .cp-page {
            --cp-primary: #b4c5ff;
            --cp-secondary: #ffb1c7;
            --cp-tertiary: #d2bbff;
            --cp-surface: #0b1326;
            --cp-low: #131b2e;
            --cp-container: #171f33;
            --cp-high: #222a3d;
            --cp-highest: #2d3449;
            --cp-outline: #434655;
            --cp-text: #dae2fd;
            --cp-muted: #c3c6d7;
            min-height: 100vh;
            background: var(--cp-surface);
            color: var(--cp-text);
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        .cp-glass {
            background: rgba(23, 31, 51, .64);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(141, 144, 160, .1);
        }

        .cp-neon {
            background: linear-gradient(135deg, #be0062 0%, #8343f4 100%);
        }

        .cp-scroll::-webkit-scrollbar { width: 6px; }
        .cp-scroll::-webkit-scrollbar-track { background: #0b1326; }
        .cp-scroll::-webkit-scrollbar-thumb { background: #2d3449; border-radius: 10px; }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        @media (max-width: 1023px) {
            .cp-desktop-sidebar { display: none !important; }
            .cp-main { margin-right: 0 !important; }
            .cp-topbar { right: 0 !important; }
        }
    </style>

    <div class="cp-page" dir="rtl">
        {{-- القائمة الجانبية: مطابقة للتصميم الأصلي --}}
        <aside class="cp-desktop-sidebar fixed right-0 top-0 z-50 hidden h-full w-64 flex-col border-l border-white/5 bg-[#171f33] py-8 shadow-lg lg:flex">
            <div class="flex items-center gap-3 px-6 mb-10">
                <div class="flex items-center justify-center w-10 h-10 cp-neon rounded-xl">
                    <span class="text-white material-symbols-outlined">psychology</span>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-[#b4c5ff]">نظام الاستشارات</h1>
                    <p class="text-[10px] text-[#c3c6d7]">لوحة التحكم الرئيسية</p>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449]">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>لوحة القيادة</span>
                </a>
                <a href="{{ route('consultations.mine') }}" class="flex items-center gap-4 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449]">
                    <span class="material-symbols-outlined">assignment</span>
                    <span>الطلبات</span>
                </a>
                <a href="#messagesContainer" class="flex items-center gap-4 rounded-xl border-r-4 border-[#b4c5ff] bg-[#b4c5ff]/5 px-4 py-3 text-[#b4c5ff] shadow-[0_0_15px_rgba(180,197,255,.15)]">
                    <span class="material-symbols-outlined">chat</span>
                    <span>المحادثات</span>
                </a>
                <a href="#sharedFiles" class="flex items-center gap-4 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449]">
                    <span class="material-symbols-outlined">folder</span>
                    <span>الملفات</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449]">
                    <span class="material-symbols-outlined">settings</span>
                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="px-4 mt-auto">
                <a href="{{ route('consultations.create') }}" class="flex items-center justify-center w-full gap-2 py-3 text-xs font-bold text-white transition cp-neon rounded-xl active:scale-95">
                    <span class="material-symbols-outlined">add</span>
                    <span>طلب جديد</span>
                </a>
            </div>
        </aside>

        <main class="min-h-screen pb-20 cp-main lg:mr-64 lg:pb-0">
            {{-- الشريط العلوي --}}
            <header class="cp-topbar fixed left-0 right-0 top-0 z-40 flex h-16 items-center justify-between border-b border-white/5 bg-[#0b1326]/80 px-6 shadow-sm backdrop-blur-md lg:right-64">
                <div class="hidden items-center rounded-full border border-white/5 bg-[#2d3449]/50 px-4 py-2 md:flex">
                    <span class="material-symbols-outlined ml-2 text-[#c3c6d7]">search</span>
                    <input class="w-64 border-none bg-transparent text-sm text-white placeholder:text-[#c3c6d7]/50 focus:ring-0" placeholder="بحث عن طلبات، عملاء..." type="text">
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <button class="relative flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] transition hover:bg-[#2d3449]/50">
                            <span class="material-symbols-outlined">notifications</span>
                            <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-[#ffb1c7]"></span>
                        </button>
                        <button class="flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] transition hover:bg-[#2d3449]/50">
                            <span class="material-symbols-outlined">settings</span>
                        </button>
                        <button id="toggleConsultationDetails" class="flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] transition hover:bg-[#2d3449]/50">
                            <span class="material-symbols-outlined">help</span>
                        </button>
                    </div>

                    <div class="w-px h-8 mx-2 bg-white/10"></div>

                    <div class="flex items-center gap-3">
                        <div class="hidden text-left sm:block">
                            <p class="text-xs font-bold text-[#dae2fd]">{{ $currentUser->name }}</p>
                            <p class="text-[10px] text-[#c3c6d7]">{{ $currentUser->role }}</p>
                        </div>
                        <div class="h-10 w-10 overflow-hidden rounded-full border-2 border-[#b4c5ff]/20">
                            @if ($currentUser->profile_photo)
                                <img class="object-cover w-full h-full" src="{{ asset('storage/' . $currentUser->profile_photo) }}" alt="{{ $currentUser->name }}">
                            @else
                                <div class="flex items-center justify-center w-full h-full font-bold text-white cp-neon">{{ mb_substr($currentUser->name, 0, 1) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <div class="w-full px-4 pt-24 pb-10 mx-auto space-y-8 max-w-7xl sm:px-6">
                {{-- عنوان الصفحة --}}
                <section class="flex flex-col gap-1">
                    <h2 class="text-3xl font-bold text-[#dae2fd]">أهلًا بك، {{ $currentUser->name }}</h2>
                    <div class="flex items-center gap-2 text-[#c3c6d7]">
                        <span class="material-symbols-outlined text-sm text-[#b4c5ff]">schedule</span>
                        <p>أنت الآن داخل محادثة الاستشارة رقم {{ $consultation->consultation_number }}.</p>
                    </div>
                </section>

                {{-- أعلى الصفحة --}}
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="cp-neon relative flex min-h-[250px] flex-col justify-between overflow-hidden rounded-3xl p-8 shadow-2xl lg:col-span-2">
                        <div class="absolute rounded-full -right-20 -top-20 h-80 w-80 bg-white/10 blur-3xl"></div>
                        <div class="relative z-10 flex items-start justify-between">
                            <div>
                                <span class="inline-block px-4 py-1 mb-4 text-xs font-bold text-white rounded-full bg-white/20 backdrop-blur-md">استشارة نشطة</span>
                                <h3 class="text-3xl font-bold text-white">{{ $consultation->title }}</h3>
                                <p class="max-w-md mt-2 text-white/80">{{ $consultation->description }}</p>
                            </div>
                            <span class="text-6xl text-white material-symbols-outlined opacity-30">rocket_launch</span>
                        </div>
                        <div class="relative z-10 flex items-center gap-4 mt-8">
                            <a href="#messagesContainer" class="rounded-xl bg-white px-6 py-2.5 text-xs font-bold text-[#2563eb] transition hover:shadow-lg">فتح المحادثة</a>
                            <button id="toggleConsultationDetailsHero" class="rounded-xl border border-white/30 px-6 py-2.5 text-xs font-bold text-white transition hover:bg-white/10">عرض التفاصيل</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex items-center justify-between p-6 cp-glass rounded-3xl">
                            <div>
                                <p class="text-xs font-bold uppercase text-[#c3c6d7]">الرسائل</p>
                                <h4 class="mt-1 text-2xl font-semibold text-[#b4c5ff]">{{ $messages->count() }}</h4>
                                <p class="mt-2 text-xs text-[#c3c6d7]">إجمالي رسائل الاستشارة</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#b4c5ff]/10 text-[#b4c5ff]">
                                <span class="material-symbols-outlined">chat</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-6 cp-glass rounded-3xl">
                            <div>
                                <p class="text-xs font-bold uppercase text-[#c3c6d7]">حالة الاستشارة</p>
                                <h4 class="mt-1 text-lg font-semibold text-[#ffb1c7]">{{ $statusLabels[$consultation->status] ?? $consultation->status }}</h4>
                                <p class="mt-2 text-xs text-[#c3c6d7]">{{ $consultation->payment_status === 'paid' ? 'تم تأكيد الدفع' : 'الدفع غير مؤكد' }}</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#ffb1c7]/10 text-[#ffb1c7]">
                                <span class="material-symbols-outlined">insights</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- المحادثة الرئيسية --}}
                <section class="overflow-hidden cp-glass rounded-3xl">
                    <div class="flex items-center justify-between p-6 border-b border-white/5">
                        <div class="flex items-center gap-4">
                            @if ($otherUser && $otherUser->role === 'engineer')
                                <a href="{{ route('engineers.show', ['user' => $otherUser->id]) }}" class="relative">
                            @else
                                <div class="relative">
                            @endif
                                <div class="w-16 h-16 overflow-hidden shadow-lg rounded-2xl">
                                    @if ($otherUser?->profile_photo)
                                        <img class="object-cover w-full h-full" src="{{ asset('storage/' . $otherUser->profile_photo) }}" alt="{{ $otherUser?->name }}">
                                    @else
                                        <div class="flex items-center justify-center w-full h-full text-xl font-bold text-white cp-neon">{{ mb_substr($otherUser?->name ?? 'م', 0, 1) }}</div>
                                    @endif
                                </div>
                                <span class="presence-dot absolute -bottom-1 -left-1 h-4 w-4 rounded-full border-2 border-[#171f33] bg-slate-500"></span>
                            @if ($otherUser && $otherUser->role === 'engineer')
                                </a>
                            @else
                                </div>
                            @endif

                            <div>
                                @if ($otherUser && $otherUser->role === 'engineer')
                                    <a href="{{ route('engineers.show', ['user' => $otherUser->id]) }}" class="font-bold text-[#dae2fd] transition hover:text-[#b4c5ff]">{{ $otherUser->name }}</a>
                                @else
                                    <h5 class="font-bold text-[#dae2fd]">{{ $otherUser?->name ?? 'المستخدم' }}</h5>
                                @endif
                                <p class="text-sm text-[#c3c6d7]">{{ $otherUser?->role === 'engineer' ? 'المهندس المسؤول عن الاستشارة' : 'صاحب الاستشارة' }}</p>
                                <p class="mt-1 text-xs text-[#c3c6d7]"><span class="presence-status">غير متصل</span><span id="headerTypingStatus" class="hidden text-[#b4c5ff]"> · يكتب الآن...</span></p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            @if ($otherUser && $otherUser->role === 'engineer')
                                <a href="{{ route('engineers.show', ['user' => $otherUser->id]) }}" class="rounded-xl bg-[#b4c5ff]/10 p-2 text-[#b4c5ff] transition hover:bg-[#b4c5ff]/20">
                                    <span class="material-symbols-outlined">person</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div id="messagesContainer" class="cp-scroll h-[560px] overflow-y-auto p-4 sm:p-6">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="flex-1 h-px bg-white/10"></div>
                            <span class="rounded-full border border-white/10 bg-[#060e20]/60 px-4 py-2 text-xs font-bold text-[#c3c6d7]">{{ $consultation->created_at?->format('Y-m-d') }}</span>
                            <div class="flex-1 h-px bg-white/10"></div>
                        </div>

                        <div id="messagesList" class="space-y-4">
                            @forelse ($messages as $message)
                                @php
                                    $isMine = (int) $message->sender_id === (int) auth()->id();
                                    $sender = $message->sender;
                                    $extension = $message->attachment ? strtolower(pathinfo($message->attachment, PATHINFO_EXTENSION)) : null;
                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                                @endphp

                                <div data-message-id="{{ $message->id }}" class="flex items-end gap-3 {{ $isMine ? 'flex-row-reverse' : '' }}">
                                    <div class="flex-none overflow-hidden border rounded-full h-9 w-9 border-white/10">
                                        @if ($sender?->profile_photo)
                                            <img class="object-cover w-full h-full" src="{{ asset('storage/' . $sender->profile_photo) }}" alt="{{ $sender->name }}">
                                        @else
                                            <div class="flex items-center justify-center w-full h-full text-xs font-bold text-white cp-neon">{{ mb_substr($sender?->name ?? 'م', 0, 1) }}</div>
                                        @endif
                                    </div>

                                    <div class="max-w-[82%] sm:max-w-[62%]">
                                        <div class="rounded-2xl px-4 py-3 shadow-lg {{ $isMine ? 'rounded-br-md bg-gradient-to-br from-[#2563eb] to-[#8343f4] text-white' : 'rounded-bl-md border border-white/5 bg-[#222a3d] text-[#dae2fd]' }}">
                                            <div class="flex items-center justify-between gap-4 mb-2">
                                                <p class="text-xs font-bold">{{ $isMine ? 'أنت' : ($sender?->name ?? 'المستخدم') }}</p>
                                                <span class="text-[10px] opacity-60">{{ $message->created_at?->format('H:i') }}</span>
                                            </div>

                                            @if ($message->message)
                                                <p class="text-sm leading-7 whitespace-pre-line">{{ $message->message }}</p>
                                            @endif

                                            @if ($message->attachment)
                                                @if ($isImage)
                                                    <a href="{{ route('consultations.messages.attachment', [$consultation, $message]) }}" target="_blank" rel="noopener noreferrer" class="block mt-3 overflow-hidden border rounded-2xl border-white/10">
                                                        <img src="{{ route('consultations.messages.attachment', [$consultation, $message]) }}" alt="مرفق" class="object-cover w-full max-h-64">
                                                    </a>
                                                @else
                                                    <a href="{{ route('consultations.messages.attachment', [$consultation, $message]) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between gap-4 p-3 mt-3 border rounded-2xl border-white/10 bg-black/15">
                                                        <div class="flex items-center min-w-0 gap-3">
                                                            <div class="flex h-11 w-11 flex-none items-center justify-center rounded-xl bg-[#8343f4]/20 text-xs font-bold text-[#d2bbff]">{{ strtoupper($extension ?: 'FILE') }}</div>
                                                            <div class="min-w-0">
                                                                <p class="text-sm font-bold truncate">{{ basename($message->attachment) }}</p>
                                                                <p class="mt-1 text-xs opacity-60">اضغط لفتح الملف</p>
                                                            </div>
                                                        </div>
                                                        <span class="material-symbols-outlined">download</span>
                                                    </a>
                                                @endif
                                            @endif

                                            @if ($isMine)
                                                <div class="mt-2 text-left text-xs text-[#b4c5ff]">✓✓</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="flex h-[420px] flex-col items-center justify-center text-center">
                                    <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-[#b4c5ff]/10 text-4xl">💬</div>
                                    <h3 class="text-xl font-bold text-white">لا توجد رسائل حتى الآن</h3>
                                    <p class="mt-2 text-sm text-[#c3c6d7]">ابدأ المحادثة بإرسال أول رسالة</p>
                                </div>
                            @endforelse
                        </div>

                        <div id="typingIndicator" class="mt-5 hidden text-sm font-bold text-[#b4c5ff]">يكتب الآن...</div>
                    </div>

                    <div class="border-t border-white/5 bg-[#131b2e] p-4">
                        <form id="chatForm" method="POST" action="{{ route('consultations.messages.store', $consultation) }}" enctype="multipart/form-data" class="space-y-3" x-data="{ fileName: '', selectFile(event) { this.fileName = event.target.files[0] ? event.target.files[0].name : ''; } }">
                            @csrf
                            <div class="flex items-end gap-2 rounded-3xl border border-white/10 bg-[#222a3d] p-2">
                                <textarea id="message" name="message" rows="1" placeholder="اكتب رسالتك هنا..." class="min-h-[48px] max-h-32 flex-1 resize-none border-0 bg-transparent px-3 py-3 text-sm text-white placeholder:text-[#c3c6d7]/50 focus:ring-0">{{ old('message') }}</textarea>
                                <label for="attachment" class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-xl bg-white/5 text-[#c3c6d7] transition hover:bg-white/10" title="إرفاق ملف">
                                    <span class="material-symbols-outlined">attach_file</span>
                                </label>
                                <input id="attachment" type="file" name="attachment" class="hidden" accept=".pdf,.dwg,.jpg,.jpeg,.png,.webp,.doc,.docx,.xls,.xlsx,.zip" @change="selectFile($event)">
                                <button id="sendButton" type="submit" class="flex items-center justify-center flex-none text-white transition rounded-full shadow-lg cp-neon h-11 w-11 hover:scale-105" aria-label="إرسال">
                                    <span class="material-symbols-outlined">send</span>
                                </button>
                            </div>
                            <span x-show="fileName" x-text="fileName" class="block max-w-full truncate text-xs text-[#c3c6d7]"></span>
                        </form>
                    </div>
                </section>

                {{-- الملفات والتفاصيل --}}
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <section id="sharedFiles" class="p-6 cp-glass rounded-3xl">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[#d2bbff]">folder_shared</span>
                                <h3 class="text-xl font-semibold">الملفات المشتركة</h3>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @forelse ($messages->whereNotNull('attachment')->take(5) as $fileMessage)
                                @php $fileExtension = strtolower(pathinfo($fileMessage->attachment, PATHINFO_EXTENSION)); @endphp
                                <a href="{{ route('consultations.messages.attachment', [$consultation, $fileMessage]) }}" target="_blank" rel="noopener noreferrer" class="group flex items-center justify-between rounded-2xl border border-transparent p-3 transition hover:border-white/10 hover:bg-[#2d3449]/30">
                                    <div class="flex items-center min-w-0 gap-4">
                                        <div class="flex h-12 w-12 flex-none items-center justify-center rounded-xl bg-[#8343f4]/20 text-xs font-bold text-[#d2bbff]">{{ strtoupper($fileExtension ?: 'FILE') }}</div>
                                        <div class="min-w-0">
                                            <p class="truncate font-bold text-[#dae2fd]">{{ basename($fileMessage->attachment) }}</p>
                                            <p class="text-xs text-[#c3c6d7]">{{ $fileMessage->created_at?->format('Y-m-d H:i') }}</p>
                                        </div>
                                    </div>
                                    <span class="material-symbols-outlined text-[#c3c6d7] opacity-0 transition group-hover:opacity-100">download</span>
                                </a>
                            @empty
                                <p class="py-8 text-center text-sm text-[#c3c6d7]">لا توجد ملفات مشتركة</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="p-6 cp-glass rounded-3xl">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="material-symbols-outlined text-[#b4c5ff]">analytics</span>
                            <h3 class="text-xl font-semibold">تفاصيل الاستشارة</h3>
                        </div>

                        <div class="space-y-5">
                            <div class="flex items-center justify-between pb-4 border-b border-white/5">
                                <span class="text-[#c3c6d7]">رقم الطلب</span>
                                <span class="text-xs font-bold text-[#dae2fd]">{{ $consultation->consultation_number }}</span>
                            </div>
                            <div class="flex items-center justify-between pb-4 border-b border-white/5">
                                <span class="text-[#c3c6d7]">نوع الخدمة</span>
                                <span class="rounded-full bg-[#8343f4]/20 px-3 py-1 text-xs font-bold text-[#d2bbff]">{{ $consultation->consultationType?->name ?? 'غير محدد' }}</span>
                            </div>
                            <div class="flex items-center justify-between pb-4 border-b border-white/5">
                                <span class="text-[#c3c6d7]">تاريخ الطلب</span>
                                <span class="text-xs font-bold text-[#dae2fd]">{{ $consultation->created_at?->format('Y-m-d') }}</span>
                            </div>
                            <div class="flex items-center justify-between pb-4 border-b border-white/5">
                                <span class="text-[#c3c6d7]">الحالة</span>
                                <span class="text-xs font-bold text-[#dae2fd]">{{ $statusLabels[$consultation->status] ?? $consultation->status }}</span>
                            </div>
                            <div class="rounded-2xl bg-[#2d3449]/20 p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-sm text-[#b4c5ff]">lightbulb</span>
                                    <span class="text-xs font-bold text-[#b4c5ff]">وصف الاستشارة</span>
                                </div>
                                <p class="text-xs leading-relaxed text-[#c3c6d7]">{{ $consultation->description }}</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>

        {{-- Drawer التفاصيل للجوال --}}
        <div id="consultationDetailsBackdrop" class="fixed inset-0 z-40 hidden bg-[#060e20]/70 backdrop-blur-sm lg:hidden"></div>
        <div id="consultationDetailsDrawer" class="fixed inset-y-0 right-0 z-50 hidden w-[92%] max-w-sm overflow-y-auto border-l border-white/10 bg-[#0b1326] p-5 shadow-2xl lg:hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold">تفاصيل الاستشارة</h3>
                <button id="closeConsultationDetails" type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 text-[#c3c6d7]">×</button>
            </div>
            <div class="space-y-4">
                <div class="rounded-2xl border border-white/10 bg-white/[.04] p-4"><p class="text-xs text-[#c3c6d7]">رقم الطلب</p><p class="mt-2 font-bold">{{ $consultation->consultation_number }}</p></div>
                <div class="rounded-2xl border border-white/10 bg-white/[.04] p-4"><p class="text-xs text-[#c3c6d7]">العنوان</p><p class="mt-2 font-bold">{{ $consultation->title }}</p></div>
                <div class="rounded-2xl border border-white/10 bg-white/[.04] p-4"><p class="text-xs text-[#c3c6d7]">الحالة</p><p class="mt-2 font-bold">{{ $statusLabels[$consultation->status] ?? $consultation->status }}</p></div>
            </div>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function () {
            const consultationId = @json($consultation->id);
            const currentUserId = @json(auth()->id());
            const form = document.getElementById('chatForm');
            const textarea = document.getElementById('message');
            const attachment = document.getElementById('attachment');
            const sendButton = document.getElementById('sendButton');
            const messagesContainer =
                document.getElementById('messagesContainer');
            const messagesList =
                document.getElementById('messagesList');
            const typingIndicator =
                document.getElementById('typingIndicator');
            const presenceDots =
                document.querySelectorAll('.presence-dot');
            const presenceStatuses =
                document.querySelectorAll('.presence-status');
            const headerTypingStatus =
                document.getElementById('headerTypingStatus');
            const toggleConsultationDetails =
                document.getElementById('toggleConsultationDetails');
            const consultationDetailsDrawer =
                document.getElementById('consultationDetailsDrawer');
            const consultationDetailsBackdrop =
                document.getElementById('consultationDetailsBackdrop');
            const closeConsultationDetails =
                document.getElementById('closeConsultationDetails');

            let channel = null;
            let typingTimer = null;

            const openDetails = () => {
                consultationDetailsDrawer?.classList.remove('hidden');
                consultationDetailsBackdrop?.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            const closeDetails = () => {
                consultationDetailsDrawer?.classList.add('hidden');
                consultationDetailsBackdrop?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            toggleConsultationDetails?.addEventListener('click', openDetails);
            closeConsultationDetails?.addEventListener('click', closeDetails);
            consultationDetailsBackdrop?.addEventListener('click', closeDetails);

            const scrollToBottom = () => {
                if (messagesContainer) {
                    messagesContainer.scrollTop =
                        messagesContainer.scrollHeight;
                }
            };

            const escapeHtml = (value) => {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            };

            const setPresence = (online) => {
                presenceDots.forEach((dot) => {
                    dot.classList.toggle('bg-green-400', online);
                    dot.classList.toggle('bg-[#3A4A66]', !online);
                });

                presenceStatuses.forEach((status) => {
                    status.textContent = online
                        ? 'نشط الآن'
                        : 'غير متصل';

                    status.classList.toggle(
                        'text-green-400',
                        online
                    );
                });
            };

            const messageExists = (id) => {
                return document.querySelector(
                    `[data-message-id="${id}"]`
                ) !== null;
            };

            const appendMessage = (message, mine) => {
                if (!messagesList || messageExists(message.id)) {
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.dataset.messageId = message.id;
                wrapper.className =
                    `flex items-end gap-3 ${
                        mine
                            ? 'flex-row-reverse justify-start'
                            : 'justify-start'
                    }`;

                const initial = escapeHtml(
                    (message.sender_name || 'م').charAt(0)
                );

                let avatar = `
                    <div class="flex-none">
                        ${
                            message.sender_profile_photo_url
                                ? `<img
                                    src="${escapeHtml(
                                        message.sender_profile_photo_url
                                    )}"
                                    alt="${escapeHtml(
                                        message.sender_name
                                    )}"
                                    class="object-cover w-8 h-8 border rounded-full border-[#25344C]"
                                >`
                                : `<div
                                    class="flex items-center justify-center w-8 h-8 text-xs font-black text-white border rounded-full border-[#25344C] ${
                                        mine
                                            ? 'bg-gradient-to-br from-[#FF6B5B] to-[#8B7CF6]'
                                            : 'bg-gradient-to-br from-[#8B7CF6] to-[#5B4BB8]'
                                    }"
                                >${initial}</div>`
                        }
                    </div>
                `;

                let attachmentHtml = '';

                if (message.attachment_url) {
                    if (message.attachment_is_image) {
                        attachmentHtml = `
                            <a
                                href="${escapeHtml(
                                    message.attachment_url
                                )}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-block mt-3 overflow-hidden border rounded-2xl border-[#25344C] bg-black/10"
                            >
                                <img
                                    src="${escapeHtml(
                                        message.attachment_url
                                    )}"
                                    alt="مرفق"
                                    class="object-cover w-auto max-w-[220px] sm:max-w-[280px] max-h-56"
                                >
                            </a>
                        `;
                    } else {
                        attachmentHtml = `
                            <a
                                href="${escapeHtml(
                                    message.attachment_url
                                )}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center justify-between gap-4 p-3 mt-4 transition border rounded-2xl border-[#25344C] bg-black/15 hover:bg-black/25"
                            >
                                <div class="flex items-center min-w-0 gap-3">
                                    <div
                                        class="flex items-center justify-center flex-none text-xs font-black w-11 h-11 rounded-xl bg-[#8B7CF6]/20 text-[#D9D2FF]"
                                    >
                                        ${escapeHtml(
                                            (
                                                message.attachment_extension
                                                || 'FILE'
                                            ).toUpperCase()
                                        )}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-sm font-bold truncate">
                                            ${escapeHtml(
                                                message.attachment_name
                                            )}
                                        </p>
                                        <p class="mt-1 text-xs opacity-60">
                                            اضغط لفتح الملف
                                        </p>
                                    </div>
                                </div>

                                <span
                                    class="flex items-center justify-center flex-none w-10 h-10 border rounded-full border-[#25344C]"
                                >↓</span>
                            </a>
                        `;
                    }
                }

                wrapper.innerHTML = `
                    ${avatar}

                    <div class="w-auto max-w-[84%] sm:max-w-[58%]">
                        <div
                            class="px-4 py-2.5 shadow-lg rounded-[20px] ${
                                mine
                                    ? 'rounded-br-md bg-gradient-to-br from-[#FF6B5B] to-[#8B7CF6] text-white ring-1 ring-white/10'
                                    : 'rounded-bl-md border border-[#25344C] bg-[#16233A]/85 text-[#F4F1FA] backdrop-blur-xl'
                            }"
                        >
                            <div
                                class="flex items-center justify-between gap-4 mb-1.5"
                            >
                                <p class="text-sm font-black">
                                    ${
                                        mine
                                            ? 'أنت'
                                            : escapeHtml(
                                                message.sender_name
                                            )
                                    }
                                </p>

                                <span
                                    class="text-[11px] ${
                                        mine
                                            ? 'text-white/70'
                                            : 'text-[#6B7A93]'
                                    }"
                                >
                                    ${escapeHtml(message.time)}
                                </span>
                            </div>

                            ${
                                message.body
                                    ? `<p
                                        class="text-sm leading-6 whitespace-pre-line sm:text-[15px]"
                                    >${escapeHtml(message.body)}</p>`
                                    : ''
                            }

                            ${attachmentHtml}

                            ${
                                mine
                                    ? `<div
                                        class="mt-2 text-xs text-left text-white/70"
                                    >✓</div>`
                                    : ''
                            }
                        </div>
                    </div>
                `;

                messagesList.appendChild(wrapper);
                scrollToBottom();
            };

            scrollToBottom();

            if (window.Echo) {
                channel = window.Echo.join(
                    `consultation.${consultationId}`
                )
                    .here((users) => {
                        setPresence(
                            users.some(
                                (user) =>
                                    Number(user.id)
                                    !== Number(currentUserId)
                            )
                        );
                    })
                    .joining((user) => {
                        if (
                            Number(user.id)
                            !== Number(currentUserId)
                        ) {
                            setPresence(true);
                        }
                    })
                    .leaving((user) => {
                        if (
                            Number(user.id)
                            !== Number(currentUserId)
                        ) {
                            setPresence(false);
                        }
                    })
                    .listen(
                        '.consultation.message.sent',
                        (event) => {
                            appendMessage(
                                event.message,
                                Number(event.message.sender_id)
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

                            typingIndicator?.classList.remove(
                                'hidden'
                            );
                            headerTypingStatus?.classList.remove(
                                'hidden'
                            );

                            clearTimeout(typingTimer);

                            typingTimer = setTimeout(() => {
                                typingIndicator?.classList.add(
                                    'hidden'
                                );
                                headerTypingStatus?.classList.add(
                                    'hidden'
                                );
                            }, 1500);
                        }
                    );
            }

            textarea?.addEventListener('input', () => {
                channel?.whisper('typing', {
                    user_id: currentUserId,
                });
            });

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();

                const hasMessage =
                    textarea?.value.trim().length > 0;
                const hasAttachment =
                    attachment?.files?.length > 0;

                if (!hasMessage && !hasAttachment) {
                    return;
                }

                sendButton.disabled = true;
                sendButton.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );

                try {
                    const formData = new FormData(form);

                    const response = await window.axios.post(
                        form.action,
                        formData,
                        {
                            headers: {
                                Accept: 'application/json',
                                'Content-Type':
                                    'multipart/form-data',
                            },
                        }
                    );

                    appendMessage(
                        response.data.message,
                        true
                    );

                    form.reset();

                    if (window.Alpine) {
                        form.dispatchEvent(
                            new Event(
                                'reset',
                                { bubbles: true }
                            )
                        );
                    }
                } catch (error) {
                    const errors =
                        error.response?.data?.errors;

                    const message = errors
                        ? Object.values(errors)
                            .flat()
                            .join('\n')
                        : 'تعذر إرسال الرسالة. حاول مرة أخرى.';

                    alert(message);
                } finally {
                    sendButton.disabled = false;
                    sendButton.classList.remove(
                        'opacity-60',
                        'cursor-not-allowed'
                    );
                }
            });
        });
    </script>
</x-app-layout>
