import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/styles';
import { ar } from 'intl-tel-input/locale';

document.addEventListener('DOMContentLoaded', () => {
    const phoneInput = document.getElementById('phone');

    if (!phoneInput || phoneInput.dataset.intlTelReady === 'true') {
        return;
    }

    phoneInput.dataset.intlTelReady = 'true';

    const form = phoneInput.closest('form');
    const countryCodeInput = document.getElementById('country_code');
    const dialCodeInput = document.getElementById('dial_code');
    const phoneError = document.getElementById('phone-client-error');

    const initialCountry =
        countryCodeInput?.value?.toLowerCase() || 'ps';

    const iti = intlTelInput(phoneInput, {
        initialCountry: initialCountry,

        countryOrder: [
            'ps',
            'sa',
            'jo',
            'ae',
            'qa',
            'kw',
            'bh',
            'om',
            'eg',
            'iq',
            'lb',
            'sy',
            'tr',
            'gb',
            'us',
        ],

        separateDialCode: true,
        countrySearch: true,
        countryNameLocale: 'ar',
        uiTranslations: ar,
        showFlags: true,
        nationalMode: true,
        strictMode: true,
        formatAsYouType: true,
        countrySelectorMode: 'AUTO',

        loadUtils: () => import('intl-tel-input/utils'),
    });

    function syncCountryData() {
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

    function hidePhoneError() {
        if (!phoneError) {
            return;
        }

        phoneError.textContent = '';
        phoneError.classList.add('hidden');

        phoneInput
            .closest('.iti-premium-wrapper')
            ?.classList.remove('phone-has-error');
    }

    function showPhoneError(message) {
        if (!phoneError) {
            return;
        }

        phoneError.textContent = message;
        phoneError.classList.remove('hidden');

        phoneInput
            .closest('.iti-premium-wrapper')
            ?.classList.add('phone-has-error');
    }

    phoneInput.addEventListener('countrychange', () => {
        syncCountryData();
        hidePhoneError();
    });

    phoneInput.addEventListener('input', hidePhoneError);

    syncCountryData();

    if (!form) {
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        hidePhoneError();
        syncCountryData();

        try {
            await iti.promise;
        } catch (error) {
            console.error(
                'تعذر تحميل أدوات التحقق من الهاتف:',
                error
            );
        }

        const numberValue = phoneInput.value.trim();

        if (!numberValue) {
            showPhoneError('يرجى إدخال رقم الهاتف.');
            phoneInput.focus();
            return;
        }

        if (!iti.isValidNumber()) {
            showPhoneError(
                'رقم الهاتف غير صحيح بالنسبة للدولة المختارة.'
            );

            phoneInput.focus();
            return;
        }

        /*
         * نرسل الرقم المحلي إلى Laravel.
         * RegisteredUserController سيحوّله إلى E.164.
         */
        form.submit();
    });
});
