<?php $title = 'Chat với ' . e($userName); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="/admin/messages" class="btn btn-sm btn-outline-secondary me-2">← Quay lại</a>
        <strong>💬 <?= e($userName) ?></strong>
        <?php if ($userEmail): ?>
            <span class="text-muted ms-2 small">(<?= e($userEmail) ?>)</span>
        <?php endif; ?>
    </div>
    <form method="post" action="/admin/messages/<?= urlencode($sessionId) ?>/delete"
          onsubmit="return confirm('Xóa toàn bộ cuộc hội thoại này?')">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-sm btn-outline-danger">🗑 Xóa</button>
    </form>
</div>

<!-- Chat window -->
<div id="admin-chat-window" style="
    height: 460px;
    overflow-y: auto;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    gap: 10px;
">
    <?php foreach ($messages as $msg): ?>
        <?php $isAdmin = $msg['sender'] === 'admin'; ?>
        <div class="d-flex <?= $isAdmin ? 'justify-content-end' : 'justify-content-start' ?>">
            <div style="
                max-width: 65%;
                padding: 10px 15px;
                border-radius: <?= $isAdmin ? '18px 18px 4px 18px' : '18px 18px 18px 4px' ?>;
                background: <?= $isAdmin ? '#1d4ed8' : '#fff' ?>;
                color: <?= $isAdmin ? '#fff' : '#333' ?>;
                border: <?= $isAdmin ? 'none' : '1px solid #e0e0e0' ?>;
                box-shadow: 0 2px 8px rgba(0,0,0,0.07);
                font-size: 14px;
                line-height: 1.5;
            ">
                <?php if (!$isAdmin): ?>
                    <div style="font-size:11px;color:#888;margin-bottom:3px;"><?= e($msg['user_name']) ?></div>
                <?php endif; ?>
                <?= nl2br(e($msg['message'])) ?>
                <div style="font-size:10px;opacity:0.6;margin-top:4px;text-align:right;">
                    <?= date('H:i', strtotime($msg['created_at'])) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div id="admin-chat-end"></div>
</div>

<!-- Reply box -->
<div class="d-flex gap-2 mt-3">
    <textarea id="admin-reply-input"
              rows="2"
              class="form-control"
              placeholder="Nhập tin nhắn trả lời... (Enter để gửi, Shift+Enter xuống dòng)"
              style="resize:none;border-radius:12px;"></textarea>
    <button id="admin-reply-btn" class="btn btn-primary px-4" style="border-radius:12px;">
        <i class="bi bi-send-fill"></i>
    </button>
</div>

<script>
(function () {
    var sessionId  = <?= json_encode($sessionId) ?>;
    var chatWindow = document.getElementById('admin-chat-window');
    var input      = document.getElementById('admin-reply-input');
    var sendBtn    = document.getElementById('admin-reply-btn');
    var lastId     = <?= !empty($messages) ? (int)end($messages)['id'] : 0 ?>;

    /* Scroll xuống cuối */
    function scrollEnd() {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }
    scrollEnd();

    /* Render 1 tin nhắn mới */
    function appendMessage(msg) {
        var isAdmin = msg.sender === 'admin';
        var wrap = document.createElement('div');
        wrap.className = 'd-flex ' + (isAdmin ? 'justify-content-end' : 'justify-content-start');

        var bubble = document.createElement('div');
        bubble.style.cssText = 'max-width:65%;padding:10px 15px;border-radius:' +
            (isAdmin ? '18px 18px 4px 18px' : '18px 18px 18px 4px') +
            ';background:' + (isAdmin ? '#1d4ed8' : '#fff') +
            ';color:' + (isAdmin ? '#fff' : '#333') +
            ';border:' + (isAdmin ? 'none' : '1px solid #e0e0e0') +
            ';box-shadow:0 2px 8px rgba(0,0,0,0.07);font-size:14px;line-height:1.5;';

        var nameHtml = !isAdmin ? '<div style="font-size:11px;color:#888;margin-bottom:3px;">' + msg.user_name + '</div>' : '';
        var time = msg.created_at ? msg.created_at.substr(11,5) : new Date().toTimeString().substr(0,5);
        bubble.innerHTML = nameHtml + msg.message.replace(/\n/g,'<br>') +
            '<div style="font-size:10px;opacity:0.6;margin-top:4px;text-align:right;">' + time + '</div>';

        wrap.appendChild(bubble);
        document.getElementById('admin-chat-end').before(wrap);
        scrollEnd();
    }

    /* Gửi tin nhắn */
    function send() {
        var text = input.value.trim();
        if (!text) return;
        input.value = '';
        sendBtn.disabled = true;

        var fd = new FormData();
        fd.append('message', text);
        fd.append('_csrf', document.querySelector('meta[name=csrf]')?.content || <?= json_encode(csrf_token()) ?>);

        fetch('/admin/messages/' + encodeURIComponent(sessionId) + '/reply', {
            method: 'POST', body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function (data) {
            if (data.success) {
                appendMessage({ sender: 'admin', message: text, created_at: null, user_name: 'Admin' });
                lastId = data.id || lastId;
            } else {
                alert(data.error || 'Không gửi được tin nhắn.');
            }
        })
        .catch(function () {
            alert('Lỗi kết nối. Vui lòng tải lại trang và thử lại.');
        })
        .finally(function () { sendBtn.disabled = false; input.focus(); });
    }

    sendBtn.addEventListener('click', send);
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
    });

    /* Poll tin nhắn mới từ user mỗi 3s */
    setInterval(function () {
        fetch('/admin/messages/' + encodeURIComponent(sessionId) + '/poll?after=' + lastId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            (data.messages || []).forEach(function (msg) {
                if (msg.sender !== 'admin') {
                    appendMessage(msg);
                }
                if (parseInt(msg.id) > lastId) lastId = parseInt(msg.id);
            });
        });
    }, 2000);
})();
</script>
