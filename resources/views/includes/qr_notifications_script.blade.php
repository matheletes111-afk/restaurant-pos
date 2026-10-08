<script>
(function() {
    'use strict';

    // Store state
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
                    <p class="text-muted mb-0 fw-semibold" style="font-size: 0.85rem;">No pending QR orders</p>
                    <small class="text-muted" style="font-size: 0.75rem;">New customer QR orders will appear here</small>
                </div>
            `;
            return;
        }

        let html = '';
        notifications.forEach(ord => {
            const isUnread = !ord.is_read;
            let statusBadge = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">PENDING</span>';

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

    // Fetch Notifications from Server (Runs on page load and manual click)
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
            renderNotifications(data.notifications);
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

    // Initial fetch once on page load/reload only (No 15-second background polling)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fetchQrNotifications);
    } else {
        fetchQrNotifications();
    }
})();
</script>
