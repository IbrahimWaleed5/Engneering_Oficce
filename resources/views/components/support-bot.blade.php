@auth
<div id="supportBotWidget" class="support-bot-widget">
    <button
        type="button"
        id="supportBotToggle"
        class="support-bot-toggle"
        aria-label="فتح الدعم الفني"
    >
        <span class="support-bot-toggle-icon">💬</span>

        <span
            id="supportBotUnread"
            class="support-bot-unread d-none"
        >
            1
        </span>
    </button>

    <section
        id="supportBotPanel"
        class="support-bot-panel"
        aria-hidden="true"
    >
        <header class="support-bot-header">
            <div>
                <h3>مساعد الوليد الهندسي</h3>
                <p id="supportBotStatus">
                    متصل وجاهز للمساعدة
                </p>
            </div>

            <button
                type="button"
                id="supportBotClose"
                class="support-bot-close"
                aria-label="إغلاق"
            >
                ×
            </button>
        </header>

        <div
            id="supportBotMessages"
            class="support-bot-messages"
        >
            <div class="support-bot-loading">
                جاري تحميل المحادثة...
            </div>
        </div>

        <div
            id="supportBotActions"
            class="support-bot-actions"
        ></div>

        <form
            id="supportBotForm"
            class="support-bot-form"
        >
            @csrf

            <textarea
                id="supportBotInput"
                name="message"
                rows="1"
                maxlength="5000"
                placeholder="اكتب سؤالك هنا..."
                required
            ></textarea>

            <button
                type="submit"
                id="supportBotSend"
            >
                إرسال
            </button>
        </form>
    </section>
</div>

