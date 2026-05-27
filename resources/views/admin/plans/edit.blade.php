<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Edit Plan</title>
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
                <h5 class="m-b-10">Edit Plan</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('plans.index') }}">Plans</a></li>
                <li class="breadcrumb-item" aria-current="page">Edit</li>
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
              <h5>Edit Plan Information</h5>
            </div>
            <div class="card-body">
              @include('includes.message')
              
              <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> The price you enter should be <strong>INCLUDING GST (18%)</strong>. 
                The taxable amount and GST amount will be calculated automatically.
              </div>

              <div class="alert alert-warning mb-4">
                <i class="fa fa-exclamation-triangle me-2"></i>
                <strong>Versioning Notice:</strong> Updating a plan will create a new version. 
                Existing subscriptions will continue with the old plan until renewal.
              </div>
              
              <form method="POST" action="{{ route('plans.update', $plan->id) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Plan Name *</label>
                      <input type="text" class="form-control" id="name" name="name" 
                             value="{{ old('name', $plan->name) }}" required>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="price">Price (Including GST 18%) *</label>
                      <input type="number" class="form-control" id="price" name="price" 
                             step="0.01" min="0" value="{{ old('price', $plan->price) }}" required>
                      <small class="text-muted">Enter the final price customer pays (GST 18% included)</small>
                    </div>
                  </div>
                </div>

                <!-- GST Breakdown Display -->
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>GST Percentage</label>
                      <input type="text" class="form-control" id="gst_percentage_display" 
                             value="18%" readonly disabled style="background-color: #f8f9fa;">
                      <input type="hidden" name="gst_percentage" id="gst_percentage" value="18">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Taxable Amount (Excluding GST)</label>
                      <input type="text" class="form-control" id="taxable_amount_display" 
                             readonly disabled style="background-color: #e8f5e9; color: #2e7d32; font-weight: bold;">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>GST Amount (18%)</label>
                      <input type="text" class="form-control" id="gst_amount_display" 
                             readonly disabled style="background-color: #fff3e0; color: #e65100; font-weight: bold;">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="country_id">Country ID</label>
                      <input type="number" class="form-control" id="country_id" name="country_id" 
                             value="{{ old('country_id', $plan->country_id) }}">
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="currency">Currency</label>
                      <select class="form-control" id="currency" name="currency">
                        <option value="INR" {{ old('currency', $plan->currency) == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                        <option value="USD" {{ old('currency', $plan->currency) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="EUR" {{ old('currency', $plan->currency) == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                        <option value="GBP" {{ old('currency', $plan->currency) == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="billing_cycle">Billing Cycle *</label>
                      <select class="form-control" id="billing_cycle" name="billing_cycle" required>
                        <option value="monthly" {{ old('billing_cycle', $plan->billing_cycle) == 'monthly' ? 'selected' : '' }}>Monthly (30 days)</option>
                        <option value="quarterly" {{ old('billing_cycle', $plan->billing_cycle) == 'quarterly' ? 'selected' : '' }}>Quarterly (90 days)</option>
                        <option value="half-yearly" {{ old('billing_cycle', $plan->billing_cycle) == 'half-yearly' ? 'selected' : '' }}>Half Yearly (180 days)</option>
                        <option value="yearly" {{ old('billing_cycle', $plan->billing_cycle) == 'yearly' ? 'selected' : '' }}>Yearly (365 days)</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="duration_days">Duration (Days) *</label>
                      <input type="number" class="form-control" id="duration_days" name="duration_days" 
                             min="1" value="{{ old('duration_days', $plan->duration_days) }}" required>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="is_default_free">Default Free Plan *</label>
                      <select class="form-control" id="is_default_free" name="is_default_free" required>
                        <option value="N" {{ old('is_default_free', $plan->is_default_free) == 'N' ? 'selected' : '' }}>No</option>
                        <option value="Y" {{ old('is_default_free', $plan->is_default_free) == 'Y' ? 'selected' : '' }}>Yes</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="is_default_paid">Default Paid Plan *</label>
                      <select class="form-control" id="is_default_paid" name="is_default_paid" required>
                        <option value="N" {{ old('is_default_paid', $plan->is_default_paid) == 'N' ? 'selected' : '' }}>No</option>
                        <option value="Y" {{ old('is_default_paid', $plan->is_default_paid) == 'Y' ? 'selected' : '' }}>Yes</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Default Plan Checkbox -->
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Set as Default Plan</label>
                      <div class="custom-control custom-checkbox mt-2">
                        <input type="checkbox" class="custom-control-input" id="is_default_plan"
                               {{ old('is_default_plan', $plan->is_default_plan) == 'Y' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_default_plan">
                          Set as Default Plan (This will be the recommended plan for new restaurants)
                        </label>
                      </div>
                      <input type="hidden" name="is_default_plan" id="is_default_plan_value" 
                             value="{{ old('is_default_plan', $plan->is_default_plan ?? 'N') }}">
                      <small class="text-muted">Only one default plan can exist. Existing default will be replaced.</small>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" id="description" name="description" rows="3" 
                            placeholder="Describe what this plan includes...">{{ old('description', $plan->description) }}</textarea>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="category_number">Maximum Categories</label>
                      <input type="number" class="form-control" id="category_number" name="category_number"
                             min="0" value="{{ old('category_number', $plan->category_number ?? 0) }}">
                      <small class="text-muted">0 = Unlimited</small>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="total_number_of_dishes">Maximum Dishes</label>
                      <input type="number" class="form-control" id="total_number_of_dishes" name="total_number_of_dishes"
                             min="0" value="{{ old('total_number_of_dishes', $plan->total_number_of_dishes ?? 0) }}">
                      <small class="text-muted">0 = Unlimited</small>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="total_number_of_table">Maximum Tables</label>
                      <input type="number" class="form-control" id="total_number_of_table" name="total_number_of_table"
                             min="0" value="{{ old('total_number_of_table', $plan->total_number_of_table ?? 0) }}">
                      <small class="text-muted">0 = Unlimited</small>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="inventory_checkbox">Inventory Management</label>
                      <select class="form-control" id="inventory_checkbox" name="inventory_checkbox">
                        <option value="N" {{ old('inventory_checkbox', $plan->inventory_checkbox ?? 'N') == 'N' ? 'selected' : '' }}>Disabled</option>
                        <option value="Y" {{ old('inventory_checkbox', $plan->inventory_checkbox ?? 'N') == 'Y' ? 'selected' : '' }}>Enabled</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Update Plan</button>
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
    $(document).ready(function () {

      // Calculate GST breakdown when price changes
      function calculateGST() {
        var price = parseFloat($('#price').val()) || 0;
        var gstPercentage = 18; // Fixed GST rate
        var taxableAmount = price / (1 + (gstPercentage / 100));
        var gstAmount = price - taxableAmount;
        
        // Format to 2 decimal places
        taxableAmount = taxableAmount.toFixed(2);
        gstAmount = gstAmount.toFixed(2);
        
        // Update display fields
        $('#taxable_amount_display').val('₹' + taxableAmount);
        $('#gst_amount_display').val('₹' + gstAmount);
        
        // Store in hidden fields (if needed for form submission)
        if ($('#taxable_amount_hidden').length === 0) {
          $('<input>').attr({
            type: 'hidden',
            name: 'taxable_amount',
            id: 'taxable_amount_hidden',
            value: taxableAmount
          }).appendTo('form');
          
          $('<input>').attr({
            type: 'hidden',
            name: 'gst_amount',
            id: 'gst_amount_hidden',
            value: gstAmount
          }).appendTo('form');
        } else {
          $('#taxable_amount_hidden').val(taxableAmount);
          $('#gst_amount_hidden').val(gstAmount);
        }
        
        // Show warning if price is less than minimum
        if (price > 0 && price < 1.18) {
          $('#price').addClass('is-invalid');
          $('#price_error').remove();
          $('#price').after('<small id="price_error" class="text-danger">Price seems too low. Minimum price should be at least ₹1.18 for 18% GST.</small>');
        } else {
          $('#price').removeClass('is-invalid');
          $('#price_error').remove();
        }
      }
      
      // Trigger calculation on price change
      $('#price').on('input change', function() {
        calculateGST();
      });
      
      // Initial calculation on page load
      calculateGST();

      // Auto set duration based on billing cycle
      $('#billing_cycle').on('change', function () {
        var cycle = $(this).val();
        var days = 30;

        switch (cycle) {
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

      // Set initial value on page load for default plan checkbox
      if ($('#is_default_plan').is(':checked')) {
        $('#is_default_plan_value').val('Y');
      } else {
        $('#is_default_plan_value').val('N');
      }

      // Change value on checkbox toggle
      $('#is_default_plan').on('change', function () {
        if ($(this).is(':checked')) {
          $('#is_default_plan_value').val('Y');
        } else {
          $('#is_default_plan_value').val('N');
        }
      });

      // Trigger initial price calculation
      $('#price').trigger('input');
      
    });
  </script>

  <style>
    .form-control:disabled, .form-control[readonly] {
      background-color: #f8f9fa;
      cursor: not-allowed;
    }
    #taxable_amount_display {
      font-weight: bold;
      color: #2e7d32;
    }
    #gst_amount_display {
      font-weight: bold;
      color: #e65100;
    }
    .alert-info {
      border-left: 4px solid #17a2b8;
    }
    .alert-warning {
      border-left: 4px solid #ffc107;
    }
  </style>

</body>
</html>