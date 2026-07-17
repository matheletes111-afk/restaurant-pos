<style>
    .toast-notification-container {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 999999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 380px;
        width: 100%;
        pointer-events: none;
    }
    .toast-notification {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: flex-start;
        gap: 12px;
        pointer-events: auto;
        transform: translateX(120%);
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
        opacity: 0;
        border-left: 4px solid #ff6a00;
        margin-bottom: 2px;
    }
    .toast-notification.show {
        transform: translateX(0);
        opacity: 1;
    }
    .toast-notification.success {
        border-left-color: #10b981;
    }
    .toast-notification.error {
        border-left-color: #ef4444;
    }
    .toast-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: -2px;
    }
    .toast-notification.success .toast-icon-box {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    .toast-notification.error .toast-icon-box {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
    .toast-content {
        flex-grow: 1;
    }
    .toast-title {
        font-weight: 700;
        font-size: 0.88rem;
        margin: 0 0 3px 0;
        color: #1f2937;
        font-family: 'Outfit', 'Inter', sans-serif;
    }
    .toast-message {
        font-size: 0.8rem;
        color: #4b5563;
        margin: 0;
        line-height: 1.45;
        font-family: 'Outfit', 'Inter', sans-serif;
    }
    .toast-close-btn {
        background: transparent;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease;
        flex-shrink: 0;
        margin-top: -2px;
    }
    .toast-close-btn:hover {
        color: #4b5563;
    }
</style>

<div class="toast-notification-container" id="toastContainer">
    <!-- Success Toast -->
    @if(Session::has('success'))
        <div class="toast-notification success" role="alert">
            <div class="toast-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="toast-content">
                <h6 class="toast-title">Success</h6>
                <p class="toast-message">{!! Session::get('success') !!}</p>
            </div>
            <button class="toast-close-btn" onclick="dismissToast(this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Error Toast -->
    @if(Session::has('error'))
        <div class="toast-notification error" role="alert">
            <div class="toast-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="toast-content">
                <h6 class="toast-title">Error</h6>
                <p class="toast-message">{!! Session::get('error') !!}</p>
            </div>
            <button class="toast-close-btn" onclick="dismissToast(this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Validation Errors Toasts -->
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="toast-notification error" role="alert">
                <div class="toast-icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="toast-content">
                    <h6 class="toast-title">Validation Error</h6>
                    <p class="toast-message">{!! $error !!}</p>
                </div>
                <button class="toast-close-btn" onclick="dismissToast(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endforeach
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toasts = document.querySelectorAll('.toast-notification');
        toasts.forEach((toast, index) => {
            // Stagger toast entries if there are multiple
            setTimeout(() => {
                toast.classList.add('show');
            }, index * 200 + 50);

            // Auto dismiss after 5 seconds
            setTimeout(() => {
                dismissToast(toast);
            }, 5000 + (index * 200));
        });
    });

    function dismissToast(element) {
        // Handle close button click vs auto dismiss call
        const toast = element.classList.contains('toast-notification') ? element : element.closest('.toast-notification');
        if (!toast) return;
        
        toast.classList.remove('show');
        // Remove from DOM after transition
        setTimeout(() => {
            toast.remove();
        }, 400);
    }
</script>


