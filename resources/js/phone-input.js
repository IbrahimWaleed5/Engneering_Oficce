import intlTelInput from 'intl-tel-input/intlTelInputWithUtils';

function initializePhoneInput() {
    const phoneInput = document.querySelector(
        '#phone[type="tel"]'
    );

    if (
        !phoneInput ||
        phoneInput.dataset.itiInitialized === 'true'
    ) {
        return;
    }

    const countryCodeInput =
        document.getElementById('country_code');

    const dialCodeInput =
        document.getElementById('dial_code');

    const form = phoneInput.closest('form');

    const fieldWrapper = phoneInput.closest(
        '.premium-phone-field'
    );

    const initialCountry = (
        countryCodeInput?.value || 'PS'
    ).toLowerCase();

    try {
        const iti = intlTelInput(phoneInput, {
            initialCountry: initialCountry,

            separateDialCode: true,
            countrySelectorMode: 'DROPDOWN',
            dropdownParent: document.body,
            countrySearch: true,
            showFlags: true,

            nationalMode: true,
            formatAsYouType: true,
            strictMode: true,

            countryNameLocale: 'ar',

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

        phoneInput.dataset.itiInitialized = 'true';

        function clearPhoneError() {
            phoneInput.setCustomValidity('');

            fieldWrapper?.classList.remove(
                'phone-has-error'
            );
        }

        function showPhoneError(message) {
            phoneInput.setCustomValidity(message);

            fieldWrapper?.classList.add(
                'phone-has-error'
            );

            phoneInput.reportValidity();
            phoneInput.focus();
        }

        function updateCountryData() {
            const country = iti.getSelectedCountryData();

            if (countryCodeInput) {
                countryCodeInput.value =
                    country?.iso2?.toUpperCase() || 'PS';
            }

            if (dialCodeInput) {
                dialCodeInput.value = country?.dialCode
                    ? `+${country.dialCode}`
                    : '+970';
            }
        }

        updateCountryData();

        phoneInput.addEventListener(
            'countrychange',
            () => {
                updateCountryData();
                clearPhoneError();
            }
        );

        phoneInput.addEventListener(
            'input',
            clearPhoneError
        );

        form?.addEventListener('submit', (event) => {
            clearPhoneError();
            updateCountryData();

            if (!phoneInput.value.trim()) {
                event.preventDefault();

                showPhoneError(
                    'يرجى إدخال رقم الهاتف.'
                );

                return;
            }

            if (!iti.isValidNumber()) {
                event.preventDefault();

                showPhoneError(
                    'رقم الهاتف غير صحيح للدولة المختارة.'
                );

                return;
            }

            const fullNumber = iti.getNumber();

            if (fullNumber) {
                phoneInput.value = fullNumber;
            }
        });

        window.phoneInputInstance = iti;
    } catch (error) {
        delete phoneInput.dataset.itiInitialized;

        console.error(
            'تعذر تشغيل قائمة الدول لحقل الهاتف:',
            error
        );
    }
}

if (document.readyState === 'complete') {
    initializePhoneInput();
} else {
    window.addEventListener(
        'load',
        initializePhoneInput,
        { once: true }
    );
}

window.addEventListener(
    'pageshow',
    initializePhoneInput
);
