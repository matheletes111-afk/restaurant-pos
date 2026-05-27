<!DOCTYPE html>
<html lang="en">
  <!-- [Head] start -->
  <head>
    <title>BILL & BITE | Forget Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    @include('includes.style')
  </head>
  <!-- [Head] end -->

  <body>

  <div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">
        <div class="auth-header">
          <a href="#"></a>
        </div>

        <!-- Forget Password Card -->
        <div class="card my-5">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-end mb-4">
              <h3 class="mb-0"><b>Forget Password</b></h3>
              <a href="#" class="link-primary">Reset your account password</a>
            </div>

            @include('includes.message')

            <form action="{{ route('forget.password.portal.forget.password.submit') }}" method="POST">
              @csrf
              <div class="form-group mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="Enter registered email">
              </div>

              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Send Reset Link</button>
              </div>
            </form>

            <div class="saprator mt-3">
              <span></span>
            </div>

            <div class="row">
              <div class="col-6">
                <div class="d-grid">
                  <a href="{{ route('login') }}" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <i class="fa fa-arrow-left"></i> Back to Login
                  </a>
                </div>
              </div>

              <div class="col-6">
                <div class="d-grid">
                  <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    Contact Admin
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="auth-footer row">
          <div class="col my-1">
            <p class="m-0">BILL & BITE</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('includes.script')
  </body>
  <!-- [Body] end -->
</html>
