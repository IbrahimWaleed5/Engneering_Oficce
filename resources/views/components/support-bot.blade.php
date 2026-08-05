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
                <h3>مساعد إبداع هوم</h3>
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
        border: 0;
        border-radius: 50%;
        background: #1f5f56;
        color: #fff;
        cursor: pointer;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .22);
        position: relative;
        transition: transform .2s ease;
    }

    .support-bot-toggle:hover {
        transform: scale(1.05);
    }

    .support-bot-toggle-icon {
        font-size: 25px;
    }

    .support-bot-unread {
        position: absolute;
        top: -3px;
        right: -3px;
        width: 21px;
        height: 21px;
        border-radius: 50%;
        background: #dc3545;
        color: white;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
    }

    .d-none {
        display: none !important;
    }

    .support-bot-panel {
        position: absolute;
        left: 0;
        bottom: 72px;
        width: 380px;
        max-width: calc(100vw - 32px);
        height: 560px;
        max-height: calc(100vh - 120px);
        display: none;
        flex-direction: column;
        overflow: hidden;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 18px 50px rgba(0, 0, 0, .25);
        border: 1px solid #e7e7e7;
    }

    .support-bot-panel.is-open {
        display: flex;
    }

    .support-bot-header {
        background: #1f5f56;
        color: white;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .support-bot-header h3 {
        margin: 0 0 4px;
        font-size: 17px;
    }

    .support-bot-header p {
        margin: 0;
        font-size: 12px;
        opacity: .9;
    }

    .support-bot-close {
        border: 0;
        background: transparent;
        color: white;
        font-size: 30px;
        cursor: pointer;
        line-height: 1;
    }

    .support-bot-messages {
        flex: 1;
        padding: 16px;
        overflow-y: auto;
        background: #f7f8fa;
    }

    .support-bot-loading {
        text-align: center;
        color: #777;
        padding: 20px 10px;
    }

    .support-message-row {
        display: flex;
        margin-bottom: 12px;
    }

    .support-message-row.customer {
        justify-content: flex-start;
    }

    .support-message-row.bot,
    .support-message-row.employee,
    .support-message-row.admin,
    .support-message-row.system {
        justify-content: flex-end;
    }

    .support-message {
        max-width: 82%;
        padding: 11px 14px;
        border-radius: 14px;
        line-height: 1.7;
        font-size: 14px;
        word-break: break-word;
        white-space: pre-wrap;
    }

    .support-message-row.customer .support-message {
        background: #1f5f56;
        color: white;
        border-bottom-right-radius: 4px;
    }

    .support-message-row.bot .support-message {
        background: white;
        color: #222;
        border: 1px solid #e3e3e3;
        border-bottom-left-radius: 4px;
    }

    .support-message-row.employee .support-message,
    .support-message-row.admin .support-message {
        background: #e9f4ff;
        color: #1e293b;
        border: 1px solid #cfe5ff;
        border-bottom-left-radius: 4px;
    }

    .support-message-row.system .support-message {
        max-width: 95%;
        text-align: center;
        background: #fff3cd;
        color: #664d03;
        border: 1px solid #ffecb5;
        font-size: 12px;
    }

    .support-message-meta {
        display: block;
        margin-top: 5px;
        font-size: 10px;
        opacity: .7;
    }

    .support-bot-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        padding: 10px 14px;
        background: white;
        border-top: 1px solid #eee;
    }

    .support-bot-actions:empty {
        display: none;
    }

    .support-bot-action {
        border: 1px solid #1f5f56;
        background: white;
        color: #1f5f56;
        border-radius: 20px;
        padding: 8px 13px;
        cursor: pointer;
        font-size: 13px;
    }

    .support-bot-action.primary {
        color: white;
        background: #1f5f56;
    }

    .support-bot-action.danger {
        color: #b42318;
        border-color: #b42318;
    }

    .support-bot-form {
        display: flex;
        gap: 8px;
        align-items: flex-end;
        padding: 12px;
        background: white;
        border-top: 1px solid #eee;
    }

    .support-bot-form textarea {
        flex: 1;
        min-height: 44px;
        max-height: 120px;
        resize: none;
        border: 1px solid #d7d7d7;
        border-radius: 12px;
        padding: 10px 12px;
        outline: none;
        font-family: inherit;
    }

    .support-bot-form textarea:focus {
        border-color: #1f5f56;
    }

    .support-bot-form button {
        height: 44px;
        border: 0;
        border-radius: 11px;
        padding: 0 17px;
        background: #1f5f56;
        color: white;
        cursor: pointer;
    }

    .support-bot-form button:disabled,
    .support-bot-action:disabled {
        opacity: .6;
        cursor: not-allowed;
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
            height: 100%;
            max-width: none;
            max-height: none;
            border-radius: 0;
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
            return;
        }

        if (ticketMode === 'employee') {
            statusText.textContent =
                'المحادثة مع موظف الدعم';
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
                        'مرحبًا بك في دعم إبداع هوم. كيف يمكنني مساعدتك؟',
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
