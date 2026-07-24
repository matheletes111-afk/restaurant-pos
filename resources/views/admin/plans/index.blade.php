<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Manage Plans</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  
  <style>
    /* Premium custom switch toggle */
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 22px;
        vertical-align: middle;
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
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #ff6a00;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #ff6a00;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(22px);
        -ms-transform: translateX(22px);
        transform: translateX(22px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
    
    .default-plan-status {
        margin-left: 8px;
        vertical-align: middle;
    }
    
    .plan-row {
        cursor: grab;
    }
    .plan-row:active {
        cursor: grabbing;
    }
    .ui-state-highlight {
        height: 50px;
        background-color: rgba(255, 106, 0, 0.05) !important;
        border: 2px dashed #ff6a00 !important;
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
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h5 class="m-b-10">Manage Subscription Plans</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item" aria-current="page">Manage Plans</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- Breadcrumb end -->

      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            @include('includes.message')
            <div class="card-header">
              <a href="{{ route('plans.create') }}" class="btn btn-primary" style="float: right;">
                <i class="fa fa-plus"></i> Add Plan
              </a>
            </div>

            <div class="card-body">
              <div class="dt-responsive table-responsive">
                <table id="planTable" class="table table-striped table-bordered nowrap">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Price</th>
                      <th>Currency</th>
                      <th>Billing Cycle</th>
                      <th>Duration (Days)</th>
                      <th>Default Plan</th>
                      <th>Status</th>
                     
                      <th>Razorpay Plan ID</th>
                      <th>Actions</th>
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
                    @endphp
                    <tr class="plan-row {{ $rankClass }}" data-id="{{ $plan->id }}" data-default="{{ $plan->is_default_plan }}">
                      <td>{{ $plan->id }}</td>
                      <td>{{ $plan->name }}</td>
                      <td>{{ number_format($plan->price, 2) }}</td>
                      <td>{{ $plan->currency }}</td>
                      <td>
                        <span class="btn btn-{{ $plan->billing_cycle == 'monthly' ? 'primary' : ($plan->billing_cycle == 'yearly' ? 'success' : 'info') }}">
                          {{ ucfirst($plan->billing_cycle) }}
                        </span>
                      </td>
                      <td>{{ $plan->duration_days }}</td>
                      <td>@if(@$plan->is_default_plan=="Y") Yes @else No @endif</td>
                      <td>
                        @if($plan->plan_status == 'A')
                          <span class="btn btn-sm btn-success">Active</span>
                        @elseif($plan->plan_status == 'U')
                          <span class="btn btn-sm btn-warning text-dark">Unverified</span>
                        @else
                          <span class="btn btn-sm btn-secondary">{{ $plan->plan_status }}</span>
                        @endif
                      </td>
                     
                      <td>
                        <small class="text-muted">{{ Str::limit($plan->razorpay_plan_id, 20) }}</small>
                      </td>
                      <td>
                        <!-- Edit -->
                        <a href="{{ route('plans.edit', $plan->id) }}" class="btn btn-success btn-sm">
                          <i class="fa fa-edit"></i>
                        </a>

                        <!-- Delete -->
                        <button class="btn btn-danger btn-sm delete-btn" 
                                data-id="{{ $plan->id }}" 
                                data-name="{{ $plan->name }}">
                          <i class="fa fa-trash"></i>
                        </button>

                        <!-- View History -->
                        {{-- <a href="#" class="btn btn-info btn-sm history-btn" 
                           data-id="{{ $plan->id }}">
                          <i class="fa fa-history"></i>
                        </a> --}}

                            <?php
        $user = auth()->user();
        $hasFreeTrial = \App\Models\Subscription::where('user_id', $user->restaurant_id)
            ->whereHas('plan', function($query) {
                $query->where('price', 0);
            })
            ->exists();
        
        $isActive = \App\Models\Subscription::where('user_id', $user->restaurant_id)
            ->where('plan_id', $plan->id)
            ->where('status', 'active')
            ->exists();
    ?>
    
{{--     @if($isActive)
        <button class="btn btn-secondary btn-sm" disabled>
            <i class="fa fa-check"></i> Subscribed
        </button>
    @elseif($plan->price == 0 && $hasFreeTrial)
        <button class="btn btn-warning btn-sm" disabled title="You have already used your free trial">
            <i class="fa fa-ban"></i> Trial Used
        </button>
    @else
        <a href="{{ route('admin.subscriptions.create', $plan->id) }}" 
           class="btn btn-primary btn-sm">
            <i class="fa fa-shopping-cart"></i> 
            {{ $plan->price == 0 ? 'Start Free Trial' : 'Subscribe' }}
        </a>
    @endif --}}
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

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete plan "<span id="planName"></span>"?
        </div>
        <div class="modal-footer">
          <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
      $('#planTable').DataTable({
        ordering: false
      });

      // Delete button
      $('.delete-btn').on('click', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        
        $('#planName').text(name);
        $('#deleteForm').attr('action', '{{ url("admin/plans") }}/' + id);
        $('#deleteModal').modal('show');
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
                // Reload the page to reflect the new ID mappings
                location.reload();
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