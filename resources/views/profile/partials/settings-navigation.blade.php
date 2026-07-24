<div class="mb-8">
    <div
        class="p-3 border shadow-2xl rounded-3xl bg-slate-950/70 border-white/10 backdrop-blur-2xl"
    >
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">

            {{-- البيانات الشخصية --}}
            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold transition-all border rounded-2xl
                {{ request()->routeIs('profile.edit')
                    ? 'text-white border-cyan-400/30 bg-gradient-to-l from-cyan-500 to-blue-600 shadow-lg shadow-cyan-500/20'
                    : 'text-slate-300 border-white/10 bg-white/[0.03] hover:bg-white/[0.07] hover:text-white' }}"
            >
                <span>👤</span>
                <span>البيانات الشخصية</span>
            </a>

            {{-- كلمة المرور --}}
            <a
                href="{{ route('profile.password.edit') }}"
                class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold transition-all border rounded-2xl
                {{ request()->routeIs('profile.password.edit')
                    ? 'text-white border-cyan-400/30 bg-gradient-to-l from-cyan-500 to-blue-600 shadow-lg shadow-cyan-500/20'
                    : 'text-slate-300 border-white/10 bg-white/[0.03] hover:bg-white/[0.07] hover:text-white' }}"
            >
                <span>🔐</span>
                <span>كلمة المرور</span>
            </a>

            {{-- التخصص والنبذة --}}
            @if (auth()->user()->role === 'engineer')
                <a
                    href="{{ route('engineer.specialty.edit') }}"
                    class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold transition-all border rounded-2xl
                    {{ request()->routeIs('engineer.specialty.*')
                        ? 'text-white border-cyan-400/30 bg-gradient-to-l from-cyan-500 to-blue-600 shadow-lg shadow-cyan-500/20'
                        : 'text-slate-300 border-white/10 bg-white/[0.03] hover:bg-white/[0.07] hover:text-white' }}"
                >
                    <span>🧾</span>
                    <span>التخصص والنبذة</span>
                </a>
            @else
                <div
                    class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold border cursor-not-allowed rounded-2xl border-white/5 bg-white/[0.02] text-slate-600"
                >
                    <span>🧾</span>
                    <span>التخصص والنبذة</span>
                </div>
            @endif

            {{-- حذف الحساب --}}
            <a
                href="{{ route('profile.delete') }}"
                class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold transition-all border rounded-2xl
                {{ request()->routeIs('profile.delete')
                    ? 'text-white border-red-400/30 bg-gradient-to-l from-red-500 to-rose-600 shadow-lg shadow-red-500/20'
                    : 'text-red-200 border-red-400/20 bg-red-500/5 hover:bg-red-500/10 hover:text-white' }}"
            >
                <span>⚠️</span>
                <span>حذف الحساب</span>
            </a>

        </div>
    </div>
</div>
