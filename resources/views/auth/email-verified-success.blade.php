<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>تم تأكيد البريد الإلكتروني</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-white bg-slate-950">
    <main
        class="flex items-center justify-center min-h-screen p-5"
    >
        <section
            class="w-full max-w-lg p-8 text-center border shadow-2xl rounded-3xl border-green-500/20 bg-slate-900/90"
        >
            <div
                class="flex items-center justify-center w-20 h-20 mx-auto text-4xl border rounded-full border-green-400/30 bg-green-500/10"
            >
                ✓
            </div>

            <h1 class="mt-6 text-3xl font-black">
                تم تأكيد البريد بنجاح
            </h1>

            @if ($email)
                <p class="mt-4 text-green-300" dir="ltr">
                    {{ $email }}
                </p>
            @endif

            <p class="mt-4 leading-8 text-slate-300">
                يمكنك إغلاق هذه الصفحة والعودة إلى الجهاز
                الذي أنشأت الحساب منه.
            </p>

            <a
                href="{{ route('login') }}"
                class="inline-flex items-center justify-center w-full px-6 py-4 font-black mt-7 rounded-2xl bg-gradient-to-l from-cyan-500 to-blue-600"
            >
                الذهاب إلى تسجيل الدخول
            </a>
        </section>
    </main>
</body>
</html>
