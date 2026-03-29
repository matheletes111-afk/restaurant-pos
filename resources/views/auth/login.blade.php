<!DOCTYPE html>
<html lang="en">
  <!-- [Head] start -->
  <head>
    <title>Restaurant</title>
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
              <h3 class="mb-0"><b>Login</b></h3>
              <a href="#" class="link-primary">Welcome Back Admin !!</a>
            </div>
            <form action="{{route('custom.login')}}" method="POST">
            @csrf
            <div class="form-group mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control" required placeholder="Email Address">
            </div>
            <div class="form-group mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required placeholder="Password">
            </div>
            <div class="d-flex mt-1 justify-content-between">
              <div class="form-check">
               
                <label class="form-check-label text-muted" for="customCheckc1"></label>
              </div>
              <h5 class="text-secondary f-w-400"></h5>
            </div>
            <div class="d-grid ">
              <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
            <div class="saprator mt-3">
              <span></span>
            </div>
            <div class="row">
              <div class="col-6">
                <div class="d-grid">
                  <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    Forget Password
                  </button>
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
          <!-- <div class=""> -->
            <div class="col my-1">
              <p class="m-0">Restaurant</a></p>
            </div>
            
          <!-- </div> -->
        </div>
      </div>
    </div>
  </div>
    @include('includes.script')
  </body>
  <!-- [Body] end -->
</html>
