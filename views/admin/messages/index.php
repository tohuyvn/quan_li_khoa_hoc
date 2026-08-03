<?php $title = $title ?? 'Quản lý Tin nhắn & Đánh giá'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">💬 Hỗ trợ khách hàng trực tuyến</h4>
    <div class="d-flex gap-2">
        <a href="/admin/messages" class="btn btn-sm btn-dark fw-bold"><i class="bi bi-chat-dots-fill me-1"></i> Trực tuyến</a>
        <a href="/admin/messages/reviews" class="btn btn-sm btn-outline-secondary"><i class="bi bi-star-fill me-1"></i> Đánh giá của khách</a>
    </div>
</div>

<div class="row g-3">
    <!-- Left Column: Conversations List -->
    <div class="col-md-4 col-lg-3">
        <div class="card border rounded-3 p-0 bg-white shadow-sm overflow-hidden" style="height: 580px; display: flex; flex-direction: column;">
            <div class="p-3 border-bottom bg-light fw-bold text-dark"><i class="bi bi-people-fill me-2"></i>Cuộc trò chuyện</div>
            <div class="list-group list-group-flush overflow-y-auto flex-grow-1">
                <?php if (empty($conversations)): ?>
                    <div class="text-center py-5 text-muted small">
                        <i class="bi bi-chat-square-text d-block mb-2" style="font-size: 1.5rem;"></i>
                        Chưa có cuộc trò chuyện nào.
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv):
                        $isActive = ($activeSessionId === $conv['session_id']);
                        $db = \App\DB::connection();
                        $stmt = $db->prepare('SELECT message, sender FROM support_messages WHERE session_id=? ORDER BY id DESC LIMIT 1');
                        $stmt->execute([$conv['session_id']]);
                        $last = $stmt->fetch();
                        $snippet = ($last['sender'] === 'admin' ? 'Bạn: ' : '') . ($last['message'] ?? '');
                    ?>
                        <a href="/admin/messages/<?= urlencode($conv['session_id']) ?>" 
                           class="list-group-item list-group-item-action border-bottom p-3 transition-all <?= $isActive ? 'bg-primary text-white active' : '' ?>"
                           style="border-left: 4px solid <?= $conv['unread'] > 0 ? 'var(--accent, #d70018)' : 'transparent' ?>;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-truncate <?= $isActive ? 'text-white' : 'text-dark' ?>" style="max-width: 140px;">
                                    <?= e($conv['user_name']) ?>
                                </span>
                                <small class="text-truncate <?= $isActive ? 'text-white-50' : 'text-muted' ?>" style="font-size: 10px;">
                                    <?= date('H:i d/m', strtotime($conv['last_at'])) ?>
                                </small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-truncate small <?= $isActive ? 'text-white-50' : 'text-muted' ?>" style="max-width: 170px; font-size: 12px;">
                                    <?= e($snippet) ?>
                                </span>
                                <?php if ($conv['unread'] > 0): ?>
                                    <span class="badge bg-danger rounded-pill bounce" style="font-size: 10px; padding: 3px 6px;"><?= (int)$conv['unread'] ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Active Chat & Sidebar Info -->
    <div class="col-md-8 col-lg-9">
        <div class="card border rounded-3 bg-white shadow-sm overflow-hidden" style="height: 580px;">
            <?php if (!$activeSessionId): ?>
                <!-- No Conversation Selected -->
                <div class="d-flex flex-column justify-content-center align-items-center text-center h-100 p-5 text-muted">
                    <div style="font-size: 4rem; opacity: 0.3;"><i class="bi bi-chat-left-dots-fill"></i></div>
                    <h5 class="fw-bold mt-3 text-dark">Bắt đầu tư vấn</h5>
                    <p class="small mx-auto" style="max-width: 320px;">Chọn một cuộc trò chuyện từ danh sách bên trái để phản hồi thắc mắc của khách hàng.</p>
                </div>
            <?php else: ?>
                <!-- Conversation Active -->
                <div class="d-flex flex-column h-100">
                    <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-dark"><i class="bi bi-circle-fill text-success small me-1"></i> <?= e($userName) ?></span>
                            <span id="admin-user-status" class="ms-2"></span>
                            <?php if ($userEmail): ?>
                                <span class="text-muted ms-2 small">(<?= e($userEmail) ?>)</span>
                            <?php endif; ?>
                        </div>
                        <form method="post" action="/admin/messages/<?= urlencode($activeSessionId) ?>/delete"
                              onsubmit="return confirm('Xóa vĩnh viễn cuộc trò chuyện này?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Xóa hội thoại
                            </button>
                        </form>
                    </div>

                    <!-- Inner Body: Chat Box and Customer Info Sidebar -->
                    <div class="d-flex flex-grow-1 overflow-hidden">
                        <!-- Chat Messages Feed -->
                        <div class="d-flex flex-column flex-grow-1 p-3 bg-light-subtle overflow-hidden">
                            <div id="admin-chat-window" class="flex-grow-1 overflow-y-auto d-flex flex-column gap-2 mb-3 p-2" style="background: transparent;">
                                <?php foreach ($messages as $msg): ?>
                                    <?php $isAdmin = ($msg['sender'] === 'admin'); ?>
                                    <div class="d-flex <?= $isAdmin ? 'justify-content-end' : 'justify-content-start' ?>">
                                        <div class="shadow-sm p-2 px-3 rounded-4" style="
                                            max-width: 70%;
                                            font-size: 13.5px;
                                            line-height: 1.4;
                                            background-color: <?= $isAdmin ? '#1d4ed8' : '#fff' ?>;
                                            color: <?= $isAdmin ? '#fff' : '#333' ?>;
                                            border-top-right-radius: <?= $isAdmin ? '4px' : '16px' ?>;
                                            border-top-left-radius: <?= $isAdmin ? '16px' : '4px' ?>;
                                            border: <?= $isAdmin ? 'none' : '1px solid #e2e8f0' ?>;
                                        ">
                                            <?php if (!$isAdmin): ?>
                                                <div class="fw-bold small text-muted mb-1" style="font-size: 10px;"><?= e($msg['user_name']) ?></div>
                                            <?php endif; ?>
                                            <?= nl2br(e($msg['message'])) ?>
                                            <div class="text-end small mt-1 opacity-70" style="font-size: 9px;">
                                                <?= date('H:i', strtotime($msg['created_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div id="admin-chat-end"></div>
                            </div>

                            <!-- Input Box -->
                            <div class="d-flex gap-2">
                                <textarea id="admin-reply-input"
                                          rows="2"
                                          class="form-control form-control-sm"
                                          placeholder="Nhập phản hồi... (Enter để gửi, Shift+Enter xuống dòng)"
                                          style="resize:none; border-radius: 12px;"></textarea>
                                <button id="admin-reply-btn" class="btn btn-primary px-3 align-self-end" style="height: 40px; border-radius: 12px;">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Customer Info Sidebar -->
                        <div class="border-start bg-light overflow-y-auto p-3" style="width: 240px; flex-shrink: 0; font-size: 12px;">
                            <h6 class="fw-bold mb-3 text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 10px;">📋 Đơn hàng gần nhất</h6>
                            <?php if (!$recentOrder): ?>
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-bag-x d-block mb-1" style="font-size: 1.2rem;"></i>
                                    Chưa có đơn hàng nào từ email này.
                                </div>
                            <?php else: ?>
                                <div class="card p-2 border rounded-3 mb-2 bg-white shadow-sm">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-bold text-danger">#<?= $recentOrder['id'] ?></span>
                                        <small class="text-muted"><?= date('d/m/y', strtotime($recentOrder['created_at'])) ?></small>
                                    </div>
                                    <div class="mb-1"><strong>Tổng:</strong> <?= number_format($recentOrder['total_amount'], 0, ',', '.') ?>đ</div>
                                    <div class="mb-1">
                                        <strong>Trạng thái:</strong> 
                                        <?php
                                        $statusClass = match ($recentOrder['status']) {
                                            'confirmed' => 'success',
                                            'shipping' => 'primary',
                                            'completed' => 'dark',
                                            'cancelled' => 'danger',
                                            default => 'warning'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?> text-white" style="font-size: 9px;"><?= e($recentOrder['status']) ?></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Thanh toán:</strong>
                                        <span class="badge bg-secondary" style="font-size: 9px;"><?= e($recentOrder['payment_status']) ?></span>
                                    </div>
                                    
                                    <hr class="my-2">
                                    <div class="fw-bold mb-1 text-muted">Sản phẩm:</div>
                                    <ul class="list-unstyled mb-0 ps-0" style="line-height: 1.4;">
                                        <?php foreach ($recentOrderItems as $item): ?>
                                            <li class="text-truncate mb-1" title="<?= e($item['name']) ?>">
                                                • <?= (int)$item['quantity'] ?>x <?= e($item['name']) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($activeSessionId): ?>
<script>
(function () {
    const sessionId = <?= json_encode($activeSessionId) ?>;
    const userName = <?= json_encode($userName) ?>;
    const chatWindow = document.getElementById('admin-chat-window');
    const input = document.getElementById('admin-reply-input');
    const sendBtn = document.getElementById('admin-reply-btn');
    let lastId = <?= !empty($messages) ? (int)end($messages)['id'] : 0 ?>;
    let adminEventSource = null;

    // Load notification sound
    const notificationSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-84.wav');

    function scrollEnd() {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }
    scrollEnd();

    function appendMessage(msg) {
        const isAdmin = (msg.sender === 'admin');
        const wrap = document.createElement('div');
        wrap.className = 'd-flex ' + (isAdmin ? 'justify-content-end' : 'justify-content-start');

        const bubble = document.createElement('div');
        bubble.className = 'shadow-sm p-2 px-3 rounded-4';
        bubble.style.cssText = 'max-width: 70%; font-size: 13.5px; line-height: 1.4; background-color: ' + 
                               (isAdmin ? '#1d4ed8' : '#fff') + '; color: ' + (isAdmin ? '#fff' : '#333') + '; ' +
                               'border-top-right-radius: ' + (isAdmin ? '4px' : '16px') + '; border-top-left-radius: ' + 
                               (isAdmin ? '16px' : '4px') + '; border: ' + (isAdmin ? 'none' : '1px solid #e2e8f0') + ';';

        const nameHtml = !isAdmin ? '<div class="fw-bold small text-muted mb-1" style="font-size: 10px;">' + msg.user_name + '</div>' : '';
        const time = msg.created_at ? msg.created_at.substr(11, 5) : new Date().toTimeString().substr(0, 5);
        bubble.innerHTML = nameHtml + msg.message.replace(/\n/g, '<br>') +
                           '<div class="text-end small mt-1 opacity-70" style="font-size: 9px;">' + time + '</div>';

        wrap.appendChild(bubble);
        
        var typingEl = document.getElementById('admin-user-typing');
        if (typingEl) {
            typingEl.before(wrap);
        } else {
            document.getElementById('admin-chat-end').before(wrap);
        }
        scrollEnd();
        
        // Play notification sound for customer messages
        if (!isAdmin) {
            notificationSound.play().catch(e => console.log('Audio autoplay prevented'));
        }
    }

    function send() {
        const text = input.value.trim();
        if (!text) return;
        input.value = '';
        reportAdminTyping(false);
        sendBtn.disabled = true;

        const fd = new FormData();
        fd.append('message', text);
        fd.append('_csrf', document.querySelector('meta[name=csrf]')?.content || '');

        fetch('/admin/messages/' + encodeURIComponent(sessionId) + '/reply', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                appendMessage({ sender: 'admin', message: text, created_at: null, user_name: 'Admin' });
                lastId = data.id || lastId;
            } else {
                alert(data.error || 'Lỗi gửi tin nhắn.');
            }
        })
        .catch(() => alert('Lỗi kết nối.'))
        .finally(() => {
            sendBtn.disabled = false;
            input.focus();
        });
    }

    sendBtn.addEventListener('click', send);
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            send();
        }
    });

    function startSSE() {
        if (adminEventSource) {
            adminEventSource.close();
        }
        adminEventSource = new EventSource('/admin/messages/' + encodeURIComponent(sessionId) + '/sse?last_id=' + lastId, { withCredentials: true });
        
        adminEventSource.addEventListener('message', function (e) {
            var msg = JSON.parse(e.data);
            if (msg.sender !== 'admin') {
                appendMessage(msg);
            }
            var id = parseInt(msg.id, 10);
            if (id > lastId) lastId = id;
        });
        
        adminEventSource.addEventListener('typing', function (e) {
            var data = JSON.parse(e.data);
            var typingEl = document.getElementById('admin-user-typing');
            if (data.typing) {
                if (!typingEl) {
                    typingEl = document.createElement('div');
                    typingEl.id = 'admin-user-typing';
                    typingEl.className = 'd-flex justify-content-start mb-2';
                    typingEl.innerHTML = '<div class="shadow-sm p-2 px-3 rounded-4" style="max-width: 70%; font-size:13.5px; line-height:1.4; background-color:#e2e8f0; color:#333; border-top-left-radius: 4px; border-top-right-radius: 16px; border: 1px solid #cbd5e1;"><div class="typing-indicator" style="display:flex; gap:3px; margin-bottom: 2px;"><span class="typing-dot" style="width:4px; height:4px; background:#4b5563; border-radius:50%; animation: bounce 1.4s infinite both;"></span><span class="typing-dot" style="width:4px; height:4px; background:#4b5563; border-radius:50%; animation: bounce 1.4s infinite both 0.2s;"></span><span class="typing-dot" style="width:4px; height:4px; background:#4b5563; border-radius:50%; animation: bounce 1.4s infinite both 0.4s;"></span></div>' + userName + ' đang nhập...</div>';
                    document.getElementById('admin-chat-end').before(typingEl);
                    scrollEnd();
                }
            } else {
                if (typingEl) {
                    typingEl.remove();
                }
            }
        });
        
        adminEventSource.addEventListener('status', function (e) {
            var data = JSON.parse(e.data);
            var statusEl = document.getElementById('admin-user-status');
            if (statusEl) {
                if (data.online) {
                    statusEl.innerHTML = '<span class="badge bg-success" style="font-size: 10px;"><i class="bi bi-circle-fill me-1"></i>Trực tuyến</span>';
                } else {
                    statusEl.innerHTML = '<span class="badge bg-secondary" style="font-size: 10px;"><i class="bi bi-circle-fill me-1"></i>Ngoại tuyến</span>';
                }
            }
        });
    }

    startSSE();

    let adminTypingTimeout = null;
    let isAdminTypingState = false;

    function reportAdminTyping(typing) {
        if (isAdminTypingState === typing) return;
        isAdminTypingState = typing;
        
        const fd = new FormData();
        fd.append('typing', typing ? '1' : '0');
        fd.append('_csrf', document.querySelector('meta[name=csrf]')?.content || '');
        
        fetch('/admin/messages/' + encodeURIComponent(sessionId) + '/typing', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).catch(function() {});
    }

    if (input) {
        input.addEventListener('input', function () {
            reportAdminTyping(true);
            if (adminTypingTimeout) clearTimeout(adminTypingTimeout);
            adminTypingTimeout = setTimeout(function () {
                reportAdminTyping(false);
            }, 2500);
        });
        
        input.addEventListener('blur', function () {
            reportAdminTyping(false);
        });
    }
})();
</script>
<?php endif; ?>
