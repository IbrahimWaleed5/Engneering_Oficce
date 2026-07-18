<x-app-layout>

    <div class="py-12" dir="rtl">

        <div class="max-w-3xl p-6 mx-auto bg-white rounded shadow">

            <h2 class="mb-6 text-2xl font-bold">
                إضافة عمل أو إنجاز
            </h2>

            @if ($errors->any())
                <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
           <form method="POST"
      action="{{ route('engineer.works.store') }}"
      enctype="multipart/form-data">

    @csrf
                <div class="mb-4">
                    <label class="block mb-2">عنوان العمل</label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        class="w-full p-2 border rounded"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="block mb-2">نوع المشروع</label>

                    <input
                        type="text"
                        name="project_type"
                        value="{{ old('project_type') }}"
                        placeholder="تصميم معماري، إنشائي، كهربائي..."
                        class="w-full p-2 border rounded"
                    >
                </div>

                <div class="mb-4">
                    <label class="block mb-2">الموقع</label>

                    <input
                        type="text"
                        name="location"
                        value="{{ old('location') }}"
                        class="w-full p-2 border rounded"
                    >
                </div>

                <div class="mb-4">
                    <label class="block mb-2">سنة الإنجاز</label>

                    <input
                        type="number"
                        name="completion_year"
                        value="{{ old('completion_year') }}"
                        min="1900"
                        max="{{ date('Y') }}"
                        class="w-full p-2 border rounded"
                    >
                </div>

                <div class="mb-4">
                    <label class="block mb-2">وصف العمل</label>

                    <textarea
                        name="description"
                        rows="5"
                        class="w-full p-2 border rounded"
                    >{{ old('description') }}</textarea>
                </div>

                <div class="mb-6">
                    <label class="block mb-2">
                        صور العمل
                    </label>

                    <input
                        type="file"
                        name="images[]"
                        accept=".jpg,.jpeg,.png,.webp"
                        multiple
                        required
                        class="w-full p-2 border rounded"
                    >

                    <p class="mt-2 text-sm text-gray-500">
                        يمكنك اختيار من صورة واحدة حتى 10 صور.
                    </p>
                </div>

                <button
                    type="submit"
                    class="px-5 py-2 text-black bg-blue-600 rounded"
                >
                    إرسال العمل للمراجعة
                </button>

            </form>

        </div>

    </div>

</x-app-layout>