@push('styles')
<style>
    :root {
        --support-bg: #0b1326;
        --support-surface: #171f33;
        --support-surface-low: #131b2e;
        --support-surface-high: #222a3d;
        --support-surface-highest: #2d3449;
        --support-bright: #31394d;
        --support-outline: #424754;
        --support-primary: #adc6ff;
        --support-primary-container: #4d8eff;
        --support-secondary: #4edea3;
        --support-secondary-container: #00a572;
        --support-text: #dae2fd;
        --support-muted: #c2c6d6;
        --support-warning: #ffb95f;
    }

    .support-bot-widget {
        position: fixed;
        left: 24px;
        bottom: 24px;
        z-index: 9999;
        direction: rtl;
        font-family: inherit;
    }

    .support-bot-toggle {
        width: 58px;
        height: 58px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(173, 198, 255, .22);
        border-radius: 16px;
        background:
            linear-gradient(145deg, rgba(77, 142, 255, .95), rgba(24, 74, 155, .95));
        color: #fff;
        cursor: pointer;
        box-shadow:
            0 18px 45px rgba(0, 0, 0, .35),
            inset 0 1px rgba(255, 255, 255, .15);
        position: relative;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .support-bot-toggle:hover {
        transform: translateY(-2px);
        box-shadow:
            0 22px 55px rgba(0, 0, 0, .42),
            0 0 24px rgba(77, 142, 255, .25);
    }

    .support-bot-toggle-icon {
        font-size: 25px;
    }

    .support-bot-unread {
        position: absolute;
        top: -5px;
        right: -5px;
        min-width: 22px;
        height: 22px;
        padding: 0 5px;
        border-radius: 999px;
        background: #ef4444;
        color: white;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--support-bg);
        font-weight: 800;
    }

    .d-none {
        display: none !important;
    }

    .support-bot-panel {
        position: absolute;
        left: 0;
        bottom: 72px;
        width: 420px;
        max-width: calc(100vw - 32px);
        height: 650px;
        max-height: calc(100vh - 112px);
        display: none;
        flex-direction: column;
        overflow: hidden;
        border-radius: 18px;
        background: var(--support-surface);
        color: var(--support-text);
        box-shadow: 0 28px 90px rgba(0, 0, 0, .55);
        border: 1px solid var(--support-outline);
    }

    .support-bot-panel.is-open {
        display: flex;
    }

    .support-bot-header {
        min-height: 80px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        color: var(--support-text);
        background:
            linear-gradient(135deg, rgba(45, 52, 73, .98), rgba(23, 31, 51, .98));
        border-bottom: 1px solid var(--support-outline);
    }

    .support-bot-header > div::before {
        content: "🎧";
        float: right;
        width: 46px;
        height: 46px;
        margin-left: 12px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: var(--support-primary-container);
        border: 1px solid rgba(173, 198, 255, .2);
        box-shadow: inset 0 1px rgba(255,255,255,.14);
        font-size: 21px;
    }

    .support-bot-header h3 {
        margin: 1px 0 5px;
        font-size: 17px;
        font-weight: 800;
        color: var(--support-text);
    }

    .support-bot-header p {
        margin: 0;
        font-size: 12px;
        color: var(--support-muted);
    }

    .support-bot-close {
        width: 38px;
        height: 38px;
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        border: 1px solid var(--support-outline);
        border-radius: 10px;
        background: rgba(255,255,255,.035);
        color: var(--support-muted);
        font-size: 25px;
        cursor: pointer;
        line-height: 1;
        transition: .2s ease;
    }

    .support-bot-close:hover {
        color: white;
        border-color: rgba(173,198,255,.45);
        background: rgba(173,198,255,.08);
    }

    .support-bot-messages {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
        background:
            radial-gradient(circle at top, rgba(77,142,255,.08), transparent 36%),
            var(--support-bg);
        scrollbar-width: thin;
        scrollbar-color: #424754 transparent;
    }

    .support-bot-loading {
        text-align: center;
        color: var(--support-muted);
        padding: 28px 10px;
    }

    .support-message-row {
        display: flex;
        margin-bottom: 16px;
    }

    .support-message-row.customer {
        justify-content: flex-start;
    }

    .support-message-row.bot,
    .support-message-row.employee,
    .support-message-row.admin {
        justify-content: flex-end;
    }

    .support-message-row.system {
        justify-content: center;
    }

    .support-message {
        max-width: 84%;
        padding: 13px 15px;
        border-radius: 16px;
        line-height: 1.8;
        font-size: 14px;
        word-break: break-word;
        white-space: pre-wrap;
        box-shadow: 0 7px 18px rgba(0,0,0,.12);
    }

    .support-message-row.customer .support-message {
        color: #eafff6;
        background: var(--support-secondary-container);
        border: 1px solid rgba(78,222,163,.22);
        border-top-left-radius: 4px;
    }

    .support-message-row.bot .support-message {
        color: var(--support-text);
        background: var(--support-bright);
        border: 1px solid var(--support-outline);
        border-top-right-radius: 4px;
    }

    .support-message-row.employee .support-message,
    .support-message-row.admin .support-message {
        color: var(--support-text);
        background: linear-gradient(135deg, #243754, #1e304b);
        border: 1px solid rgba(173,198,255,.22);
        border-top-right-radius: 4px;
    }

    .support-message-row.system .support-message {
        max-width: 92%;
        text-align: center;
        color: #ffddb8;
        background: rgba(202,129,0,.18);
        border: 1px solid rgba(255,185,95,.3);
        border-radius: 999px;
        padding: 9px 18px;
        font-size: 12px;
        box-shadow: none;
    }

    .support-message-meta {
        display: block;
        margin-top: 6px;
        font-size: 10px;
        color: inherit;
        opacity: .68;
    }

    .support-bot-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        padding: 12px 14px;
        background: var(--support-surface-highest);
        border-top: 1px solid var(--support-outline);
    }

    .support-bot-actions:empty {
        display: none;
    }

    .support-bot-action {
        min-height: 40px;
        border: 1px solid var(--support-outline);
        background: var(--support-surface);
        color: var(--support-primary);
        border-radius: 10px;
        padding: 9px 14px;
        cursor: pointer;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: inherit;
        font-weight: 800;
        transition: .2s ease;
    }

    .support-bot-action:hover {
        border-color: rgba(173,198,255,.5);
        background: var(--support-surface-high);
        transform: translateY(-1px);
    }

    .support-bot-action.primary {
        color: #003824;
        border-color: rgba(78,222,163,.3);
        background: var(--support-secondary);
    }

    .support-bot-action.danger {
        color: #ffb4ab;
        border-color: rgba(255,180,171,.4);
    }

    .support-bot-form {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        padding: 14px;
        background: var(--support-surface-highest);
        border-top: 1px solid var(--support-outline);
    }

    .support-bot-form textarea {
        flex: 1;
        min-height: 46px;
        max-height: 120px;
        resize: none;
        border: 1px solid var(--support-outline);
        border-radius: 11px;
        padding: 11px 13px;
        outline: none;
        font-family: inherit;
        color: var(--support-text);
        background: var(--support-bg);
        box-shadow: inset 0 2px 8px rgba(0,0,0,.15);
        transition: .2s ease;
    }

    .support-bot-form textarea::placeholder {
        color: #8c909f;
    }

    .support-bot-form textarea:focus {
        border-color: var(--support-primary);
        box-shadow: 0 0 0 2px rgba(173,198,255,.12);
    }

    .support-bot-form button {
        height: 46px;
        border: 1px solid rgba(78,222,163,.22);
        border-radius: 11px;
        padding: 0 21px;
        background: var(--support-secondary-container);
        color: #eafff6;
        cursor: pointer;
        font-weight: 900;
        transition: .2s ease;
    }

    .support-bot-form button:hover {
        background: var(--support-secondary);
        color: #003824;
    }

    .support-bot-form button:disabled,
    .support-bot-action:disabled {
        opacity: .6;
        cursor: not-allowed;
        transform: none;
    }

    /*
     * عندما يُعرض المكوّن داخل صفحة الدعم المستقلة.
     */
    #support-center-page .support-bot-widget {
        position: static !important;
        inset: auto !important;
        width: 100% !important;
        height: 100% !important;
        z-index: auto !important;
    }

    #support-center-page .support-bot-toggle {
        display: none !important;
    }

    #support-center-page .support-bot-panel {
        position: static !important;
        inset: auto !important;
        display: flex !important;
        width: 100% !important;
        max-width: none !important;
        height: 100% !important;
        max-height: none !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    #support-center-page .support-bot-close {
        display: none !important;
    }

    @media (max-width: 600px) {
        .support-bot-widget {
            left: 12px;
            right: 12px;
            bottom: 12px;
        }

        .support-bot-toggle {
            position: absolute;
            left: 0;
            bottom: 0;
        }

        .support-bot-panel {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100dvh;
            max-width: none;
            max-height: none;
            border-radius: 0;
        }

        #support-center-page .support-bot-panel {
            position: static !important;
            height: 100% !important;
        }

        .support-bot-messages {
            padding: 16px;
        }

        .support-message {
            max-width: 90%;
        }

        .support-bot-form {
            padding-bottom:
                max(14px, env(safe-area-inset-bottom));
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const widget = document.getElementById('supportBotWidget');

    if (!widget) {
        return;
    }

    const toggleButton = document.getElementById('supportBotToggle');
    const closeButton = document.getElementById('supportBotClose');
    const panel = document.getElementById('supportBotPanel');
    const messagesContainer = document.getElementById('supportBotMessages');
    const actionsContainer = document.getElementById('supportBotActions');
    const form = document.getElementById('supportBotForm');
    const input = document.getElementById('supportBotInput');
    const sendButton = document.getElementById('supportBotSend');
    const statusText = document.getElementById('supportBotStatus');
    const unreadBadge = document.getElementById('supportBotUnread');

    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.getAttribute('content');

    let ticketId = null;
    let ticketMode = 'bot';
    let initialized = false;
    let requestRunning = false;
    let lastMessageId = 0;
    let pollingTimer = null;
    let pollingRunning = false;

    const routes = {
        start: @json(route('support-bot.start')),
        send: @json(route('support-bot.send')),

        officialConversationTemplate: @json(
            \Illuminate\Support\Facades\Route::has('support.show')
                ? route(
                    'support.show',
                    ['supportTicket' => '__TICKET__']
                )
                : null
        ),

        messagesTemplate: @json(
            route(
                'support-bot.messages',
                ['ticket' => '__TICKET__']
            )
        ),
        resolveTemplate: @json(
            route(
                'support-bot.resolve',
                ['ticket' => '__TICKET__']
            )
        ),
        transferTemplate: @json(
            route(
                'support-bot.transfer',
                ['ticket' => '__TICKET__']
            )
        ),
    };

    function routeFor(template, id) {
        return template.replace('__TICKET__', id);
    }

    async function apiRequest(url, options = {}) {
        const method = options.method ?? 'POST';

        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        };

        if (method !== 'GET') {
            headers['Content-Type'] = 'application/json';
        }

        const response = await fetch(url, {
            method: method,
            headers: headers,
            body:
                method !== 'GET' && options.body
                    ? JSON.stringify(options.body)
                    : undefined,
        });

        let data = {};

        try {
            data = await response.json();
        } catch (error) {
            data = {
                message: 'وصل رد غير صالح من الخادم.',
            };
        }

        if (!response.ok) {
            if (response.status === 419) {
                throw new Error(
                    'انتهت جلسة الدخول. حدّث الصفحة وحاول مرة أخرى.'
                );
            }

            if (
                response.status === 422 &&
                data.errors
            ) {
                const firstError = Object
                    .values(data.errors)
                    .flat()[0];

                throw new Error(
                    firstError ?? data.message
                );
            }

            throw new Error(
                data.message ??
                'حدث خطأ أثناء تنفيذ الطلب.'
            );
        }

        return data;
    }

    function openPanel() {
        panel.classList.add('is-open');
        panel.setAttribute('aria-hidden', 'false');
        unreadBadge.classList.add('d-none');

        if (!initialized) {
            startConversation();
        } else {
            input.focus();
        }
    }

    function closePanel() {
        panel.classList.remove('is-open');
        panel.setAttribute('aria-hidden', 'true');
    }

    window.openSupportBot = openPanel;
    window.closeSupportBot = closePanel;

    window.addEventListener(
        'open-support-bot',
        openPanel
    );

    function clearMessages() {
        messagesContainer.innerHTML = '';
    }

    function addMessage(message) {
        if (!message || !message.message) {
            return;
        }

        const senderType = message.sender_type ?? 'system';

        const row = document.createElement('div');
        row.className = `support-message-row ${senderType}`;

        const bubble = document.createElement('div');
        bubble.className = 'support-message';
        bubble.textContent = message.message;

        if (message.created_at) {
            const meta = document.createElement('span');
            meta.className = 'support-message-meta';

            const date = new Date(message.created_at);

            meta.textContent = date.toLocaleTimeString(
                'ar',
                {
                    hour: '2-digit',
                    minute: '2-digit',
                }
            );

            bubble.appendChild(meta);
        }

        row.appendChild(bubble);
        messagesContainer.appendChild(row);

        scrollToBottom();

        if (
            !panel.classList.contains('is-open') &&
            senderType !== 'customer'
        ) {
            unreadBadge.classList.remove('d-none');
        }
    }

    function showError(message) {
        addMessage({
            sender_type: 'system',
            message: message,
        });
    }

    function scrollToBottom() {
        messagesContainer.scrollTop =
            messagesContainer.scrollHeight;
    }

    function clearActions() {
        actionsContainer.innerHTML = '';
    }

    function createAction(
        label,
        callback,
        type = ''
    ) {
        const button = document.createElement('button');

        button.type = 'button';
        button.className =
            `support-bot-action ${type}`.trim();
        button.textContent = label;

        button.addEventListener('click', async function () {
            button.disabled = true;

            try {
                await callback();
            } finally {
                button.disabled = false;
            }
        });

        actionsContainer.appendChild(button);

        return button;
    }

    function createLinkAction(
        label,
        url,
        type = ''
    ) {
        const link = document.createElement('a');

        link.href = url;
        link.className =
            `support-bot-action ${type}`.trim();
        link.textContent = label;

        actionsContainer.appendChild(link);

        return link;
    }

    function showEmployeeConversationLink() {
        if (
            !ticketId ||
            !routes.officialConversationTemplate
        ) {
            return;
        }

        clearActions();

        createLinkAction(
            'فتح المحادثة الرسمية مع موظف الدعم',
            routeFor(
                routes.officialConversationTemplate,
                ticketId
            ),
            'primary'
        );
    }

    function showBotFeedbackActions() {
        clearActions();

        createAction(
            'نعم، تم حل المشكلة',
            resolveByBot,
            'primary'
        );

        createAction(
            'لا، تواصل مع موظف',
            transferToEmployee
        );
    }

    function showTransferAction() {
        clearActions();

        createAction(
            'تحويل إلى موظف الدعم',
            transferToEmployee,
            'primary'
        );
    }

    function updateStatus() {
        if (ticketMode === 'bot') {
            statusText.textContent =
                'يرد عليك المساعد الآلي';
            return;
        }

        if (ticketMode === 'waiting_employee') {
            statusText.textContent =
                'بانتظار موظف الدعم';

            showEmployeeConversationLink();
            return;
        }

        if (ticketMode === 'employee') {
            statusText.textContent =
                'المحادثة مع موظف الدعم';

            showEmployeeConversationLink();
            return;
        }

        statusText.textContent =
            'الدعم الفني';
    }

    async function startConversation() {
        if (requestRunning) {
            return;
        }

        requestRunning = true;
        clearMessages();
        clearActions();

        messagesContainer.innerHTML = `
            <div class="support-bot-loading">
                جاري فتح المحادثة...
            </div>
        `;

        try {
            const data = await apiRequest(
                routes.start
            );

            ticketId = data.ticket.id;
            ticketMode = data.ticket.support_mode;
            initialized = true;
            lastMessageId = Number(
                data.last_message_id ?? 0
            );

            startPolling();

            clearMessages();

            if (
                Array.isArray(data.messages) &&
                data.messages.length
            ) {
                data.messages.forEach(addMessage);
            } else {
                addMessage({
                    sender_type: 'bot',
                    message:
                        'مرحبًا بك في دعم الوليد الهندسي. كيف يمكنني مساعدتك؟',
                });
            }

            updateStatus();
            input.focus();
        } catch (error) {
            clearMessages();
            showError(error.message);
        } finally {
            requestRunning = false;
        }
    }

    async function sendMessage(event) {
        event.preventDefault();

        const message = input.value.trim();

        if (
            !message ||
            !ticketId ||
            requestRunning
        ) {
            return;
        }

        clearActions();

        addMessage({
            sender_type: 'customer',
            message: message,
            created_at: new Date().toISOString(),
        });

        input.value = '';
        resizeInput();

        requestRunning = true;
        sendButton.disabled = true;
        input.disabled = true;

        try {
            const data = await apiRequest(
                routes.send,
                {
                    body: {
                        ticket_id: ticketId,
                        message: message,
                    },
                }
            );

            if (data.message) {
                addMessage(data.message);
            }

            if (data.ticket?.support_mode) {
                ticketMode = data.ticket.support_mode;
            } else if (data.handled_by === 'employee') {
                ticketMode = 'employee';
            }

            if (data.notice) {
                addMessage({
                    sender_type: 'system',
                    message: data.notice,
                });
            }

            if (data.show_feedback_buttons) {
                showBotFeedbackActions();
            }

            if (data.show_transfer_button) {
                showTransferAction();
            }

            updateStatus();
        } catch (error) {
            showError(error.message);
        } finally {
            requestRunning = false;
            sendButton.disabled = false;
            input.disabled = false;
            input.focus();
        }
    }

    async function resolveByBot() {
        if (!ticketId) {
            return;
        }

        try {
            const data = await apiRequest(
                routeFor(
                    routes.resolveTemplate,
                    ticketId
                )
            );

            clearActions();

            addMessage({
                sender_type: 'system',
                message: data.message,
            });

            input.disabled = true;
            sendButton.disabled = true;
            statusText.textContent =
                'تم حل المشكلة';

            stopPolling();
        } catch (error) {
            showError(error.message);
        }
    }

    async function transferToEmployee() {
        if (!ticketId) {
            return;
        }

        try {
            const data = await apiRequest(
                routeFor(
                    routes.transferTemplate,
                    ticketId
                )
            );

            clearActions();

            ticketMode =
                data.ticket?.support_mode ??
                (
                    data.assigned
                        ? 'employee'
                        : 'waiting_employee'
                );

            addMessage({
                sender_type: 'system',
                message: data.message,
            });

            updateStatus();
        } catch (error) {
            showError(error.message);
        }
    }

    function startPolling() {
        stopPolling();

        pollingTimer = window.setInterval(
            fetchNewMessages,
            4000
        );
    }

    function stopPolling() {
        if (pollingTimer) {
            window.clearInterval(pollingTimer);
            pollingTimer = null;
        }
    }

    async function fetchNewMessages() {
        if (
            !ticketId ||
            pollingRunning ||
            document.hidden
        ) {
            return;
        }

        pollingRunning = true;

        try {
            const baseUrl = routeFor(
                routes.messagesTemplate,
                ticketId
            );

            const separator = baseUrl.includes('?')
                ? '&'
                : '?';

            const data = await apiRequest(
                `${baseUrl}${separator}after_id=${lastMessageId}`,
                {
                    method: 'GET',
                }
            );

            if (data.ticket?.support_mode) {
                ticketMode = data.ticket.support_mode;
                updateStatus();
            }

            if (Array.isArray(data.messages)) {
                data.messages.forEach(function (message) {
                    if (message.sender_type !== 'customer') {
                        addMessage(message);
                    }

                    lastMessageId = Math.max(
                        lastMessageId,
                        Number(message.id ?? 0)
                    );
                });
            }

            lastMessageId = Math.max(
                lastMessageId,
                Number(data.last_message_id ?? 0)
            );

            if (data.conversation_closed) {
                clearActions();
                input.disabled = true;
                sendButton.disabled = true;
                statusText.textContent =
                    'تم إغلاق المحادثة';
                stopPolling();
            }
        } catch (error) {
            console.error(
                'Support polling error:',
                error
            );
        } finally {
            pollingRunning = false;
        }
    }

    function resizeInput() {
        input.style.height = 'auto';

        input.style.height = Math.min(
            input.scrollHeight,
            120
        ) + 'px';
    }

    toggleButton.addEventListener(
        'click',
        openPanel
    );

    closeButton.addEventListener(
        'click',
        closePanel
    );

    form.addEventListener(
        'submit',
        sendMessage
    );

    input.addEventListener(
        'input',
        resizeInput
    );

    input.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key === 'Enter' &&
                !event.shiftKey
            ) {
                event.preventDefault();
                form.requestSubmit();
            }
        }
    );

    window.addEventListener(
        'beforeunload',
        stopPolling
    );

    document.addEventListener(
        'visibilitychange',
        function () {
            if (
                !document.hidden &&
                initialized &&
                ticketId
            ) {
                fetchNewMessages();
            }
        }
    );
});
</script>
@endpush
@endauth
