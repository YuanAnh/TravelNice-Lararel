<style>
.chat-widget { position: fixed; bottom: 24px; right: 24px; z-index: 1000; }
.chat-btn {
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg, #7C3AED, #4F46E5);
    color: #fff; border: none;
    box-shadow: 0 4px 20px rgba(124,58,237,.4);
    font-size: 24px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: transform .2s;
}
.chat-btn:hover { transform: scale(1.1); }
.chat-bubble-label {
    position: absolute; bottom: 62px; right: 0;
    background: #fff; border-radius: 12px 12px 0 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,.12);
    padding: 8px 14px; font-size: 13px; font-weight: 600;
    white-space: nowrap; color: #1A1A2E;
    animation: fadeIn .3s ease;
}
.chat-window {
    position: absolute; bottom: 70px; right: 0;
    width: 360px; height: 500px;
    background: #fff; border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0,0,0,.15);
    display: none; flex-direction: column;
    overflow: hidden;
    animation: slideUp .2s ease;
}
.chat-window.open { display: flex; }
@keyframes slideUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }

.chat-header {
    background: linear-gradient(135deg, #7C3AED, #4F46E5);
    padding: 14px 16px; display: flex; align-items: center; gap: 10px;
}
.chat-header .avatar { width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:18px; }
.chat-header .info .name { color:#fff;font-size:14px;font-weight:700; }
.chat-header .info .status { color:rgba(255,255,255,.75);font-size:11px; }
.chat-header .close-btn { margin-left:auto;background:none;border:none;color:rgba(255,255,255,.8);font-size:18px;cursor:pointer; }

.chat-messages { flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px; }
.msg { max-width:80%;padding:10px 14px;border-radius:12px;font-size:13px;line-height:1.5;word-break:break-word; }
.msg.user { background:#7C3AED;color:#fff;align-self:flex-end;border-radius:12px 12px 0 12px; }
.msg.bot { background:#F3F4F6;color:#1A1A2E;align-self:flex-start;border-radius:12px 12px 12px 0; }
.msg.typing { background:#F3F4F6;align-self:flex-start; }
.typing-dots span { display:inline-block;width:6px;height:6px;border-radius:50%;background:#9CA3AF;margin:0 2px;animation:dot-bounce .8s infinite; }
.typing-dots span:nth-child(2){animation-delay:.15s}
.typing-dots span:nth-child(3){animation-delay:.3s}
@keyframes dot-bounce{0%,80%,100%{transform:scale(0.6);opacity:.5}40%{transform:scale(1);opacity:1}}

.chat-input-wrap { padding:12px;border-top:1px solid #E5E7EB;display:flex;gap:8px; }
.chat-input { flex:1;border:1.5px solid #E5E7EB;border-radius:20px;padding:8px 14px;font-size:13px;outline:none;resize:none;height:38px;line-height:1.4; }
.chat-input:focus { border-color:#7C3AED; }
.chat-send { width:38px;height:38px;border-radius:50%;background:#7C3AED;color:#fff;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:opacity .15s; }
.chat-send:hover { opacity:.85; }
.chat-send:disabled { opacity:.5;cursor:not-allowed; }

.quick-btns { display:flex;flex-wrap:wrap;gap:6px;padding:0 16px 12px; }
.quick-btn { font-size:12px;padding:4px 10px;border-radius:12px;border:1px solid #7C3AED;color:#7C3AED;background:#fff;cursor:pointer;transition:all .15s; }
.quick-btn:hover { background:#7C3AED;color:#fff; }
</style>

<div class="chat-widget" id="chatWidget">
    <div id="chatBubble" class="chat-bubble-label">💬 Tư vấn AI miễn phí</div>

    <div class="chat-window" id="chatWindow">
        <div class="chat-header">
            <div class="avatar">🤖</div>
            <div class="info">
                <div class="name">TravelNice AI</div>
                <div class="status">🟢 Đang hoạt động</div>
            </div>
            <button class="close-btn" onclick="toggleChat()"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="chat-messages" id="chatMessages">
            <div class="msg bot">
                Xin chào! Tôi là trợ lý AI của TravelNice 👋<br>
                Tôi có thể giúp bạn tìm tour phù hợp, tư vấn lịch trình và giải đáp mọi thắc mắc về du lịch!
            </div>
        </div>

        <div class="quick-btns" id="quickBtns">
            <button class="quick-btn" onclick="sendQuick('Tour Nhật Bản giá tốt nhất?')">🗾 Tour Nhật Bản</button>
            <button class="quick-btn" onclick="sendQuick('Tour trong nước dưới 5 triệu?')">🏖️ Tour trong nước</button>
            <button class="quick-btn" onclick="sendQuick('Tour phù hợp cho gia đình có trẻ em?')">👨‍👩‍👧 Tour gia đình</button>
        </div>

        <div class="chat-input-wrap">
            <input type="text" class="chat-input" id="chatInput"
                   placeholder="Nhập câu hỏi..." maxlength="500"
                   onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();sendMessage();}">
            <button class="chat-send" id="sendBtn" onclick="sendMessage()">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>

    <button class="chat-btn" onclick="toggleChat()">
        <i class="bi bi-chat-dots-fill" id="chatIcon"></i>
    </button>
</div>

<script>
let chatHistory = [];
let isOpen = false;
let bubbleTimeout;

// Ẩn bubble sau 5s
bubbleTimeout = setTimeout(() => {
    const bubble = document.getElementById('chatBubble');
    if (bubble) bubble.style.display = 'none';
}, 5000);

function toggleChat() {
    isOpen = !isOpen;
    const win = document.getElementById('chatWindow');
    const icon = document.getElementById('chatIcon');
    const bubble = document.getElementById('chatBubble');

    if (isOpen) {
        win.classList.add('open');
        icon.className = 'bi bi-x-lg';
        if (bubble) bubble.style.display = 'none';
        clearTimeout(bubbleTimeout);
        document.getElementById('chatInput').focus();
    } else {
        win.classList.remove('open');
        icon.className = 'bi bi-chat-dots-fill';
    }
}

function sendQuick(text) {
    document.getElementById('chatInput').value = text;
    document.getElementById('quickBtns').style.display = 'none';
    sendMessage();
}

async function sendMessage() {
    const input = document.getElementById('chatInput');
    const msg = input.value.trim();
    if (!msg) return;

    input.value = '';
    appendMessage('user', msg);

    // Typing indicator
    const typingId = appendTyping();
    document.getElementById('sendBtn').disabled = true;

    chatHistory.push({ role: 'user', content: msg });

    try {
        const res = await fetch('{{ route("ai.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({ message: msg, history: chatHistory.slice(-10) })
        });

        const data = await res.json();
        removeTyping(typingId);

        const reply = data.reply || 'Xin lỗi, có lỗi xảy ra!';
        appendMessage('bot', reply);
        chatHistory.push({ role: 'assistant', content: reply });

    } catch (e) {
        removeTyping(typingId);
        appendMessage('bot', 'Không thể kết nối AI. Vui lòng thử lại!');
    }

    document.getElementById('sendBtn').disabled = false;
}

function appendMessage(role, text) {
    const msgs = document.getElementById('chatMessages');
    const div = document.createElement('div');
    div.className = 'msg ' + role;
    div.textContent = text;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
}

function appendTyping() {
    const msgs = document.getElementById('chatMessages');
    const id = 'typing_' + Date.now();
    const div = document.createElement('div');
    div.className = 'msg typing';
    div.id = id;
    div.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return id;
}

function removeTyping(id) {
    document.getElementById(id)?.remove();
}
</script>