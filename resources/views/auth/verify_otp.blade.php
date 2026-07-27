<!DOCTYPE html>
<html lang="en">
<head>
  <title>Restaurant - Two-Factor Authentication</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  @include('includes.style')
  <style>
    body {
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Public Sans', 'Segoe UI', sans-serif;
    }

    .auth-wrapper {
      width: 100%;
      max-width: 450px;
      padding: 20px;
    }

    .auth-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.6);
      border-radius: 24px;
      box-shadow: 0 20px 45px rgba(15, 23, 42, 0.06);
      overflow: hidden;
      position: relative;
      padding: 40px 30px;
    }

    .auth-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, #009d1a 0%, #00bc20 100%);
    }

    .logo-container {
      text-align: center;
      margin-bottom: 25px;
    }

    .logo-img {
      max-height: 70px;
      width: auto;
      object-fit: contain;
      filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.03));
    }

    .auth-title {
      font-weight: 800;
      color: #0f172a;
      font-size: 1.5rem;
      text-align: center;
      margin-bottom: 6px;
    }

    .auth-subtitle {
      color: #64748b;
      font-size: 0.85rem;
      text-align: center;
      margin-bottom: 30px;
    }

    .form-group label {
      font-weight: 700;
      color: #475569;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 8px;
      display: inline-block;
    }

    .form-group .form-control {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 12px 16px;
      font-size: 1.1rem;
      font-weight: 700;
      color: #0f172a;
      transition: all 0.2s ease;
      letter-spacing: 0.2em;
      text-align: center;
    }

    .form-group .form-control:focus {
      background: #ffffff;
      border-color: #ff6b00;
      box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.15), 0 4px 12px rgba(255, 107, 0, 0.08);
      outline: none;
    }

    .btn-verify {
      background: linear-gradient(135deg, #ff6b00 0%, #ff8800 100%);
      border: none;
      color: white;
      border-radius: 30px;
      padding: 12px 30px;
      font-weight: 700;
      font-size: 0.95rem;
      box-shadow: 0 4px 15px rgba(255, 107, 0, 0.25);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      width: 100%;
    }

    .btn-verify:hover {
      background: linear-gradient(135deg, #ff8800 0%, #ff6b00 100%);
      transform: translateY(-1px);
      box-shadow: 0 8px 25px rgba(255, 107, 0, 0.4);
    }

    .auth-actions-row {
      display: flex;
      justify-content: space-between;
      margin-top: 25px;
      border-top: 1px solid #f1f5f9;
      padding-top: 20px;
      font-size: 0.85rem;
    }

    .auth-action-link {
      color: #64748b;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: transparent;
      border: none;
      cursor: pointer;
    }

    .auth-action-link:hover {
      color: #ff6b00;
    }

    .auth-footer {
      text-align: center;
      margin-top: 20px;
      font-size: 0.8rem;
      color: #94a3b8;
    }

    #loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(15, 23, 42, 0.7);
      backdrop-filter: blur(8px);
      display: none;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      z-index: 9999;
      color: white;
    }

    .spinner {
      width: 50px;
      height: 50px;
      border: 5px solid rgba(255, 255, 255, 0.2);
      border-top-color: #00bc20;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 20px;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .loading-text {
      font-weight: 700;
      font-size: 1.1rem;
      letter-spacing: 0.05em;
      color: #ffffff;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>
<body>

<div id="loading-overlay">
  <div class="spinner"></div>
  <div class="loading-text" id="overlay-text">Verifying code... Please wait.</div>
</div>

<div class="auth-wrapper">
  <div class="auth-card">
    
    <!-- Logo Section -->
    <div class="logo-container">
      <img src="{{ asset('logo.png') }}" class="img-fluid logo-img" alt="Restaurant Logo">
    </div>
    
    <div class="auth-title">Two-Factor Auth</div>
    <div class="auth-subtitle">We have sent a 6-digit verification code to your email.</div>
    
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.75rem; padding: 1.1rem;"></button>
      </div>
    @endif
    
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.75rem; padding: 1.1rem;"></button>
      </div>
    @endif
    
    <form action="{{ route('login.verify') }}" id="otp-form" method="POST">
      @csrf
      <div class="form-group mb-4">
        <label class="form-label d-block text-center">Verification Code</label>
        <input type="text" name="otp" class="form-control" required placeholder="123456" maxlength="6" autofocus autocomplete="off">
      </div>
      
      <button type="submit" class="btn btn-verify">Verify & Authenticate</button>
    </form>
    
    <div class="auth-actions-row">
      <a href="{{ route('login.verify.resend') }}" id="resend-link" class="auth-action-link">
        <i class="fas fa-redo"></i> Resend Code
      </a>
      <a href="{{ route('login') }}" class="auth-action-link">
        <i class="fas fa-arrow-left"></i> Back to Login
      </a>
    </div>
  </div>

  <div class="auth-footer">
    <p class="m-0">&copy; {{ date('Y') }} Restaurant Management System. All rights reserved.</p>
  </div>
</div>

@include('includes.script')

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
  $(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
      $('.alert').fadeOut('slow');
    }, 5000);

    // Limit OTP input to numbers only
    $('input[name="otp"]').on('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Show loading overlay on verify form submit
    $('#otp-form').on('submit', function() {
      $('#overlay-text').text('Verifying code... Please wait.');
      $('#loading-overlay').css('display', 'flex');
    });

    // Show loading overlay on resend link click
    $('#resend-link').on('click', function() {
      $('#overlay-text').text('Resending verification code... Please wait.');
      $('#loading-overlay').css('display', 'flex');
    });
  });
</script>

</body>
</html>
