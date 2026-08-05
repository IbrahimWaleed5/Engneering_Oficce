<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black text-white">
                    الدعم الفني
                </h1>

                <p class="mt-2 text-sm leading-7 text-slate-400">
                    تواصل مع المساعد الآلي أو اطلب تحويل المحادثة إلى موظف دعم.
                    تستمر هنا نفس تذكرة الدعم المفتوحة في حسابك.
                </p>
            </div>

            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-black text-slate-200 transition hover:border-cyan-400/30 hover:bg-cyan-500/10 hover:text-white"
            >
                العودة إلى لوحة التحكم
            </a>
        </div>
    </x-slot>

    <div
        id="support-center-page"
        class="w-full max-w-6xl px-4 py-8 mx-auto sm:px-6 lg:px-8"
    >
        <div
            class="overflow-hidden border shadow-2xl rounded-3xl border-white/10 bg-slate-900/70 shadow-black/20"
        >
            <div
                class="p-5 border-b border-white/10 bg-gradient-to-l from-cyan-500/10 via-blue-600/10 to-violet-500/10 sm:p-6"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center justify-center w-12 h-12 text-2xl border shrink-0 rounded-2xl border-cyan-300/20 bg-cyan-500/10"
                    >
                        🎧
                    </div>

                    <div>
                        <h2 class="font-black text-white">
                            مركز مساعدة الوليد الهندسي
                        </h2>

                        <p class="mt-1 text-sm text-slate-400">
                            اكتب سؤالك، وسيتم تحميل محادثتك المفتوحة تلقائيًا.
                        </p>
                    </div>
                </div>
            </div>

            <div class="support-center-shell">
                <x-support-bot />
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /*
             * تحويل مكوّن البوت العائم إلى محادثة كاملة داخل الصفحة.
             */
            #support-center-page .support-center-shell {
                min-height: 620px;
            }

            #support-center-page .support-bot-widget {
                position: static !important;
                inset: auto !important;
                width: 100% !important;
                height: auto !important;
                min-height: 620px;
                z-index: auto !important;
            }

            #support-center-page #supportBotToggle {
                display: none !important;
            }

            #support-center-page #supportBotPanel {
                position: static !important;
                inset: auto !important;
                display: flex !important;
                width: 100% !important;
                max-width: none !important;
                height: 620px !important;
                max-height: none !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                background: rgb(15 23 42) !important;
            }

            #support-center-page .support-bot-header {
                background:
                    linear-gradient(
                        to left,
                        rgba(6, 182, 212, .18),
                        rgba(37, 99, 235, .15)
                    ) !important;
                border-bottom: 1px solid rgba(255, 255, 255, .08);
            }

            #support-center-page .support-bot-close {
                display: none !important;
            }

            #support-center-page .support-bot-messages {
                background: rgba(2, 6, 23, .55) !important;
            }

            #support-center-page .support-bot-actions,
            #support-center-page .support-bot-form {
                background: rgb(15 23 42) !important;
                border-color: rgba(255, 255, 255, .08) !important;
            }

            #support-center-page .support-bot-form textarea {
                color: white !important;
                border-color: rgba(255, 255, 255, .12) !important;
                background: rgb(2 6 23) !important;
            }

            #support-center-page .support-bot-form textarea::placeholder {
                color: rgb(148 163 184) !important;
            }

            @media (max-width: 640px) {
                #support-center-page {
                    padding-left: 0;
                    padding-right: 0;
                    padding-bottom: 0;
                }

                #support-center-page > div {
                    border-right: 0;
                    border-left: 0;
                    border-bottom: 0;
                    border-radius: 1.5rem 1.5rem 0 0;
                }

                #support-center-page .support-center-shell,
                #support-center-page .support-bot-widget {
                    min-height: calc(100dvh - 235px);
                }

                #support-center-page #supportBotPanel {
                    position: static !important;
                    height: calc(100dvh - 235px) !important;
                    min-height: 520px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const panel = document.getElementById('supportBotPanel');

                if (panel) {
                    panel.classList.add('is-open');
                    panel.setAttribute('aria-hidden', 'false');
                }

                /*
                 * مكوّن الدعم يستخدم نفس endpoint الخاص ببدء/استرجاع
                 * التذكرة المفتوحة، لذلك تظهر نفس المحادثة السابقة.
                 */
                window.setTimeout(function () {
                    if (typeof window.openSupportBot === 'function') {
                        window.openSupportBot();
                        return;
                    }

                    document
                        .getElementById('supportBotToggle')
                        ?.click();
                }, 250);
            });
        </script>
    @endpush
</x-app-layout>
