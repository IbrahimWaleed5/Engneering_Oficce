@if (session('success'))

    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        class="flex items-start justify-between gap-4 p-4 mb-6 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10 backdrop-blur-xl"
    >

        <div class="flex items-start gap-3">

            <span class="text-xl">
                ✅
            </span>

            <p>
                {{ session('success') }}
            </p>

        </div>

        <button
            type="button"
            @click="show = false"
            class="text-green-200 hover:text-white"
        >
            ✕
        </button>

    </div>

@endif

@if (session('error'))

    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        class="flex items-start justify-between gap-4 p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10 backdrop-blur-xl"
    >

        <div class="flex items-start gap-3">

            <span class="text-xl">
                ⚠️
            </span>

            <p>
                {{ session('error') }}
            </p>

        </div>

        <button
            type="button"
            @click="show = false"
            class="text-red-200 hover:text-white"
        >
            ✕
        </button>

    </div>

@endif

@if ($errors->any())

    <div
        class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10 backdrop-blur-xl"
    >

        <div class="flex gap-3">

            <span class="text-xl">
                ⚠️
            </span>

            <div>

                <p class="mb-2 font-bold">
                    يرجى تصحيح الأخطاء التالية:
                </p>

                <ul class="space-y-1 text-sm list-disc list-inside">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    </div>

@endif
