<x-app-layout>
    <div class="py-10" dir="rtl">
        <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70 sm:p-8">
                <div class="mb-8">
                    <p class="text-sm font-bold text-cyan-300">
                        خدمة تسجيل المكاتب الهندسية
                    </p>

                    <h1 class="mt-2 text-3xl font-black text-white">
                        طلب تسجيل مكتب هندسي
                    </h1>

                    <p class="mt-3 leading-8 text-slate-400">
                        أدخل بيانات المكتب وارفع المستندات المطلوبة. بعد مراجعة الطلب وقبوله،
                        يحدد مدير النظام قيمة الاشتراك ومدته، ثم تظهر لك خطوة رفع إيصال الدفع.
                    </p>
                </div>

                @if (session('success'))
                    <div class="p-4 mb-6 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="p-4 mb-6 border text-cyan-100 rounded-2xl border-cyan-500/20 bg-cyan-500/10">
                        {{ session('info') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                        <ul class="space-y-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('office-applications.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 font-bold text-white">اسم المكتب</label>
                            <input type="text" name="office_name" value="{{ old('office_name') }}" required
                                   class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800">
                        </div>

                        <div>
                            <label class="block mb-2 font-bold text-white">البريد الإلكتروني للمكتب</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                   class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800">
                        </div>

                        <div>
                            <label class="block mb-2 font-bold text-white">رقم الهاتف</label>
                            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                                   class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800">
                        </div>

                        <div>
                            <label class="block mb-2 font-bold text-white">رقم السجل التجاري</label>
                            <input type="text" name="commercial_registration" value="{{ old('commercial_registration') }}" required
                                   class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800">
                        </div>

                        <div>
                            <label class="block mb-2 font-bold text-white">رقم الترخيص</label>
                            <input type="text" name="license_number" value="{{ old('license_number') }}" required
                                   class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800">
                        </div>

                        <div>
                            <label class="block mb-2 font-bold text-white">الدولة</label>
                            <input type="text" name="country" value="{{ old('country', 'السعودية') }}" required
                                   class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800">
                        </div>

                        <div>
                            <label class="block mb-2 font-bold text-white">المدينة</label>
                            <input type="text" name="city" value="{{ old('city') }}" required
                                   class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800">
                        </div>

                        <div>
                            <label class="block mb-2 font-bold text-white">ملف السجل التجاري</label>
                            <input type="file" name="commercial_registration_file"
                                   accept=".pdf,.jpg,.jpeg,.png,.webp" required
                                   class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800">
                            <p class="mt-2 text-xs text-slate-400">PDF أو صورة، بحد أقصى 10 ميجابايت.</p>
                        </div>

                        <div>
                            <label class="block mb-2 font-bold text-white">ملف ترخيص المكتب</label>
                            <input type="file" name="license_document"
                                   accept=".pdf,.jpg,.jpeg,.png,.webp" required
                                   class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800">
                            <p class="mt-2 text-xs text-slate-400">PDF أو صورة، بحد أقصى 10 ميجابايت.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 font-bold text-white">عنوان المكتب</label>
                        <textarea name="address" rows="3" required
                                  class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800">{{ old('address') }}</textarea>
                    </div>

                    <div>
                        <label class="block mb-2 font-bold text-white">نبذة عن المكتب أو ملاحظات</label>
                        <textarea name="notes" rows="5"
                                  class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit"
                            class="w-full px-6 py-3 font-black text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500 sm:w-auto">
                        إرسال طلب المكتب
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
