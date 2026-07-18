<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">

            <div>
                <h2 class="text-xl font-bold text-white">
                    محادثة الاستشارة
                </h2>

                <p class="mt-1 text-sm text-slate-400">
                    {{ $consultation->consultation_number }}
                    —
                    {{ $consultation->title }}
                </p>
            </div>

            <a
                href="{{ url()->previous() }}"
                class="secondary-button"
            >
                رجوع
            </a>

        </div>
    </x-slot>

    <div class="py-10" dir="rtl">

        <div class="max-w-5xl px-4 mx-auto">

            @if(session('success'))
                <div
                    class="p-4 mb-6 border text-emerald-200 rounded-xl border-emerald-500/30 bg-emerald-500/10"
                >
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div
                    class="p-4 mb-6 text-red-200 border rounded-xl border-red-500/30 bg-red-500/10"
                >
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-5 mb-6 glass-panel rounded-2xl">

                <div class="grid gap-4 md:grid-cols-3">

                    <div>
                        <p class="text-sm text-slate-400">
                            العميل
                        </p>

                        <p class="mt-1 font-bold text-white">
                            {{ $consultation->customer->name ?? 'غير محدد' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-400">
                            المهندس
                        </p>

                        <p class="mt-1 font-bold text-white">
                            {{ $consultation->engineer->name ?? 'لم يتم التعيين' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-400">
                            حالة الاستشارة
                        </p>

                        <p class="mt-1 font-bold text-white">
                            {{ $consultation->status }}
                        </p>
                    </div>

                </div>

            </div>

            <div class="p-5 glass-panel rounded-2xl">

                <div class="space-y-5">

                    @forelse($consultation->messages as $message)

                        @php
                            $isMine = $message->sender_id === auth()->id();
                        @endphp

                        <div
                            class="flex {{ $isMine ? 'justify-start' : 'justify-end' }}"
                        >

                            <div
                                class="w-full max-w-2xl p-4 rounded-2xl
                                {{ $isMine
                                    ? 'bg-blue-500/15 border border-blue-500/30'
                                    : 'bg-white/5 border border-white/10'
                                }}"
                            >

                                <div
                                    class="flex flex-wrap items-center justify-between gap-3"
                                >

                                    <p class="font-bold text-white">
                                        {{ $message->sender->name ?? 'مستخدم' }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $message->created_at->format('Y-m-d H:i') }}
                                    </p>

                                </div>

                                @if($message->message)

                                    <p
                                        class="mt-3 leading-8 whitespace-pre-line text-slate-200"
                                    >
                                        {{ $message->message }}
                                    </p>

                                @endif

                                @if($message->attachment)

                                    <a
                                        href="{{ asset(
                                            'storage/' . $message->attachment
                                        ) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 mt-4 font-bold text-blue-200 border rounded-xl border-blue-500/30 bg-blue-500/10 hover:bg-blue-500/20"
                                    >
                                        فتح المرفق
                                    </a>

                                @endif

                            </div>

                        </div>

                    @empty

                        <div class="py-16 text-center">

                            <div class="mb-4 text-5xl">
                                💬
                            </div>

                            <h3 class="text-xl font-bold text-white">
                                لا توجد رسائل حتى الآن
                            </h3>

                            <p class="mt-2 text-slate-400">
                                ابدأ المحادثة بإرسال أول رسالة
                            </p>

                        </div>

                    @endforelse

                </div>

                <form
                    method="POST"
                    action="{{ route(
                        'consultations.messages.store',
                        $consultation
                    ) }}"
                    enctype="multipart/form-data"
                    class="pt-6 mt-8 border-t border-white/10"
                >
                    @csrf

                    <label
                        for="message"
                        class="block mb-2 font-bold text-slate-200"
                    >
                        الرسالة
                    </label>

                    <textarea
                        id="message"
                        name="message"
                        rows="4"
                        class="form-control"
                        placeholder="اكتب رسالتك هنا..."
                    >{{ old('message') }}</textarea>

                    <div class="mt-4">

                        <label
                            for="attachment"
                            class="block mb-2 font-bold text-slate-200"
                        >
                            إرفاق ملف
                        </label>

                        <input
                            id="attachment"
                            type="file"
                            name="attachment"
                            class="form-control"
                        >

                        <p class="mt-2 text-xs text-slate-500">
                            الحد الأقصى 10 ميجابايت.
                        </p>

                    </div>

                    <button
                        type="submit"
                        class="mt-5 primary-button"
                    >
                        إرسال الرسالة
                    </button>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
