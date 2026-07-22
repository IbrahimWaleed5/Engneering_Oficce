<x-app-layout>

    <div
        class="relative py-12"
        dir="rtl"
    >

        <div
            class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8"
        >

            <x-page-header
                title="معلومات الدفع"
                description="بيانات التحويل البنكي والمحفظة الإلكترونية"
                icon="💳"
            />

            <x-alerts />

            <div class="mt-8">

                <x-payment-information />

            </div>

        </div>

    </div>

</x-app-layout>
