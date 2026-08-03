/**
 * cart-anim.js
 * Hiệu ứng thêm sản phẩm vào giỏ hàng:
 *   - Icon giỏ hàng bay từ nút → biểu tượng giỏ ở header
 *   - Badge số lượng nảy và tăng lên khi sản phẩm chưa có trong giỏ
 */
(function () {
    'use strict';

    /* -------- Khởi tạo badge -------- */
    function initBadge() {
        const cartIcon = document.getElementById('cart-icon');
        if (!cartIcon) return null;

        // Kiểm tra badge đã tồn tại chưa
        let badge = cartIcon.querySelector('.cart-badge');
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'cart-badge';
            // Số lượng ban đầu từ PHP (được nhúng qua data attribute hoặc mặc định 0)
            const initCount = parseInt(cartIcon.dataset.count || '0', 10);
            badge.textContent = initCount > 0 ? initCount : '';
            badge.style.display = initCount > 0 ? 'flex' : 'none';
            cartIcon.appendChild(badge);
        }
        return badge;
    }

    /* -------- Cập nhật badge -------- */
    function updateBadge(badge, newCount) {
        badge.textContent = newCount;
        badge.style.display = newCount > 0 ? 'flex' : 'none';

        // Trigger bounce animation
        badge.classList.remove('bounce');
        void badge.offsetWidth; // reflow để reset animation
        badge.classList.add('bounce');
        setTimeout(() => badge.classList.remove('bounce'), 600);
    }

    /* -------- Tạo icon bay -------- */
    function launchFlyIcon(startEl, endEl, onDone) {
        const startRect = startEl.getBoundingClientRect();
        const endRect   = endEl.getBoundingClientRect();

        // Tạo icon
        const icon = document.createElement('div');
        icon.className = 'cart-fly-icon';
        icon.innerHTML = '<i class="bi bi-cart3"></i>';

        // Vị trí bắt đầu – center của nút thêm giỏ
        const startX = startRect.left + startRect.width / 2 - 18;
        const startY = startRect.top  + startRect.height / 2 - 18;

        icon.style.left = startX + 'px';
        icon.style.top  = startY + 'px';

        // Tắt transition tạm để set vị trí không bị animate
        icon.style.transition = 'none';
        document.body.appendChild(icon);

        // Vị trí đích – center của biểu tượng giỏ
        const endX = endRect.left + endRect.width / 2 - 18;
        const endY = endRect.top  + endRect.height / 2 - 18;

        // Trigger animation sau 1 frame
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                icon.style.transition =
                    'top 0.75s cubic-bezier(0.25,0.46,0.45,0.94), ' +
                    'left 0.75s cubic-bezier(0.25,0.46,0.45,0.94), ' +
                    'transform 0.75s ease, ' +
                    'opacity 0.25s ease 0.5s';
                icon.style.left      = endX + 'px';
                icon.style.top       = endY + 'px';
                icon.style.transform = 'scale(0.3)';
                icon.style.opacity   = '0';
            });
        });

        // Xoá icon sau khi animation xong
        setTimeout(() => {
            icon.remove();
            if (onDone) onDone();
        }, 850);
    }

    /* -------- Xử lý click nút thêm giỏ -------- */
    document.addEventListener('DOMContentLoaded', function () {
        const cartIcon = document.getElementById('cart-icon');
        const badge = initBadge();

        document.body.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-buy, #addToCartBtn, .btn-add-to-cart, [data-action="add-to-cart"]');
            if (!btn) return;

            const form = btn.closest('form');
            if (!form) return;

            const action = form.getAttribute('action');
            if (!action || !action.includes('/cart/add/')) return;

            e.preventDefault();

            const formData = new FormData(form);
            const headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            };

            btn.classList.add('adding');
            btn.style.transform = 'scale(0.94)';
            setTimeout(() => {
                btn.classList.remove('adding');
                btn.style.transform = '';
            }, 250);

            fetch(action, {
                method: 'POST',
                headers: headers,
                body: formData,
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(data => {
                if (!data.success) {
                    if (window.showToast) showToast('error', data.message || 'Không thể thêm sản phẩm vào giỏ hàng.');
                    else alert(data.message || 'Không thể thêm sản phẩm vào giỏ hàng.');
                    return;
                }

                if (cartIcon) {
                    launchFlyIcon(btn, cartIcon, function () {
                        if (badge) {
                            updateBadge(badge, data.cartCount);
                        }
                        if (window.showToast) {
                            showToast(data.added ? 'success' : 'info', data.added ? '🛒 Đã thêm sản phẩm vào giỏ hàng!' : '↑ Đã tăng số lượng sản phẩm trong giỏ.');
                        }
                    });
                } else {
                    if (window.showToast) {
                        showToast(data.added ? 'success' : 'info', data.added ? '🛒 Đã thêm sản phẩm vào giỏ hàng!' : '↑ Đã tăng số lượng sản phẩm trong giỏ.');
                    }
                }
            })
            .catch(err => {
                console.error('[cart-anim] Lỗi AJAX:', err);
                form.submit();
            });
        });

        // Global click visual feedback effect for buttons & interactive elements
        document.body.addEventListener('mousedown', function (e) {
            const target = e.target.closest('button, .btn, .nav-link, .keyword-tag, .product-card, .list-group-item');
            if (!target) return;

            target.style.transition = 'transform 0.1s cubic-bezier(0,0,0.2,1)';
            target.style.transform = 'scale(0.97) translateY(1px)';
        });

        document.body.addEventListener('mouseup', function (e) {
            const target = e.target.closest('button, .btn, .nav-link, .keyword-tag, .product-card, .list-group-item');
            if (!target) return;

            setTimeout(() => {
                target.style.transform = '';
            }, 120);
        });
    });
})();

