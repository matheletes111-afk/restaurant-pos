<!DOCTYPE html>
<html lang="en">
<head>
  <title>BILL & BITE | Reset Password</title>
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
  </style>
</head>
<body>

<div class="auth-wrapper">
  <div class="auth-card">
    
    <!-- Logo Section -->
    <div class="logo-container">
      <img src="{{ asset('logo.png') }}" class="img-fluid logo-img" alt="Restaurant Logo">
    </div>
    
    <div class="auth-title">Reset Password</div>
    <div class="auth-subtitle">Set a secure new password for your account</div>
    
    @include('includes.message')
    
    <form action="{{ route('forget.password.portal.forget.password.enter.new.password') }}" method="POST">
      @csrf
      <input type="hidden" name="id" value="{{ @$data->id }}">
      
      <div class="form-group mb-3">
        <label class="form-label">New Password</label>
        <input type="password" name="password" class="form-control" required placeholder="Enter new password">
      </div>
      
      <div class="form-group mb-4">
        <label class="form-label">Verification Code (OTP)</label>
        <input type="text" name="email_vcode" class="form-control" required placeholder="Enter OTP code">
      </div>
      
      <button type="submit" class="btn btn-login">Reset Password</button>
    </form>
    
    <div class="auth-actions-row">
      <a href="{{ route('login') }}" class="auth-action-link">
        <i class="fas fa-arrow-left"></i> Back to Login
      </a>
      <button type="button" class="auth-action-link" data-bs-toggle="modal" data-bs-target="#contactModal">
        <i class="fas fa-headset"></i> Contact Admin
      </button>
    </div>
  </div>

  <div class="auth-footer">
    <p class="m-0">&copy; {{ date('Y') }} BILL & BITE. All rights reserved.</p>
  </div>
</div>

<!-- Contact Admin Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-headset text-success me-2"></i> Contact Admin</h5>
        <button type="button" class="btn-close-custom" data-dismiss="modal"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body text-center py-4">
        <i class="fas fa-headset text-success fa-3x mb-3" style="opacity: 0.9;"></i>
        <h5 class="font-weight-bold mb-2">Need Assistance?</h5>
        <p class="text-muted mb-4">Please contact the system administrator for account creation, support, or billing queries.</p>
        <div class="d-flex flex-column gap-3 align-items-center justify-content-center">
          <div class="d-inline-flex align-items-center gap-2">
            <i class="fas fa-envelope text-secondary"></i>
            <span class="font-weight-bold text-dark">admin@restaurantpos.com</span>
          </div>
          <div class="d-inline-flex align-items-center gap-2">
            <i class="fas fa-phone-alt text-secondary"></i>
            <span class="font-weight-bold text-dark">+1 (555) 019-2834</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@include('includes.script')
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
