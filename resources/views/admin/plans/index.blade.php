<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Subscription Plans || Bill&Bite</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  
  <style>
    :root {
      --plan-primary: #ff5e14;
      --plan-primary-hover: #ea580c;
      --plan-primary-light: rgba(255, 94, 20, 0.08);
      --plan-primary-gradient: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
      --plan-dark: #0f172a;
      --plan-slate: #1e293b;
      --plan-muted: #64748b;
      --plan-border: #e2e8f0;
      --plan-card-bg: #ffffff;
    }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Switch toggle styling */
    .switch {
      position: relative;
      display: inline-block;
      width: 42px;
      height: 22px;
      vertical-align: middle;
      margin-bottom: 0;
    }

    .switch input { 
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #cbd5e1;
      transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 16px;
      width: 16px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
      border-radius: 50%;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    input:checked + .slider {
      background: var(--plan-primary-gradient);
    }

    input:checked + .slider:before {
      transform: translateX(20px);
    }

    .plan-row {
      cursor: grab;
      transition: background-color 0.15s ease;
    }
    .plan-row:active {
      cursor: grabbing;
    }
    .ui-state-highlight {
      height: 52px;
      background-color: rgba(255, 94, 20, 0.06) !important;
      border: 2px dashed #ff5e14 !important;
    }

    /* Floating Toast Alerts */
    .plan-toast-container {
      position: fixed;
      bottom: 28px;
      right: 28px;
      z-index: 99999;
      display: flex;
      flex-direction: column;
      gap: 10px;
      pointer-events: none;
    }

    .plan-toast {
      background: #0f172a;
      color: #ffffff;
      padding: 14px 20px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 0.88rem;
      font-weight: 600;
      pointer-events: auto;
      border-left: 4px solid var(--plan-primary);
      min-width: 280px;
      max-width: 420px;
    }

    .plan-toast.success {
      border-left-color: #10b981;
    }

    .plan-toast.error {
      border-left-color: #ef4444;
    }
  </style>
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
      <div class="page-header mb-3">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <ul class="breadcrumb mb-2" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Manage Subscription Plans</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- Breadcrumb end -->

      <!-- Header Banner Deck -->
      <div class="card mb-4" style="border-radius: 18px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04); overflow: hidden; position: relative;">
        <div style="position: absolute; top: 0; left: 0; width: 6px; height: 100%; background: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);"></div>
        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; border-radius: 14px; background: rgba(255, 94, 20, 0.1); color: #ff5e14; display: flex; align-items: center; justify-content: center; font-size: 1.45rem; border: 1px solid rgba(255, 94, 20, 0.2);">
              <i class="fas fa-layer-group"></i>
            </div>
            <div>
              <h4 class="mb-1 fw-bold" style="font-family: 'Outfit', sans-serif; color: #0f172a;">Subscription Plans Master</h4>
              <p class="text-muted mb-0 small">Create pricing plans, toggle active availability, set default plans, and drag to reorder display hierarchy.</p>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <a href="{{ route('plans.create') }}" class="btn text-white fw-bold px-4 py-2" style="background: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%); border-radius: 30px; box-shadow: 0 6px 18px rgba(255, 94, 20, 0.25);">
              <i class="fas fa-plus me-1"></i> Add New Plan
            </a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04); overflow: hidden;">
            @include('includes.message')
            
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2 py-3 px-4 border-bottom">
              <ul class="nav nav-pills" id="planTimeframeTabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active timeframe-filter-btn" data-filter="all" href="javascript:void(0)" style="border-radius: 20px; padding: 7px 18px; font-size: 0.85rem; font-weight: 700;">
                    <i class="fas fa-layer-group me-1"></i> All Plans ({{ $plans->count() }})
                  </a>
                </li>
                <li class="nav-item ms-1">
                  <a class="nav-link timeframe-filter-btn" data-filter="yearly" href="javascript:void(0)" style="border-radius: 20px; padding: 7px 18px; font-size: 0.85rem; font-weight: 700;">
                    <i class="fas fa-calendar-check me-1"></i> Yearly ({{ $plans->where('billing_cycle', 'yearly')->count() }})
                  </a>
                </li>
                <li class="nav-item ms-1">
                  <a class="nav-link timeframe-filter-btn" data-filter="monthly" href="javascript:void(0)" style="border-radius: 20px; padding: 7px 18px; font-size: 0.85rem; font-weight: 700;">
                    <i class="fas fa-calendar-alt me-1"></i> Monthly ({{ $plans->where('billing_cycle', 'monthly')->count() }})
                  </a>
                </li>
              </ul>

              <div class="text-muted small">
                <i class="fas fa-arrows-alt me-1 text-primary"></i> <span class="d-none d-sm-inline">Drag rows to reorder plans</span>
              </div>
            </div>

            <div class="card-body p-0">
              <div class="dt-responsive table-responsive">
                <table id="planTable" class="table table-hover align-middle mb-0 nowrap">
                  <thead class="bg-light">
                    <tr>
                      <th style="width: 50px;">Drag</th>
                      <th># ID</th>
                      <th>Plan Name</th>
                      <th>Price</th>
                      <th>Billing Cycle</th>
                      <th>Duration</th>
                      <th>Multi-Outlet</th>
                      <th>Default Plan</th>
                      <th>Active Plan (Toggle)</th>
                      <th>Razorpay ID</th>
                      <th style="text-align: right;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($plans as $plan)
                    @php
                      $isDefault = $plan->is_default_plan == 'Y';
                      $isActive = $plan->plan_status == 'A';
                      $rankClass = 'rank-2-row';
                      if ($isDefault && $isActive) {
                          $rankClass = 'rank-0-row';
                      } elseif (!$isDefault && $isActive) {
                          $rankClass = 'rank-1-row';
                      }
                      $timeframe = strtolower($plan->billing_cycle ?? 'monthly');
                    @endphp
                    <tr class="plan-row {{ $rankClass }}" data-id="{{ $plan->id }}" data-default="{{ $plan->is_default_plan }}" data-timeframe="{{ $timeframe }}">
                      <td class="text-center text-muted" style="cursor: grab;">
                        <i class="fas fa-grip-vertical"></i>
                      </td>
                      <td>
                        <span class="text-muted fw-bold">#{{ $plan->id }}</span>
                      </td>
                      <td>
                        <strong class="text-dark" style="font-size: 0.94rem;">{{ $plan->name }}</strong>
                        @if($plan->label_name)
                          <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">{{ $plan->label_name }}</span>
                        @endif
                      </td>
                      <td>
                        @if($plan->price == 0)
                          <span class="badge bg-success px-2 py-1" style="font-size: 0.75rem;">FREE</span>
                        @else
                          <span class="fw-bold text-dark" style="font-family: 'Outfit', sans-serif; font-size: 1.05rem;">
                            ₹{{ number_format($plan->price, 2) }}
                          </span>
                        @endif
                      </td>
                      <td>
                        @if($timeframe == 'yearly')
                          <span class="badge bg-success text-white px-2 py-1" style="font-size: 0.78rem; border-radius: 12px;">
                            <i class="fas fa-calendar-check me-1"></i> Yearly
                          </span>
                        @else
                          <span class="badge bg-primary text-white px-2 py-1" style="font-size: 0.78rem; border-radius: 12px;">
                            <i class="fas fa-calendar-alt me-1"></i> Monthly
                          </span>
                        @endif
                      </td>
                      <td>{{ $plan->duration_days }} Days</td>
                      <td>
                        @if($plan->multi_outlet_checkbox == 'Y')
                          <span class="badge bg-info text-dark" style="font-size: 0.75rem;">
                            <i class="fas fa-store-alt me-1"></i> {{ $plan->total_number_of_outlets == 0 ? 'Unlimited' : $plan->total_number_of_outlets }} Outlets
                          </span>
                        @else
                          <span class="badge bg-light text-muted border" style="font-size: 0.75rem;">Single</span>
                        @endif
                      </td>

                      <!-- 1. Default Plan Toggle -->
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <label class="switch" title="Toggle Default Plan">
                            <input type="checkbox" class="toggle-default-plan" data-id="{{ $plan->id }}" {{ $plan->is_default_plan == 'Y' ? 'checked' : '' }}>
                            <span class="slider"></span>
                          </label>
                          <span class="default-plan-status badge {{ $plan->is_default_plan == 'Y' ? 'bg-success' : 'bg-secondary' }}" style="font-size: 0.72rem; padding: 3px 8px; border-radius: 12px;">
                            {{ $plan->is_default_plan == 'Y' ? 'Yes' : 'No' }}
                          </span>
                        </div>
                      </td>

                      <!-- 2. Active Plan Status Toggle -->
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <label class="switch" title="Toggle Plan Active / Inactive Status">
                            <input type="checkbox" class="toggle-plan-status" data-id="{{ $plan->id }}" {{ $plan->plan_status == 'A' ? 'checked' : '' }}>
                            <span class="slider"></span>
                          </label>
                          <span class="plan-status-badge badge {{ $plan->plan_status == 'A' ? 'bg-success' : 'bg-danger' }}" style="font-size: 0.72rem; padding: 3px 8px; border-radius: 12px;">
                            {{ $plan->plan_status == 'A' ? 'Active' : 'Inactive' }}
                          </span>
                        </div>
                      </td>
                     
                      <td>
                        <small class="text-muted font-monospace">{{ Str::limit($plan->razorpay_plan_id, 18) ?: 'N/A' }}</small>
                      </td>
                      <td>
                        <div class="d-flex align-items-center justify-content-end gap-1">
                          <!-- Edit -->
                          <a href="{{ route('plans.edit', $plan->id) }}" class="btn btn-outline-primary btn-sm" title="Edit Plan" style="border-radius: 8px;">
                            <i class="fa fa-edit"></i>
                          </a>

                          <!-- Delete -->
                          <button class="btn btn-outline-danger btn-sm delete-btn" 
                                  data-id="{{ $plan->id }}" 
                                  data-name="{{ $plan->name }}"
                                  title="Delete Plan"
                                  style="border-radius: 8px;">
                            <i class="fa fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Floating Toast Alert Container -->
  <div class="plan-toast-container" id="planToastContainer"></div>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 16px 36px rgba(15, 23, 42, 0.15);">
        <div class="modal-header bg-danger text-white py-3 px-4">
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-exclamation-triangle fa-lg"></i>
            <h5 class="modal-title text-white mb-0 fw-bold" id="deleteModalLabel">Confirm Delete Plan</h5>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          Are you sure you want to delete plan "<strong id="planName" class="text-danger"></strong>"?
          <p class="text-muted small mt-2 mb-0">This will soft-delete the plan and remove it from public subscription listings.</p>
        </div>
        <div class="modal-footer bg-light px-4 py-3">
          <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-secondary px-3 py-2 rounded-pill" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger px-4 py-2 rounded-pill fw-bold">Delete Plan</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  @include('includes.script')

  <script>
    $(document).ready(function() {
      var table = $('#planTable').DataTable({
        ordering: false,
        pageLength: 25
      });

      // Toast notification helper
      function showToast(message, type = 'success') {
        let icon = type === 'success' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
        let toastHtml = $('<div class="plan-toast ' + type + '">' +
          '<i class="fas ' + icon + ' fa-lg"></i>' +
          '<span>' + message + '</span>' +
        '</div>');

        $('#planToastContainer').append(toastHtml);
        setTimeout(function() {
          toastHtml.fadeOut(300, function() { $(this).remove(); });
        }, 3200);
      }

      // Timeframe tab filtering
      $('.timeframe-filter-btn').on('click', function(e) {
        e.preventDefault();
        $('.timeframe-filter-btn').removeClass('active');
        $(this).addClass('active');

        var filter = $(this).data('filter');
        if (filter === 'all') {
          table.column(4).search('').draw();
        } else if (filter === 'monthly') {
          table.column(4).search('Monthly').draw();
        } else if (filter === 'yearly') {
          table.column(4).search('Yearly').draw();
        }
      });

      // 1. Toggle Active / Inactive Plan Status AJAX
      $(document).on('change', '.toggle-plan-status', function() {
        let checkbox = $(this);
        let id = checkbox.data('id');
        let badge = checkbox.closest('td').find('.plan-status-badge');
        let isChecked = checkbox.is(':checked');

        badge.text('Updating...').removeClass('bg-success bg-danger').addClass('bg-info text-white');

        $.ajax({
          url: '{{ url("admin/plans") }}/' + id + '/toggle-status',
          type: 'POST',
          data: {
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            if (response.success) {
              if (response.is_active || response.plan_status === 'A') {
                checkbox.prop('checked', true);
                badge.text('Active').removeClass('bg-info bg-danger').addClass('bg-success text-white');
              } else {
                checkbox.prop('checked', false);
                badge.text('Inactive').removeClass('bg-info bg-success').addClass('bg-danger text-white');
              }
              showToast(response.message || 'Plan status updated successfully', 'success');
            } else {
              checkbox.prop('checked', !isChecked);
              badge.text(!isChecked ? 'Active' : 'Inactive')
                   .removeClass('bg-info')
                   .addClass(!isChecked ? 'bg-success text-white' : 'bg-danger text-white');
              showToast(response.message || 'Failed to update plan status', 'error');
            }
          },
          error: function(xhr) {
            checkbox.prop('checked', !isChecked);
            badge.text(!isChecked ? 'Active' : 'Inactive')
                 .removeClass('bg-info')
                 .addClass(!isChecked ? 'bg-success text-white' : 'bg-danger text-white');
            showToast('Error connecting to server.', 'error');
          }
        });
      });

      // 2. Toggle Default Plan AJAX
      $(document).on('change', '.toggle-default-plan', function() {
        let checkbox = $(this);
        let id = checkbox.data('id');
        let badge = checkbox.closest('td').find('.default-plan-status');
        let isChecked = checkbox.is(':checked');

        badge.text('Updating...').removeClass('bg-success bg-secondary').addClass('bg-info text-white');

        $.ajax({
          url: '{{ url("admin/plans") }}/' + id + '/toggle-default',
          type: 'POST',
          data: {
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            if (response.success) {
              if (response.is_default_plan === 'Y') {
                checkbox.prop('checked', true);
                badge.text('Yes').removeClass('bg-info bg-secondary').addClass('bg-success text-white');
              } else {
                checkbox.prop('checked', false);
                badge.text('No').removeClass('bg-info bg-success').addClass('bg-secondary text-white');
              }
              showToast(response.message || 'Default plan status updated', 'success');
            } else {
              checkbox.prop('checked', !isChecked);
              badge.text(!isChecked ? 'Yes' : 'No')
                   .removeClass('bg-info')
                   .addClass(!isChecked ? 'bg-success text-white' : 'bg-secondary text-white');
              showToast(response.message || 'Failed to update default plan', 'error');
            }
          },
          error: function(xhr) {
            checkbox.prop('checked', !isChecked);
            badge.text(!isChecked ? 'Yes' : 'No')
                 .removeClass('bg-info')
                 .addClass(!isChecked ? 'bg-success text-white' : 'bg-secondary text-white');
            showToast('Error connecting to server.', 'error');
          }
        });
      });

      // Delete button
      $('.delete-btn').on('click', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        
        $('#planName').text(name);
        $('#deleteForm').attr('action', '{{ url("admin/plans") }}/' + id);
        
        let modalEl = document.getElementById('deleteModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
          bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else {
          $('#deleteModal').modal('show');
        }
      });

      // Enable row sorting
      $('#planTable tbody').sortable({
        items: 'tr.plan-row',
        cursor: 'move',
        placeholder: 'ui-state-highlight',
        helper: function(e, tr) {
          var $originals = tr.children();
          var $helper = tr.clone();
          $helper.children().each(function(index) {
            $(this).width($originals.eq(index).width());
          });
          return $helper;
        },
        update: function(event, ui) {
          // Verify that all rank-0-rows are before all rank-1-rows, and all rank-1-rows are before all rank-2-rows
          let isValid = true;
          let currentRank = 0;
          
          $('#planTable tbody tr').each(function() {
            let rowRank = 2;
            if ($(this).hasClass('rank-0-row')) {
              rowRank = 0;
            } else if ($(this).hasClass('rank-1-row')) {
              rowRank = 1;
            }
            
            if (rowRank < currentRank) {
              isValid = false;
              return false; // break loop
            }
            currentRank = rowRank;
          });

          if (!isValid) {
            alert('Default plans (Yes) must always remain on top of other plans!');
            $(this).sortable('cancel');
            return;
          }

          // Gather IDs in the new order
          let order = [];
          $('#planTable tbody tr.plan-row').each(function() {
            let id = $(this).data('id');
            if (id) {
              order.push(id);
            }
          });

          // AJAX call to save order in backend
          $.ajax({
            url: "{{ route('admin.plans.update-order') }}",
            type: "POST",
            data: {
              _token: "{{ csrf_token() }}",
              order: order
            },
            success: function(response) {
              if (response.success) {
                showToast('Plan sort order saved successfully', 'success');
              } else {
                alert('Failed to update order: ' + response.message);
                $('#planTable tbody').sortable('cancel');
              }
            },
            error: function(xhr) {
              let msg = 'Failed to update order.';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                msg += ' ' + xhr.responseJSON.message;
              }
              alert(msg);
              $('#planTable tbody').sortable('cancel');
            }
          });
        }
      });
    });
  </script>

</body>
</html>