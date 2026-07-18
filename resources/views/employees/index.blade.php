<x-app-layout>

    <div class="p-6">

        <a
            href="/employees/create"
            class="bg-green-600 text-white px-4 py-2 rounded"
        >
            إضافة موظف
        </a>

        <table class="w-full mt-5 border">

            <tr>
                <th class="border p-2">الاسم</th>
                <th class="border p-2">البريد</th>
                <th class="border p-2">الصلاحية</th>
            </tr>

            @foreach($employees as $employee)

                <tr>

                    <td class="border p-2">
                        {{ $employee->name }}
                    </td>

                    <td class="border p-2">
                        {{ $employee->email }}
                    </td>

                    <td class="border p-2">
                        {{ $employee->role }}
                    </td>

                </tr>

            @endforeach

        </table>

    </div>

</x-app-layout>
