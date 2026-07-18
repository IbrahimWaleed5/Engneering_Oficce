import './bootstrap';

import Alpine from 'alpinejs';

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
