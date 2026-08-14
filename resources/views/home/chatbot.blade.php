<div class="mic-chatbot" data-chatbot-endpoint="{{ url('/chatbot/message') }}">
    <button class="mic-chatbot-toggle" type="button" aria-label="Open chat assistant">
        <span class="ti-comment-alt"></span>
    </button>

    <section class="mic-chatbot-panel" aria-live="polite">
        <div class="mic-chatbot-header">
            <div>
                <strong>Mi Cusina Assistant</strong>
            </div>
            <button class="mic-chatbot-close" type="button" aria-label="Close chat assistant">&times;</button>
        </div>

        <div class="mic-chatbot-messages">
            <div class="mic-chatbot-message mic-chatbot-message-bot">
                Hi! Ask me about our foods, prices, booking, contact details, your cart, or your order status.
            </div>
        </div>

        <form class="mic-chatbot-form">
            <input type="text" name="message" autocomplete="off" maxlength="300" placeholder="Ask about Mi Cusina..." required>
            <button type="submit">Send</button>
        </form>
    </section>
</div>

<style>
    .mic-chatbot {
        background: transparent !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        padding: 0 !important;
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 3000;
        font-family: inherit;
    }

    .mic-chatbot:not(.is-open) {
        clip-path: circle(36px at 36px 36px);
        height: 72px;
        overflow: hidden;
        width: 72px;
    }

    .mic-chatbot-toggle {
        box-sizing: border-box !important;
        width: 72px;
        height: 72px;
        border: 0;
        border-radius: 50%;
        color: #fff;
        background: linear-gradient(135deg, #ff5275, #F88379);
        box-shadow: 0 12px 30px rgba(184, 49, 213, .35);
        cursor: pointer;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Keep the launcher itself transparent; the circle is drawn by this layer only. */
    .mic-chatbot .mic-chatbot-toggle {
        all: unset !important;
        align-items: center !important;
        background: transparent !important;
        box-sizing: border-box !important;
        cursor: pointer !important;
        display: flex !important;
        height: 72px !important;
        justify-content: center !important;
        margin: 0 !important;
        padding: 0 !important;
        position: relative !important;
        width: 72px !important;
    }

    .mic-chatbot .mic-chatbot-toggle::before {
        background: #ed0da8;
        border-radius: 50%;
        box-shadow: 0 12px 30px rgba(184, 49, 213, .35);
        content: '';
        inset: 0;
        position: absolute;
        z-index: 0;
    }

    .mic-chatbot .mic-chatbot-toggle span { color: #111; font-size: 32px; position: relative; z-index: 1; }

    .mic-chatbot-panel {
        position: absolute;
        right: 0;
        bottom: 88px;
        width: min(360px, calc(100vw - 32px));
        height: 500px;
        max-height: calc(100vh - 128px);
        background: #fff;
        color: #202020;
        border-radius: 8px;
        box-shadow: 0 18px 60px rgba(0, 0, 0, .28);
        display: none;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, .08);
    }

    .mic-chatbot.is-open .mic-chatbot-panel {
        display: flex;
        flex-direction: column;
    }

    .mic-chatbot-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        background: #242424;
        color: #fff;
    }

    .mic-chatbot-header span {
        display: block;
        color: rgba(255, 255, 255, .72);
        font-size: 12px;
        margin-top: 2px;
    }

    .mic-chatbot-close {
        border: 0;
        background: transparent;
        color: #fff;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
    }

    .mic-chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background: #f6f6f6;
    }

    .mic-chatbot-message {
        max-width: 86%;
        margin-bottom: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 14px;
        line-height: 1.4;
        white-space: pre-line;
        overflow-wrap: anywhere;
    }

    .mic-chatbot-message-bot {
        background: #fff;
        border: 1px solid #e4e4e4;
    }

    .mic-chatbot-message-user {
        margin-left: auto;
        color: #fff;
        background: #F88379;
    }

    .mic-chatbot-form {
        display: flex;
        gap: 8px;
        padding: 12px;
        border-top: 1px solid #e7e7e7;
        background: #fff;
    }

    .mic-chatbot-form input {
        flex: 1;
        min-width: 0;
        border: 1px solid #d7d7d7;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 14px;
    }

    .mic-chatbot-form button {
        border: 0;
        border-radius: 6px;
        padding: 0 14px;
        color: #fff;
        background: #F88379;
        cursor: pointer;
        font-weight: 600;
    }

    @media (max-width: 575.98px) {
        .mic-chatbot {
            right: 16px;
            bottom: 16px;
        }

        .mic-chatbot-toggle {
            width: 62px;
            height: 62px;
            font-size: 28px;
        }

        .mic-chatbot-panel {
            bottom: 78px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatbot = document.querySelector('.mic-chatbot');
        if (!chatbot) return;

        const toggle = chatbot.querySelector('.mic-chatbot-toggle');
        const close = chatbot.querySelector('.mic-chatbot-close');
        const form = chatbot.querySelector('.mic-chatbot-form');
        const input = form.querySelector('input[name="message"]');
        const messages = chatbot.querySelector('.mic-chatbot-messages');
        const endpoint = chatbot.dataset.chatbotEndpoint;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function addMessage(text, type) {
            const bubble = document.createElement('div');
            bubble.className = 'mic-chatbot-message mic-chatbot-message-' + type;
            bubble.textContent = text;
            messages.appendChild(bubble);
            messages.scrollTop = messages.scrollHeight;
            return bubble;
        }

        toggle.addEventListener('click', function () {
            chatbot.classList.toggle('is-open');
            if (chatbot.classList.contains('is-open')) {
                input.focus();
            }
        });

        close.addEventListener('click', function () {
            chatbot.classList.remove('is-open');
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const text = input.value.trim();
            if (!text) return;

            addMessage(text, 'user');
            input.value = '';
            const waiting = addMessage('Checking the system...', 'bot');

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ message: text })
            })
                .then(response => response.json())
                .then(data => {
                    waiting.textContent = data.reply || 'I could not find that in the system.';
                })
                .catch(() => {
                    waiting.textContent = 'Sorry, the chatbot is unavailable right now.';
                });
        });
    });
</script>
