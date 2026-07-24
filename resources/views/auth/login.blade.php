<!DOCTYPE html>
<html lang="en">
<head>
  <title>Restaurant - Login</title>
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
      font-size: 0.9rem;
      color: #0f172a;
      transition: all 0.2s ease;
    }

    .form-group .form-control:focus {
      background: #ffffff;
      border-color: #009d1a;
      box-shadow: 0 0 0 3px rgba(0, 157, 26, 0.1), 0 4px 12px rgba(0, 157, 26, 0.05);
      outline: none;
    }

    .btn-login {
      background: linear-gradient(135deg, #009d1a 0%, #00bc20 100%);
      border: none;
      color: white;
      border-radius: 30px;
      padding: 12px 30px;
      font-weight: 700;
      font-size: 0.95rem;
      box-shadow: 0 4px 15px rgba(0, 157, 26, 0.2);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      width: 100%;
    }

    .btn-login:hover {
      background: linear-gradient(135deg, #00bc20 0%, #009d1a 100%);
      transform: translateY(-1px);
      box-shadow: 0 8px 25px rgba(0, 157, 26, 0.35);
    }

    .auth-actions-row {
      display: flex;
      justify-content: space-between;
      margin-top: 25px;
      border-top: 1px solid #f1f5f9;
      padding-top: 20px;
    }

    .auth-action-link {
      color: #64748b;
      font-size: 0.8rem;
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
      color: #009d1a;
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
  <div class="loading-text">Sending verification code... Please wait.</div>
</div>

<div class="auth-wrapper">
  <div class="auth-card">
    
    <!-- Logo Section -->
    <div class="logo-container">
      <img src="{{ asset('logo.png') }}" class="img-fluid logo-img" alt="Restaurant Logo">
    </div>
    
    <div class="auth-title">Welcome Back</div>
    <div class="auth-subtitle">Sign in to manage your restaurant operations</div>
    
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
    
    <form action="{{ route('custom.login') }}" id="login-form" method="POST">
      @csrf
      <div class="form-group mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" required placeholder="name@restaurant.com">
      </div>
      
      <div class="form-group mb-4">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required placeholder="••••••••">
      </div>
      
      <button type="submit" class="btn btn-login">Sign In</button>
    </form>
    
    <div class="auth-actions-row">
      <a href="{{ route('forget.password.portal') }}" class="auth-action-link">
        <i class="fas fa-key"></i> Forgot Password?
      </a>
      <button type="button" class="auth-action-link" data-bs-toggle="modal" data-bs-target="#contactModal">
        <i class="fas fa-headset"></i> Contact Admin
      </button>
    </div>
  </div>

  <div class="auth-footer">
    <p class="m-0">&copy; {{ date('Y') }} Restaurant Management System. All rights reserved.</p>
  </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="forgotModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-key text-success me-2"></i> Reset Password</h5>
        <button type="button" class="btn-close-custom" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
      </div>
      <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="modal-body">
          <p class="text-muted mb-4">Enter your email address and we'll send you a link to reset your password.</p>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="Enter registered email">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Send Reset Link</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Contact Admin Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-headset text-success me-2"></i> Contact Admin</h5>
        <button type="button" class="btn-close-custom" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body text-center py-4">
        <i class="fas fa-headset text-success fa-3x mb-3" style="opacity: 0.9;"></i>
        <h5 class="font-weight-bold mb-2">Need Assistance?</h5>
        <p class="text-muted mb-4">Please contact the system administrator for account creation, support, or billing queries.</p>
        <div class="d-flex flex-column gap-3 align-items-center justify-content-center">
          <div class="d-inline-flex align-items-center gap-2">
            <i class="fas fa-envelope text-secondary"></i>
            <span class="font-weight-bold text-dark">info@billnbite.com</span>
          </div>
          <div class="d-inline-flex align-items-center gap-2">
            <i class="fas fa-phone-alt text-secondary"></i>
            <span class="font-weight-bold text-dark">+91 7001769472</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
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

    // Show loading overlay on form submit
    $('#login-form').on('submit', function() {
      $('#loading-overlay').css('display', 'flex');
    });
  });
</script>

</body>
</html>