<link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{asset('admin_template/fonts/fontawesome.css')}}">
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{asset('admin_template/fonts/material.css')}}" >
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{asset('admin_template/css/style.css')}}" id="main-style-link" >
<link rel="stylesheet" href="{{asset('admin_template/css/style-preset.css')}}">

<style>
    /* Global Premium Modal Styling */
    .modal {
        backdrop-filter: blur(6px);
        background: rgba(15, 23, 42, 0.15);
    }
    .modal-content {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12) !important;
        overflow: hidden;
        position: relative;
    }
    .modal-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff6a00 0%, #ff8c42 100%);
        z-index: 10;
    }
    .modal-header {
        background: #f8fafc;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
        padding: 1.5rem 1.75rem !important;
    }
    .modal-header h5, .modal-header h4, .modal-header .modal-title {
        font-weight: 700 !important;
        color: #0f172a !important;
        margin-bottom: 0;
        font-size: 1.2rem !important;
        display: flex;
        align-items: center;
    }
    /* Style bootstrap's default close button as a fallback if custom is not used */
    .modal-header .btn-close {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 50%;
        padding: 0.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0.7;
    }
    .modal-header .btn-close:hover {
        background-color: rgba(239, 68, 68, 0.1);
        transform: rotate(90deg);
        opacity: 1;
    }
    /* Style our custom close button */
    .btn-close-custom {
        background: #e2e8f0;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        font-size: 0.85rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .btn-close-custom:hover {
        background: #ef4444;
        color: #ffffff;
        transform: rotate(90deg);
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
    }
    .modal-body {
        padding: 2rem 1.75rem !important;
    }
    .modal-body label {
        font-weight: 700 !important;
        color: #475569 !important;
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        margin-bottom: 0.5rem !important;
        display: inline-block;
    }
    .modal-body .form-control, .modal-body select, .modal-body textarea {
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 0.7rem 1rem !important;
        font-size: 0.9rem !important;
        color: #1e293b !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .modal-body .form-control:focus, .modal-body select:focus, .modal-body textarea:focus {
        background-color: #ffffff !important;
        border-color: #ff6a00 !important;
        box-shadow: 0 0 0 3px rgba(255, 106, 0, 0.15), 0 4px 12px rgba(255, 106, 0, 0.05) !important;
        outline: none;
    }
    .modal-footer {
        background: #f8fafc;
        border-top: 1px solid rgba(0, 0, 0, 0.05) !important;
        padding: 1.25rem 1.75rem !important;
    }
    .modal-footer .btn-secondary {
        background-color: #e2e8f0 !important;
        border: none !important;
        color: #475569 !important;
        border-radius: 30px !important;
        padding: 0.7rem 1.75rem !important;
        font-weight: 600 !important;
        transition: all 0.25s ease !important;
    }
    .modal-footer .btn-secondary:hover {
        background-color: #cbd5e1 !important;
        color: #1e293b !important;
    }
    .modal-footer .btn-success {
        background: linear-gradient(135deg, #ff6a00 0%, #ff8c42 100%) !important;
        border: none !important;
        border-radius: 30px !important;
        padding: 0.7rem 2.25rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.02em;
        box-shadow: 0 4px 15px rgba(255, 106, 0, 0.2) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .modal-footer .btn-success:hover {
        background: linear-gradient(135deg, #ff8c42 0%, #ff6a00 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(255, 106, 0, 0.3) !important;
    }

    /* Global btn-success Overrides to Brand Orange */
    .btn-success {
        background: linear-gradient(135deg, #ff6a00 0%, #ff8c42 100%) !important;
        border: none !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(255, 106, 0, 0.15) !important;
        transition: all 0.25s ease-in-out !important;
    }
    .btn-success:hover, .btn-success:focus, .btn-success:active {
        background: linear-gradient(135deg, #ff8c42 0%, #ff6a00 100%) !important;
        border: none !important;
        color: white !important;
        box-shadow: 0 6px 18px rgba(255, 106, 0, 0.25) !important;
        transform: translateY(-1px) !important;
    }
    .btn-success:disabled {
        background: #cbd5e1 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        transform: none !important;
        color: #94a3b8 !important;
    }

    .btn-outline-success {
        border-color: #ff6a00 !important;
        color: #ff6a00 !important;
        background-color: transparent !important;
        transition: all 0.25s ease-in-out !important;
    }
    .btn-outline-success:hover, .btn-outline-success:focus, .btn-outline-success:active {
        background-color: #ff6a00 !important;
        color: white !important;
        border-color: #ff6a00 !important;
    }

    /* Global btn-primary Overrides to Brand Orange */
    .btn-primary {
        background: linear-gradient(135deg, #ff6a00 0%, #ff8c42 100%) !important;
        border: none !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(255, 106, 0, 0.15) !important;
        transition: all 0.25s ease-in-out !important;
    }
    .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
        background: linear-gradient(135deg, #ff8c42 0%, #ff6a00 100%) !important;
        border: none !important;
        color: white !important;
        box-shadow: 0 6px 18px rgba(255, 106, 0, 0.25) !important;
        transform: translateY(-1px) !important;
    }
    .btn-primary:disabled {
        background: #cbd5e1 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        transform: none !important;
        color: #94a3b8 !important;
    }

    .btn-outline-primary {
        border-color: #ff6a00 !important;
        color: #ff6a00 !important;
        background-color: transparent !important;
        transition: all 0.25s ease-in-out !important;
    }
    .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
        background-color: #ff6a00 !important;
        color: white !important;
        border-color: #ff6a00 !important;
    }
</style>
