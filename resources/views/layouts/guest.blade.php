<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="rtl"
>
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        {{ config('app.name', 'مكتب الوليد الهندسي') }}
    </title>

    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
    >

    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
    <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/intl-tel-input@26.5.0/dist/css/intlTelInput.css"
>
</head>

<body class="font-sans antialiased text-white bg-slate-950">

    <main
        class="flex items-center justify-center w-full min-h-screen overflow-x-hidden"
    >
        {{ $slot }}
    </main>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@26.5.0/dist/js/intlTelInput.min.js"></script>

<script>
    window.addEventListener('load', function () {
        const phoneInput = document.getElementById('phone');

        if (
            !phoneInput ||
            typeof window.intlTelInput !== 'function' ||
            phoneInput.dataset.itiInitialized === 'true'
        ) {
            return;
        }

        phoneInput.dataset.itiInitialized = 'true';

        const countryCodeInput =
            document.getElementById('country_code');

        const dialCodeInput =
            document.getElementById('dial_code');

        const form = phoneInput.closest('form');

        const iti = window.intlTelInput(phoneInput, {
            initialCountry: (
                countryCodeInput?.value || 'PS'
            ).toLowerCase(),

            separateDialCode: true,
            nationalMode: true,
            countrySearch: true,
            showFlags: true,
            formatAsYouType: true,
            strictMode: true,

            countryOrder: [
                'ps',
                'sa',
                'ae',
                'jo',
                'eg',
                'qa',
                'kw',
                'bh',
                'om',
                'iq',
                'lb',
                'sy',
                'tr',
                'gb',
                'us'
            ],

            loadUtils: () => import(
                'https://cdn.jsdelivr.net/npm/intl-tel-input@26.5.0/dist/js/utils.js'
            )
        });

        function syncPhoneData() {
            const country = iti.getSelectedCountryData();

            if (countryCodeInput) {
                countryCodeInput.value =
                    country.iso2?.toUpperCase() || 'PS';
            }

            if (dialCodeInput) {
                dialCodeInput.value = country.dialCode
                    ? `+${country.dialCode}`
                    : '+970';
            }
        }

        phoneInput.addEventListener(
            'countrychange',
            syncPhoneData
        );

        phoneInput.addEventListener('input', function () {
            phoneInput.setCustomValidity('');
        });

        syncPhoneData();

        form?.addEventListener('submit', async function (event) {
            event.preventDefault();

            try {
                await iti.promise;
            } catch (error) {
                console.error(error);
            }

            syncPhoneData();

            if (!phoneInput.value.trim()) {
                phoneInput.setCustomValidity(
                    'يرجى إدخال رقم الهاتف.'
                );

                phoneInput.reportValidity();
                phoneInput.focus();

                return;
            }

            if (!iti.isValidNumber()) {
                phoneInput.setCustomValidity(
                    'رقم الهاتف غير صحيح للدولة المختارة.'
                );

                phoneInput.reportValidity();
                phoneInput.focus();

                return;
            }

            phoneInput.setCustomValidity('');

            const fullNumber = iti.getNumber();

            if (fullNumber) {
                phoneInput.value = fullNumber;
            }

            form.submit();
        });
    });
</script>
</body>
</html>
