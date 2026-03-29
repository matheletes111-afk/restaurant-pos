<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Create Plan</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
</head>

<body data-pc-theme="light">
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  @include('includes.sidebar')

  <div class="pc-container">
    <div class="pc-content">

      <!-- Breadcrumb -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h5 class="m-b-10">Create New Plan</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('plans.index') }}">Plans</a></li>
                <li class="breadcrumb-item" aria-current="page">Create</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- Breadcrumb end -->

      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">
              <h5>Plan Information</h5>
            </div>
            <div class="card-body">
              @include('includes.message')
              
              <form method="POST" action="{{ route('plans.store') }}">
                @csrf
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Plan Name *</label>
                      <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="price">Price *</label>
                      <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="{{ old('price') }}" required>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="country_id">Country ID</label>
                      <input type="number" class="form-control" id="country_id" name="country_id" value="{{ old('country_id') }}">
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="currency">Currency</label>
                      <select class="form-control" id="currency" name="currency">
                        <option value="INR" {{ old('currency', 'INR') == 'INR' ? 'selected' : '' }}>INR</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="billing_cycle">Billing Cycle *</label>
                      <select class="form-control" id="billing_cycle" name="billing_cycle" required>
                        <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('billing_cycle') == 'quarterly' ? 'selected' : '' }}>Quarterly (3 months)</option>
                        <option value="half-yearly" {{ old('billing_cycle') == 'half-yearly' ? 'selected' : '' }}>Half Yearly (6 months)</option>
                        <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="duration_days">Duration (Days) *</label>
                      <input type="number" class="form-control" id="duration_days" name="duration_days" min="1" value="{{ old('duration_days', 30) }}" required>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="is_default_free">Default Free Plan *</label>
                      <select class="form-control" id="is_default_free" name="is_default_free" required>
                        <option value="N" {{ old('is_default_free', 'N') == 'N' ? 'selected' : '' }}>No</option>
                        <option value="Y" {{ old('is_default_free') == 'Y' ? 'selected' : '' }}>Yes</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="is_default_paid">Default Paid Plan *</label>
                      <select class="form-control" id="is_default_paid" name="is_default_paid" required>
                        <option value="N" {{ old('is_default_paid', 'N') == 'N' ? 'selected' : '' }}>No</option>
                        <option value="Y" {{ old('is_default_paid') == 'Y' ? 'selected' : '' }}>Yes</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="category_number">Category Number</label>
                      <input type="number" class="form-control" id="category_number" name="category_number"
                             value="{{ old('category_number') }}" min="0">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="total_number_of_dishes">Total Number of Dishes</label>
                      <input type="number" class="form-control" id="total_number_of_dishes" name="total_number_of_dishes"
                             value="{{ old('total_number_of_dishes') }}" min="0">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="total_number_of_table">Total Number of Tables</label>
                      <input type="number" class="form-control" id="total_number_of_table" name="total_number_of_table"
                             value="{{ old('total_number_of_table') }}" min="0">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="inventory_checkbox">Inventory Enabled</label>
                      <select class="form-control" id="inventory_checkbox" name="inventory_checkbox">
                        <option value="N" {{ old('inventory_checkbox','N')=='N' ? 'selected' : '' }}>No</option>
                        <option value="Y" {{ old('inventory_checkbox')=='Y' ? 'selected' : '' }}>Yes</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Create Plan</button>
                  <a href="{{ route('plans.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  @include('includes.script')

  <script>
    $(document).ready(function() {
      // Auto set duration based on billing cycle
      $('#billing_cycle').on('change', function() {
        var cycle = $(this).val();
        var days = 30;
        
        switch(cycle) {
          case 'monthly':
            days = 30;
            break;
          case 'quarterly':
            days = 90;
            break;
          case 'half-yearly':
            days = 180;
            break;
          case 'yearly':
            days = 365;
            break;
        }
        
        $('#duration_days').val(days);
      });
    });
  </script>

</body>
</html>