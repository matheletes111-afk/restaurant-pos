<script>
(function() {
    'use strict';

    // Store state
    let lastKnownMaxId = 0;
    let isInitialLoad = true;
    let cachedNotifications = [];
    const fetchUrl = "{{ route('restaurant.qr.notifications') }}";
    const markReadBaseUrl = "{{ url('/restaurant/qr-notifications/mark-read') }}";
    const csrfToken = "{{ csrf_token() }}";

    // Helper: Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // Initialize initial max ID from existing DOM items if any
    const existingItems = document.querySelectorAll('.qr-notif-item');
    existingItems.forEach(item => {
        const id = parseInt(item.getAttribute('data-order-id'), 10);
        if (!isNaN(id) && id > lastKnownMaxId) {
            lastKnownMaxId = id;
        }
    });

    // Gentle 2-tone melodic notification chime using browser Web Audio API
    function playQrOrderChime() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const now = ctx.currentTime;

            // Tone 1: 587.33Hz (D5)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(587.33, now);
            gain1.gain.setValueAtTime(0.12, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.35);

            // Tone 2: 880.00Hz (A5)
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880.00, now + 0.12);
            gain2.gain.setValueAtTime(0.18, now + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.12);
            osc2.stop(now + 0.55);
        } catch (e) {
            // Audio context policy
        }
    }

    // Display floating toast in the top right corner
    function showQrOrderToast(order) {
        playQrOrderChime();
        const container = document.getElementById('qrOrderToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'qr-toast-card mb-3 p-3';
        toast.id = 'qr-toast-' + order.id;
        toast.innerHTML = `
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 30px; height: 30px; background: #fff2ea; color: #ff6a00; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <strong class="text-dark d-block" style="font-size: 0.88rem; line-height: 1.2;">New QR Order Placed!</strong>
                        <small class="text-muted" style="font-size: 0.72rem;">Table QR Ordering</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-sm" style="font-size: 0.65rem;" onclick="this.closest('.qr-toast-card').remove()"></button>
            </div>
            <div class="bg-light rounded p-2 mb-2" style="font-size: 0.8rem; border: 1px solid #f1f5f9;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold text-dark"><i class="fas fa-chair me-1 text-muted"></i>${escapeHtml(order.table_name)}</span>
                    <span class="fw-bold text-primary font-monospace">${escapeHtml(order.order_no)}</span>
                </div>
                <div class="text-truncate text-muted">${escapeHtml(order.customer_name)} &bull; <strong class="text-success">₹${escapeHtml(order.grand_total)}</strong></div>
                ${order.items_summary ? `<div class="text-truncate text-secondary mt-1" style="font-size: 0.74rem;">${escapeHtml(order.items_summary)}</div>` : ''}
            </div>
            <div class="d-flex gap-2">
                <a href="${order.view_url}" class="btn btn-sm btn-primary rounded-pill flex-grow-1 fw-semibold py-1" style="font-size: 0.78rem;">
                    View Order
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1" style="font-size: 0.78rem;" onclick="this.closest('.qr-toast-card').remove()">
                    Dismiss
                </button>
            </div>
        `;

        container.appendChild(toast);

        // Auto remove toast after 9 seconds
        setTimeout(() => {
            if (toast && toast.parentNode) {
                toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 400);
            }
        }, 9000);
    }

    // Update Badge
    function updateBadge(unreadCount) {
        const badge = document.getElementById('qrNotifBadge');
        if (!badge) return;

        if (unreadCount > 0) {
            badge.innerText = unreadCount > 99 ? '99+' : unreadCount;
            badge.style.display = '';
        } else {
            badge.style.display = 'none';
        }
    }

    // Render Notifications in Dropdown
    function renderNotifications(notifications) {
        cachedNotifications = notifications || [];
        const container = document.getElementById('qrNotifListContainer');
        if (!container) return;

        if (!notifications || notifications.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4 px-3" id="qrNotifEmptyState">
                    <div class="mb-2 text-muted opacity-50">
                        <i class="fas fa-bell-slash fa-2x"></i>
                    </div>
                    <p class="text-muted mb-0 fw-semibold" style="font-size: 0.85rem;">No QR orders in past 7 days</p>
                    <small class="text-muted" style="font-size: 0.75rem;">New customer QR orders will appear here automatically</small>
                </div>
            `;
            return;
        }

        let html = '';
        notifications.forEach(ord => {
            const isUnread = !ord.is_read;
            let statusBadge = '';
            if (ord.order_status === 'PENDING') {
                statusBadge = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">PENDING</span>';
            } else if (ord.order_status === 'APPROVED') {
                statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">APPROVED</span>';
            } else if (ord.order_status === 'REJECTED') {
                statusBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">REJECTED</span>';
            }

            html += `
                <a
                    href="${ord.view_url}"
                    class="list-group-item list-group-item-action px-3 py-2.5 border-bottom qr-notif-item ${isUnread ? 'qr-item-unread' : ''}"
                    data-order-id="${ord.id}"
                    style="text-decoration: none; transition: background 0.2s;"
                >
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="d-flex align-items-center gap-1.5">
                            ${isUnread ? '<span class="qr-unread-dot me-1" title="Unread"></span>' : ''}
                            <span class="badge bg-dark-subtle text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
                                <i class="fas fa-chair text-muted me-1"></i>${escapeHtml(ord.table_name)}
                            </span>
                            ${statusBadge}
                        </div>
                        <span class="fw-bold text-primary font-monospace" style="font-size: 0.78rem;">
                            ${escapeHtml(ord.order_no)}
                        </span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-truncate me-2" style="max-width: 220px;">
                            <div class="fw-semibold text-dark text-truncate" style="font-size: 0.83rem;">
                                ${escapeHtml(ord.customer_name)} ${ord.customer_phone ? `<span class="text-muted fw-normal">(${escapeHtml(ord.customer_phone)})</span>` : ''}
                            </div>
                            ${ord.items_summary ? `<div class="text-muted text-truncate" style="font-size: 0.75rem;">${escapeHtml(ord.items_summary)}</div>` : ''}
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="fw-bold text-success" style="font-size: 0.88rem;">
                                ₹${escapeHtml(ord.grand_total)}
                            </span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-1 text-muted" style="font-size: 0.7rem;">
                        <span><i class="far fa-clock me-1"></i>${escapeHtml(ord.created_at_human)}</span>
                        <span>${escapeHtml(ord.created_at_date)}, ${escapeHtml(ord.created_at_time)}</span>
                    </div>
                </a>
            `;
        });

        container.innerHTML = html;
    }

    // Fetch Notifications from Server
    async function fetchQrNotifications() {
        try {
            const response = await fetch(fetchUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) return;

            const data = await response.json();
            if (!data.success) return;

            updateBadge(data.unread_count);

            // Check if any brand new orders arrived
            if (!isInitialLoad && data.notifications && data.notifications.length > 0) {
                const newOrders = data.notifications.filter(o => o.id > lastKnownMaxId);
                newOrders.forEach(ord => {
                    showQrOrderToast(ord);
                });
            }

            // Update lastKnownMaxId
            if (data.latest_id && data.latest_id > lastKnownMaxId) {
                lastKnownMaxId = data.latest_id;
            }

            // Re-render list
            renderNotifications(data.notifications);

            isInitialLoad = false;
        } catch (e) {
            // Silently fail on network disruption
        }
    }

    // Handler for Mark Single Read on Click
    function handleMarkSingleRead(orderId) {
        if (!orderId) return;

        // Try POST first, fallback to GET
        fetch(`${markReadBaseUrl}/${orderId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ _token: csrfToken })
        }).then(res => {
            if (!res.ok) {
                fetch(`${markReadBaseUrl}/${orderId}`);
            }
        }).catch(() => {
            fetch(`${markReadBaseUrl}/${orderId}`).catch(() => {});
        });
    }

    // Expose globally
    window.handleQrMarkSingleRead = handleMarkSingleRead;

    // Listen for notification item clicks to mark as read
    document.addEventListener('click', function(e) {
        const item = e.target.closest('.qr-notif-item');
        if (item) {
            const orderId = item.getAttribute('data-order-id');
            if (orderId) {
                handleMarkSingleRead(orderId);
            }
        }
    });

    // When dropdown toggle is clicked, refresh notifications
    const toggleBtn = document.getElementById('qrNotifDropdownToggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            fetchQrNotifications();
        });
    }

    // Initial fetch on page load
    fetchQrNotifications();

    // Poll every 15 seconds for new QR orders
    setInterval(fetchQrNotifications, 15000);
})();
</script>
