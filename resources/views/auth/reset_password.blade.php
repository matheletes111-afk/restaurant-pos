<!DOCTYPE html>
<html lang="en">
<head>
  <title>BILL & BITE | Reset Password</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  @include('includes.style')
</head>

<body>
  <div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">
        <div class="auth-header">
          <a href="#"></a>
        </div>

        <div class="card my-5">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-end mb-4">
              <h3 class="mb-0"><b>Reset Password</b></h3>
              <a href="#" class="link-primary">Set a New Password</a>
            </div>

            @include('includes.message')

            <form action="{{ route('forget.password.portal.forget.password.enter.new.password') }}" method="POST">
              @csrf
              <input type="hidden" name="id" value="{{ @$data->id }}">

              <div class="form-group mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Enter your new password">
              </div>

              <div class="form-group mb-3">
                <label class="form-label">Code</label>
                <input type="text" name="email_vcode" class="form-control" required placeholder="Enter Otp">
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Reset Password</button>
              </div>
            </form>

            <div class="saprator mt-3">
              <span></span>
            </div>

            <div class="row">
              <div class="col-6">
                <div class="d-grid">
                  <a href="{{ route('login') }}" class="btn mt-2 btn-light-primary bg-light text-muted">
                    Back to Login
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
</html>
