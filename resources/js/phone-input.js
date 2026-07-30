import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/styles';


document.addEventListener('DOMContentLoaded', () => {
    const phoneInput = document.querySelector('#phone');

    if (!phoneInput) {
        return;
    }

    const countryCodeInput = document.querySelector('#country_code');
    const dialCodeInput = document.querySelector('#dial_code');
    const form = phoneInput.closest('form');

    const iti = intlTelInput(phoneInput, {
        initialCountry: (
            countryCodeInput?.value || 'PS'
        ).toLowerCase(),

        separateDialCode: true,
        nationalMode: true,
        countrySearch: true,
        showFlags: true,
        formatAsYouType: true,

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

        loadUtils: () => import(
            'intl-tel-input/utils'
        ),
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

    form?.addEventListener('submit', async (event) => {
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
