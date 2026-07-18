<section class="overflow-hidden glass-panel-strong rounded-[2rem]">

    <div class="p-6 border-b border-white/10">

        <h3 class="text-xl font-black text-white">
            {{ $title }}
        </h3>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-white/5 text-slate-400">

                <tr>

                    <th class="px-6 py-4 text-right">
                        الرقم
                    </th>

                    <th class="px-6 py-4 text-right">
                        العنوان
                    </th>

                    <th class="px-6 py-4 text-right">
                        النوع
                    </th>

                    <th class="px-6 py-4 text-right">
                        الحالة
                    </th>

                    <th class="px-6 py-4 text-right">
                        التاريخ
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-white/10">

                @forelse ($consultations as $consultation)

                    <tr class="transition hover:bg-white/5">

                        <td class="px-6 py-4 font-bold text-white">
                            {{ $consultation->consultation_number }}
                        </td>

                        <td class="px-6 py-4 text-slate-300">
                            {{ $consultation->title }}
                        </td>

                        <td class="px-6 py-4 text-slate-300">
                            {{ $consultation->consultationType?->name ?? '—' }}
                        </td>

                        <td class="px-6 py-4">

                            @include(
                                'dashboard.partials.status-badge',
                                ['status' => $consultation->status]
                            )

                        </td>

                        <td class="px-6 py-4 text-slate-400">
                            {{ $consultation->created_at?->format('Y-m-d') }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="px-6 py-12 text-center text-slate-400"
                        >
                            لا توجد استشارات حتى الآن
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</section>
