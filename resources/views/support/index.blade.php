<x-app-layout>
    <div class="min-h-screen py-10 bg-slate-950" dir="rtl">
        <div class="max-w-3xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="p-6 border shadow-2xl rounded-3xl border-white/10 bg-slate-900 sm:p-8">
                <h1 class="text-3xl font-black text-white">فتح تذكرة دعم</h1>

                <p class="mt-2 text-slate-400">
                    سيتم إرسال التذكرة إلى:
                    <span class="font-bold text-blue-300">
                        {{ $setting->supportEmployee->name }}
                    </span>
                </p>

                @if (session('error'))
                    <div class="p-4 mt-5 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ session('error') }}
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('support.store') }}"
                    enctype="multipart/form-data"
                    class="mt-8 space-y-6"
                >
                    @csrf

                    <div>
                        <label for="subject" class="block mb-2 font-bold text-slate-300">
                            عنوان المشكلة
                        </label>

                        <input
                            id="subject"
                            name="subject"
                            type="text"
                            value="{{ old('subject') }}"
                            required
                            maxlength="255"
                            class="w-full px-4 py-3 text-white border rounded-xl border-slate-700 bg-slate-950"
                        >

                        @error('subject')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block mb-2 font-bold text-slate-300">
                            الأولوية
                        </label>

                        <select
                            id="priority"
                            name="priority"
                            required
                            class="w-full px-4 py-3 text-white border rounded-xl border-slate-700 bg-slate-950"
                        >
                            <option value="low">منخفضة</option>
                            <option value="medium" selected>متوسطة</option>
                            <option value="high">مرتفعة</option>
                            <option value="urgent">عاجلة</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block mb-2 font-bold text-slate-300">
                            تفاصيل المشكلة
                        </label>

                        <textarea
                            id="message"
                            name="message"
                            rows="7"
                            class="w-full px-4 py-3 text-white border resize-none rounded-xl border-slate-700 bg-slate-950"
                        >{{ old('message') }}</textarea>

                        @error('message')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="attachment" class="block mb-2 font-bold text-slate-300">
                            مرفق اختياري
                        </label>

                        <input
                            id="attachment"
                            name="attachment"
                            type="file"
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.zip"
                            class="w-full px-4 py-3 border rounded-xl border-slate-700 bg-slate-950 text-slate-300"
                        >

                        @error('attachment')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            type="submit"
                            class="flex-1 px-6 py-3 font-black text-white bg-blue-600 rounded-xl"
                        >
                            إرسال التذكرة
                        </button>

                        <a
                            href="{{ route('support.index') }}"
                            class="px-6 py-3 font-bold text-center border rounded-xl border-slate-700 text-slate-300"
                        >
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
