<x-app-layout>

    <div class="py-12" dir="rtl">

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="p-6 bg-white rounded shadow dark:bg-gray-800">

                <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">
                    مراجعة أعمال المهندسين
                </h2>

                @if (session('success'))

                    <div class="p-4 mb-4 text-green-800 bg-green-100 rounded">
                        {{ session('success') }}
                    </div>

                @endif

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[1100px] border-collapse border">

                        <thead>

                            <tr class="bg-gray-100 dark:bg-gray-700">

                                <th class="p-2 border">الصورة</th>
                                <th class="p-2 border">العنوان</th>
                                <th class="p-2 border">المهندس</th>
                                <th class="p-2 border">النوع</th>
                                <th class="p-2 border">الموقع</th>
                                <th class="p-2 border">الحالة</th>
                                <th class="p-2 border">الإجراءات</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($works as $work)

                                <tr>

                                    <td class="p-2 border">

                                        @if ($work->coverImage)

                                            <img
                                                src="{{ asset('storage/' . $work->coverImage->image_path) }}"
                                                alt="{{ $work->title }}"
                                                class="object-cover w-24 h-20 rounded"
                                            >

                                        @else

                                            لا توجد صورة

                                        @endif

                                    </td>

                                    <td class="p-2 border">
                                        {{ $work->title }}
                                    </td>

                                    <td class="p-2 border">
                                        {{ $work->engineer?->name ?? 'غير معروف' }}
                                    </td>

                                    <td class="p-2 border">
                                        {{ $work->project_type ?? 'غير محدد' }}
                                    </td>

                                    <td class="p-2 border">
                                        {{ $work->location ?? 'غير محدد' }}
                                    </td>

                                    <td class="p-2 border">

                                        @if ($work->status === 'pending')

                                            <span class="px-2 py-1 text-yellow-800 bg-yellow-100 rounded">
                                                قيد المراجعة
                                            </span>

                                        @elseif ($work->status === 'approved')

                                            <span class="px-2 py-1 text-green-800 bg-green-100 rounded">
                                                مقبول
                                            </span>

                                        @elseif ($work->status === 'rejected')

                                            <span class="px-2 py-1 text-red-800 bg-red-100 rounded">
                                                مرفوض
                                            </span>

                                        @endif

                                    </td>

                                    <td class="p-2 border">

                                        <div class="flex flex-wrap gap-2">

                                            <a
                                                href="{{ route('engineer.works.show', $work) }}"
                                                class="px-3 py-1 text-white bg-blue-600 rounded"
                                            >
                                                عرض
                                            </a>

                                            @if ($work->status !== 'approved')

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.engineer-works.approve', $work) }}"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="px-3 py-1 text-white bg-green-600 rounded"
                                                    >
                                                        موافقة
                                                    </button>

                                                </form>

                                            @endif

                                            @if ($work->status !== 'rejected')

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.engineer-works.reject', $work) }}"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="px-3 py-1 text-white bg-red-600 rounded"
                                                    >
                                                        رفض
                                                    </button>

                                                </form>

                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="p-4 text-center border"
                                    >
                                        لا توجد أعمال للمراجعة.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
