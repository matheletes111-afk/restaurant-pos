<script>
(function() {
    'use strict';

    // Store state
    let cachedNotifications = [];
    let knownNotifIds = new Set();
    let isFirstFetch = true;
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

    // Audio chime using Web Audio API
    function playNotificationChime() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const now = ctx.currentTime;

            // Note 1 (E5)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(659.25, now);
            gain1.gain.setValueAtTime(0.2, now);
            gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.3);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.3);

            // Note 2 (A5)
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880, now + 0.12);
            gain2.gain.setValueAtTime(0.25, now + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.12);
            osc2.stop(now + 0.5);
        } catch (e) {
            // Audio context blocked or unsupported
        }
    }

    // Show floating toast alert for newly arrived order
    function showOrderToast(ord) {
        const container = document.getElementById('qrOrderToastContainer');
        if (!container) return;

        const isAdditional = (ord.notif_type === 'additional_items');
        const titleText = isAdditional ? 'New Items Added' : 'New QR Order Placed';
        const msgText = isAdditional 
            ? `${escapeHtml(ord.customer_name || 'Customer')} added new item in ${escapeHtml(ord.table_name || 'Table')}`
            : `${escapeHtml(ord.customer_name || 'Customer')} placed a new order for ${escapeHtml(ord.table_name || 'Table')}`;
        const toastId = 'toast_' + ord.id + '_' + Date.now();

        const toastHtml = `
            <div id="${toastId}" class="qr-toast-card p-3 mb-2" role="alert" style="pointer-events: auto; cursor: pointer;" onclick="window.location.href='${ord.view_url}'">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255, 106, 0, 0.12); display: flex; align-items: center; justify-content: center; color: #ff6a00;">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.88rem;">${titleText}</h6>
                            <span class="badge bg-dark-subtle text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.68rem; font-weight: 700;">
                                <i class="fas fa-chair me-1"></i>${escapeHtml(ord.table_name)}
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" style="font-size: 0.65rem;" onclick="event.stopPropagation(); document.getElementById('${toastId}').remove();"></button>
                </div>
                <div class="mt-2 text-dark fw-semibold" style="font-size: 0.85rem;">
                    ${msgText}
                </div>
                ${!isAdditional && ord.items_summary ? `<div class="text-muted text-truncate mt-1" style="font-size: 0.74rem;">${escapeHtml(ord.items_summary)}</div>` : ''}
                <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top" style="font-size: 0.74rem;">
                    ${!isAdditional && ord.grand_total ? `<span class="fw-bold text-success">₹${escapeHtml(ord.grand_total)}</span>` : '<span class="text-muted"><i class="far fa-clock me-1"></i>Just now</span>'}
                    <span class="text-primary fw-bold">View Order <i class="fas fa-arrow-right ms-1"></i></span>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', toastHtml);

        // Auto remove toast after 7 seconds
        setTimeout(() => {
            const el = document.getElementById(toastId);
            if (el) {
                el.style.opacity = '0';
                el.style.transition = 'opacity 0.4s ease';
                setTimeout(() => el.remove(), 400);
            }
        }, 7000);
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
                    <p class="text-muted mb-0 fw-semibold" style="font-size: 0.85rem;">No new QR orders</p>
                    <small class="text-muted" style="font-size: 0.75rem;">New customer QR orders and table additions will appear here</small>
                </div>
            `;
            return;
        }

        let html = '';
        notifications.forEach(ord => {
            const isUnread = !ord.is_read;
            const isAdditional = (ord.notif_type === 'additional_items');
            let statusBadge = '';

            if (isAdditional) {
                statusBadge = '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">NEW ITEMS</span>';
            } else if (ord.order_status === 'PENDING') {
                statusBadge = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">PENDING</span>';
            } else if (ord.order_status === 'APPROVED') {
                statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">APPROVED</span>';
            }

            if (isAdditional) {
                html += `
                    <a
                        href="${ord.view_url}"
                        class="list-group-item list-group-item-action px-3 py-2.5 border-bottom qr-notif-item ${isUnread ? 'qr-item-unread' : ''}"
                        data-order-id="${ord.id}"
                        style="text-decoration: none; transition: background 0.2s; cursor: pointer;"
                    >
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center gap-1.5">
                                ${isUnread ? '<span class="qr-unread-dot me-1" title="Unread"></span>' : ''}
                                <span class="badge bg-dark-subtle text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
                                    <i class="fas fa-chair text-muted me-1"></i>${escapeHtml(ord.table_name)}
                                </span>
                                ${statusBadge}
                            </div>
                            <span class="text-primary fw-bold" style="font-size: 0.75rem;">
                                View <i class="fas fa-arrow-right ms-0.5"></i>
                            </span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between py-1">
                            <div class="fw-semibold text-dark text-truncate" style="font-size: 0.83rem;">
                                <i class="fas fa-plus-circle text-primary me-1"></i>${escapeHtml(ord.customer_name || 'Customer')} added new item in ${escapeHtml(ord.table_name || 'Table')}
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-1 text-muted" style="font-size: 0.7rem;">
                            <span><i class="far fa-clock me-1"></i>${escapeHtml(ord.created_at_human)}</span>
                            <span>${escapeHtml(ord.created_at_date)}, ${escapeHtml(ord.created_at_time)}</span>
                        </div>
                    </a>
                `;
            } else {
                html += `
                    <a
                        href="${ord.view_url}"
                        class="list-group-item list-group-item-action px-3 py-2.5 border-bottom qr-notif-item ${isUnread ? 'qr-item-unread' : ''}"
                        data-order-id="${ord.id}"
                        style="text-decoration: none; transition: background 0.2s; cursor: pointer;"
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
                            <div class="text-truncate me-2" style="max-width: 230px;">
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
            }
        });

        container.innerHTML = html;
    }
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

            const newItemsList = data.notifications || [];

            // Detect freshly arrived unread orders
            if (!isFirstFetch) {
                newItemsList.forEach(ord => {
                    if (!ord.is_read && !knownNotifIds.has(ord.id)) {
                        playNotificationChime();
                        showOrderToast(ord);
                    }
                });
            }

            // Update known IDs
            knownNotifIds = new Set(newItemsList.map(n => n.id));
            isFirstFetch = false;

            updateBadge(data.unread_count);
            renderNotifications(newItemsList);
        } catch (e) {
            // Silently ignore network disruption
        }
    }

    // Handler for Mark Single Read on Click
    function handleMarkSingleRead(orderId) {
        if (!orderId) return;

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

    // Initial fetch once on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fetchQrNotifications);
    } else {
        fetchQrNotifications();
    }

    // Periodic polling every 15 seconds for live real-time notifications
    setInterval(fetchQrNotifications, 15000);
})();
</script>
