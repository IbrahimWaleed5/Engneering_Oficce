import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/dist/css/intlTelInput.css';
document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById('phone');

    if (!phoneInput) {
        return;
    }

    const countryCodeInput =
        document.getElementById('country_code');

    const dialCodeInput =
        document.getElementById('dial_code');

    const form = phoneInput.closest('form');

    const iti = intlTelInput(phoneInput, {
        initialCountry:
            countryCodeInput?.value?.toLowerCase() || 'ps',

        separateDialCode: true,

        countrySearch: true,

        nationalMode: true,

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
            'us',
        ],

        loadUtils: () =>
            import('intl-tel-input/utils'),
    });

    function syncPhoneData() {
        const country = iti.getSelectedCountryData();

        if (countryCodeInput) {
            countryCodeInput.value =
                country.iso2?.toUpperCase() || 'PS';
        }

        if (dialCodeInput) {
            dialCodeInput.value =
                country.dialCode
                    ? `+${country.dialCode}`
                    : '+970';
        }
    }

    phoneInput.addEventListener(
        'countrychange',
        syncPhoneData
    );

    syncPhoneData();

    form?.addEventListener('submit', async function (event) {
        event.preventDefault();

        try {
            await iti.promise;
        } catch (error) {
            console.error(error);
        }

        syncPhoneData();

        if (!iti.isValidNumber()) {
            phoneInput.setCustomValidity(
                'رقم الهاتف غير صحيح بالنسبة للدولة المختارة.'
            );

            phoneInput.reportValidity();
            phoneInput.focus();

            return;
        }

        phoneInput.setCustomValidity('');

        form.submit();
    });
});