(function () {
    'use strict';

    const STORAGE_KEY = 'hades_guest_wishlist';

    function isAuthenticated() {
        return document.querySelector('meta[name="auth-user"]')?.content === '1';
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function readGuestItems() {
        try {
            const items = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            return Array.isArray(items) ? items.filter(item => Number(item.id) > 0) : [];
        } catch (_) {
            return [];
        }
    }

    function writeGuestItems(items) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    }

    function productFromButton(productId, btn) {
        return {
            id: Number(productId),
            name: btn?.dataset.productName || 'Sản phẩm',
            image: btn?.dataset.productImage || '/image/logo.webp',
            sale_price: Number(btn?.dataset.productPrice || 0),
            original_price: Number(btn?.dataset.productOriginalPrice || 0),
            url: '/products/' + Number(productId),
        };
    }

    function updateHeaderBadge(count) {
        const badge = document.getElementById('headerWishlistBadge');
        if (!badge) return;
        badge.textContent = String(count);
        badge.classList.toggle('d-none', count <= 0);
    }

    function setButtonState(btn, added) {
        if (!btn) return;
        btn.dataset.wishlisted = added ? '1' : '0';
        btn.title = added ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích';
        const icon = btn.querySelector('i');
        if (icon) {
            const suffix = btn.id === 'detailWishlistBtn' ? ' me-1' : '';
            icon.className = 'bi ' + (added ? 'bi-heart-fill' : 'bi-heart') + suffix;
        }
        if (btn.id === 'detailWishlistBtn') {
            btn.classList.toggle('btn-danger', added);
            btn.classList.toggle('btn-outline-danger', !added);
            const text = document.getElementById('wishlistBtnText');
            if (text) text.textContent = added ? 'Đã thích' : 'Yêu thích';
        }
    }

    function syncButtons(ids) {
        const selected = new Set(ids.map(Number));
        document.querySelectorAll('[data-wishlist-product-id]').forEach(btn => {
            setButtonState(btn, selected.has(Number(btn.dataset.wishlistProductId)));
        });
    }

    function toggleGuest(productId, btn) {
        const items = readGuestItems();
        const index = items.findIndex(item => Number(item.id) === Number(productId));
        let added;
        if (index >= 0) {
            items.splice(index, 1);
            added = false;
        } else {
            items.unshift(productFromButton(productId, btn));
            added = true;
        }
        writeGuestItems(items);
        syncButtons(items.map(item => item.id));
        updateHeaderBadge(items.length);
        if (window.showToast) {
            showToast(added ? 'success' : 'info', added
                ? '❤️ Đã lưu sản phẩm yêu thích trên thiết bị này.'
                : 'Đã xóa khỏi danh sách yêu thích.');
        }
        return { added, count: items.length };
    }

    function toggleServer(productId, btn) {
        return fetch('/wishlist/toggle/' + Number(productId), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': csrfToken(),
                'Accept': 'application/json',
            },
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Không thể cập nhật yêu thích.');
            const added = data.action === 'added';
            setButtonState(btn, added);
            updateHeaderBadge(Number(data.count || 0));
            if (window.showToast) showToast(added ? 'success' : 'info', data.message);
            return data;
        });
    }

    function toggle(productId, btn) {
        if (btn) {
            btn.style.transform = 'scale(0.8)';
            setTimeout(() => { btn.style.transform = ''; }, 150);
        }
        if (!isAuthenticated()) {
            return Promise.resolve(toggleGuest(productId, btn));
        }
        return toggleServer(productId, btn).catch(error => {
            console.error('[wishlist]', error);
            if (window.showToast) showToast('error', 'Không thể cập nhật yêu thích. Vui lòng thử lại.');
        });
    }

    function removeGuest(productId) {
        const items = readGuestItems().filter(item => Number(item.id) !== Number(productId));
        writeGuestItems(items);
        updateHeaderBadge(items.length);
        syncButtons(items.map(item => item.id));
        return items;
    }

    function syncGuestToAccount() {
        const items = readGuestItems();
        if (!isAuthenticated() || items.length === 0) return;
        const body = new URLSearchParams();
        body.set('product_ids', JSON.stringify(items.map(item => Number(item.id))));

        fetch('/wishlist/sync', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': csrfToken(),
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json',
            },
            body,
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            if (!data.success) return;
            localStorage.removeItem(STORAGE_KEY);
            syncButtons(items.map(item => item.id));
            updateHeaderBadge(Number(data.count || 0));
            if (window.showToast) showToast('success', data.message);
        })
        .catch(error => console.error('[wishlist-sync]', error));
    }

    window.HadesWishlist = {
        isAuthenticated,
        getGuestItems: readGuestItems,
        removeGuest,
        updateHeaderBadge,
    };

    window.toggleCardWishlist = function (event, productId, btn) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        return toggle(productId, btn);
    };

    window.toggleWishlist = function (productId) {
        return toggle(productId, document.getElementById('detailWishlistBtn'));
    };

    document.addEventListener('DOMContentLoaded', function () {
        if (isAuthenticated()) {
            syncGuestToAccount();
        } else {
            const items = readGuestItems();
            syncButtons(items.map(item => item.id));
            updateHeaderBadge(items.length);
        }
    });
})();
