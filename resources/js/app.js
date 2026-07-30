import './bootstrap';

import Alpine from 'alpinejs';

import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/styles';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll(
        '.fade-up, .fade-in'
    );

    const revealObserver = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.12,
        }
    );

    animatedElements.forEach((element) => {
        revealObserver.observe(element);
    });

    const counters = document.querySelectorAll('[data-counter]');

    const counterObserver = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const element = entry.target;
                const target = Number(element.dataset.counter || 0);
                const duration = 1200;
                const startTime = performance.now();

                const updateCounter = (currentTime) => {
                    const progress = Math.min(
                        (currentTime - startTime) / duration,
                        1
                    );

                    const easedProgress =
                        1 - Math.pow(1 - progress, 3);

                    element.textContent = Math.floor(
                        target * easedProgress
                    );

                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    } else {
                        element.textContent = target;
                    }
                };

                requestAnimationFrame(updateCounter);
                observer.unobserve(element);
            });
        },
        {
            threshold: 0.5,
        }
    );

    counters.forEach((counter) => {
        counterObserver.observe(counter);
    });
});
function initializeInternationalPhoneInput() {
    const phoneInput = document.getElementById('phone');

    if (!phoneInput || phoneInput.dataset.itiInitialized === 'true') {
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

        loadUtils: () =>
            import('intl-tel-input/utils'),
    });

    function synchronizePhoneData() {
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
        synchronizePhoneData
    );

    phoneInput.addEventListener('input', () => {
        phoneInput.setCustomValidity('');
    });

    synchronizePhoneData();

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        try {
            await iti.promise;
        } catch (error) {
            console.error(
                'Failed to load phone utilities:',
                error
            );
        }

        synchronizePhoneData();

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
}

document.addEventListener(
    'DOMContentLoaded',
    initializeInternationalPhoneInput
);

document.addEventListener(
    'livewire:navigated',
    initializeInternationalPhoneInput
);
