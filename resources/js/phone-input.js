import intlTelInput from 'intl-tel-input/intlTelInputWithUtils';
import 'intl-tel-input/styles';

function initializePhoneInput() {
    const phoneInput = document.getElementById('phone');

    if (
        !phoneInput ||
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

    const iti = intlTelInput(phoneInput, {
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
            'us',
        ],
    });

    function updateCountryData() {
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
        updateCountryData
    );

    phoneInput.addEventListener('input', () => {
        phoneInput.setCustomValidity('');
    });

    updateCountryData();

    form?.addEventListener('submit', (event) => {
        event.preventDefault();

        updateCountryData();

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

    window.phoneInputInstance = iti;
}

if (document.readyState === 'loading') {
    document.addEventListener(
        'DOMContentLoaded',
        initializePhoneInput
    );
} else {
    initializePhoneInput();
}
