<x-app-layout>

    <div class="p-6">

        <h1 class="text-2xl mb-4">
            إضافة موظف
        </h1>

        <form method="POST" action="/employees">

            @csrf

            <input
                type="text"
                name="name"
                placeholder="الاسم"
                class="border p-2 w-full mb-3"
            >

            <input
                type="email"
                name="email"
                placeholder="البريد الإلكتروني"
                class="border p-2 w-full mb-3"
            >

            <input
                type="password"
                name="password"
                placeholder="كلمة المرور"
                class="border p-2 w-full mb-3"
            >

            <select
                name="role"
                class="border p-2 w-full mb-3"
            >
                <option value="engineer">مهندس</option>
                <option value="employee">موظف</option>
                <option value="admin">مدير</option>
            </select>

            <button
                type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded"
            >
                حفظ
            </button>

        </form>

    </div>

</x-app-layout>
