<!DOCTYPE html>
<html lang="en">
  <!-- [Head] start -->
  <head>
    <title>Restaurant - Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    @include('includes.style')
    <style>
      .logo-container {
        text-align: center;
        margin-bottom: 30px;
      }
      .logo-img {
        max-height: 80px;
        width: auto;
        object-fit: contain;
      }
      .auth-header {
        text-align: center;
        margin-bottom: 20px;
      }
      .forgot-link, .contact-link {
        cursor: pointer;
        transition: all 0.3s;
      }
      .forgot-link:hover, .contact-link:hover {
        transform: translateY(-2px);
    </style>
</head>
  
<body>
  
<div class="auth-main">
  <div class="auth-wrapper v3">
    <div class="auth-form">
      
      <!-- Logo Section -->
      <div class="logo-container">
        <img src="{{ asset('logo.png') }}" class="img-fluid logo-img" alt="Restaurant Logo">
      </div>
      
      <div class="auth-header">
        <a href="#"></a>
      </div>
      
      <div class="card my-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-end mb-4">
            <h3 class="mb-0"><b>Login</b></h3>
            <a href="#" class="link-primary">Welcome Back Admin !!</a>
          </div>
          
          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          @endif
          
          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          @endif
          
          <form action="{{ route('custom.login') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control" required placeholder="Enter your email address">
            </div>
            <div class="form-group mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required placeholder="Enter your password">
            </div>
            
            <div class="d-grid mt-3">
              <button type="submit" class="btn btn-primary btn-lg">Login</button>
            </div>
          </form>
          
          <div class="saprator mt-4">
            <span></span>
          </div>
          
          <div class="row mt-3">
            <div class="col-6">
              <div class="d-grid">
                <a href="{{route('forget.password.portal')}}" class="btn mt-2 btn-light-primary bg-light text-muted forgot-link">
                  <i class="fas fa-key me-2"></i> Forgot Password
                </a>
              </div>
            </div>
            <div class="col-6">
              <div class="d-grid">
                <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted contact-link" data-toggle="modal" data-target="#contactModal">
                  <i class="fas fa-headset me-2"></i> Contact Admin
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="auth-footer row">
        <div class="col my-1 text-center">
          <p class="m-0 text-muted">&copy; {{ date('Y') }} Restaurant Management System. All rights reserved.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-key me-2"></i> Reset Password</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="modal-body">
          <p>Enter your email address and we'll send you a link to reset your password.</p>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="Enter your registered email">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Contact Admin Modal -->


@include('includes.script')

<script>
  $(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
      $('.alert').fadeOut('slow');
    }, 5000);
  });
</script>

</body>
</html>