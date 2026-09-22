@extends('layouts.app')

@section('title')
<title>Admin || Manage Restaurant Outlets & Branches</title>
@endsection

@section('style')
@include('includes.style')
<style>
    .outlet-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    .outlet-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
    }
    .outlet-card.active-branch {
        border: 2px solid #ff6a00;
        background: linear-gradient(to bottom right, #ffffff, #fffaf5);
    }
    .stat-badge {
        font-size: 0.78rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
    }
    .outlet-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
    .limit-progress-bar {
        height: 8px;
        border-radius: 4px;
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">

    <!-- Page Header -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h5 class="m-b-10"><i class="fas fa-store-alt text-primary me-2"></i> Manage Outlets & Branches</h5>
            <p class="text-muted mb-0">Create and manage your restaurant branch outlets under your current subscription.</p>
          </div>
          <div class="col-md-4 text-md-end mt-3 mt-md-0">
            @if($canAddMore)
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOutletModal">
                <i class="fas fa-plus-circle me-1"></i> + Add New Outlet
              </button>
            @elseif(!$isMultiOutletEnabled)
              <a href="{{ route('select.plan.page') }}" class="btn btn-warning">
                <i class="fas fa-arrow-circle-up me-1"></i> Upgrade to Multi-Outlet Plan
              </a>
            @else
              <button class="btn btn-secondary disabled" title="Plan limit reached">
                <i class="fas fa-lock me-1"></i> Outlet Limit Reached
              </button>
            @endif
          </div>
        </div>
      </div>
    </div>

    @include('includes.message')

    <!-- Plan & Outlet Limits Overview Bar -->
    <div class="row mb-4">
      <div class="col-lg-4 col-md-6 mb-3">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body d-flex align-items-center">
            <div class="outlet-icon-box bg-light-primary text-primary me-3">
              <i class="fas fa-building"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1" style="font-size: 0.85rem;">Active Outlets</h6>
              <h4 class="mb-0 fw-bold">{{ $totalOutletsCount }} <span class="text-muted fw-normal" style="font-size: 0.9rem;">/ {{ $maxOutlets == 0 ? 'Unlimited' : $maxOutlets }} Allowed</span></h4>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 mb-3">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body d-flex align-items-center">
            <div class="outlet-icon-box bg-light-success text-success me-3">
              <i class="fas fa-layer-group"></i>
            </div>
            <div>
              <h6 class="text-muted mb-1" style="font-size: 0.85rem;">Current Plan</h6>
              <h5 class="mb-0 fw-bold text-dark">{{ $plan ? $plan->name : 'No Active Plan' }}</h5>
              <small class="text-{{ $isMultiOutletEnabled ? 'success' : 'danger' }} fw-semibold">
                <i class="fas fa-{{ $isMultiOutletEnabled ? 'check-circle' : 'times-circle' }}"></i>
                {{ $isMultiOutletEnabled ? 'Multi-Outlet Enabled' : 'Single Outlet Only' }}
              </small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-12 mb-3">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body d-flex align-items-center">
            <div class="outlet-icon-box bg-light-warning text-warning me-3">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="flex-grow-1">
              <h6 class="text-muted mb-1" style="font-size: 0.85rem;">Working Context</h6>
              @php
                $activeRest = \App\Models\RestaurantMaster::find($currentActiveRestaurantId);
              @endphp
              <h5 class="mb-0 fw-bold text-primary">{{ $activeRest ? $activeRest->name : 'Not Selected' }}</h5>
              <small class="text-muted">{{ $activeRest && $activeRest->isMainRestaurant() ? 'Primary Main Branch' : 'Child Outlet Branch' }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    @if(!$isMultiOutletEnabled)
      <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between mb-4" role="alert">
        <div class="d-flex align-items-center">
          <i class="fas fa-info-circle fa-2x me-3 text-warning"></i>
          <div>
            <h6 class="alert-heading mb-1 fw-bold">Multi-Outlet Feature is not active in your current plan</h6>
            <p class="mb-0 text-muted" style="font-size: 0.9rem;">To open and manage multiple restaurant branches under a single account, please upgrade your subscription plan.</p>
          </div>
        </div>
        <a href="{{ route('select.plan.page') }}" class="btn btn-warning text-dark fw-bold btn-sm text-nowrap ms-3">Upgrade Plan</a>
      </div>
    @endif

    <!-- Outlets Grid / List -->
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-list me-2 text-primary"></i> All Outlets & Branches</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-4">Type / Code</th>
                <th>Outlet Name</th>
                <th>Address & Pincode</th>
                <th>GSTIN / FSSAI</th>
                <th>Tables / Orders</th>
                <th>Status</th>
                <th>Active Context</th>
                <th class="text-end pe-4">Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- 1. Main Restaurant Row -->
              <tr class="{{ $currentActiveRestaurantId == $mainRestaurant->id ? 'table-warning bg-opacity-25' : '' }}">
                <td class="ps-4">
                  <span class="badge bg-primary text-white stat-badge">
                    <i class="fas fa-crown me-1"></i> Main Branch
                  </span>
                  <div class="small text-muted mt-1 fw-semibold">{{ $mainRestaurant->restaurant_id_unique ?? 'BILL-BITE-001' }}</div>
                </td>
                <td>
                  <strong class="text-dark fs-6">{{ $mainRestaurant->name }}</strong>
                </td>
                <td>
                  <div class="text-dark">{{ $mainRestaurant->address ?? 'N/A' }}</div>
                  <small class="text-muted">Pincode: {{ $mainRestaurant->pincode ?? 'N/A' }}</small>
                </td>
                <td>
                  <div><small><strong>GSTIN:</strong> {{ $mainRestaurant->gstin ?? 'N/A' }}</small></div>
                  <div><small><strong>FSSAI:</strong> {{ $mainRestaurant->fssai_number ?? 'N/A' }}</small></div>
                </td>
                <td>
                  <span class="badge bg-light text-dark border">{{ $mainRestaurant->tables()->count() }} Tables</span>
                  <span class="badge bg-light text-dark border">{{ $mainRestaurant->orders()->count() }} Orders</span>
                </td>
                <td>
                  <span class="badge bg-success">Active</span>
                </td>
                <td>
                  @if($currentActiveRestaurantId == $mainRestaurant->id)
                    <span class="badge bg-primary px-3 py-2 text-white">
                      <i class="fas fa-check-circle me-1"></i> Currently Active
                    </span>
                  @else
                    <a href="{{ route('restaurant.outlets.switch', $mainRestaurant->id) }}" class="btn btn-outline-primary btn-sm">
                      <i class="fas fa-exchange-alt me-1"></i> Switch Here
                    </a>
                  @endif
                </td>
                <td class="text-end pe-4">
                  <button class="btn btn-sm btn-outline-secondary editOutletBtn"
                          data-id="{{ $mainRestaurant->id }}"
                          data-name="{{ $mainRestaurant->name }}"
                          data-address="{{ $mainRestaurant->address }}"
                          data-pincode="{{ $mainRestaurant->pincode }}"
                          data-gstin="{{ $mainRestaurant->gstin }}"
                          data-fssai="{{ $mainRestaurant->fssai_number }}"
                          data-gst="{{ $mainRestaurant->gst_percentage }}"
                          data-upi="{{ $mainRestaurant->upi_id }}">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                </td>
              </tr>

              <!-- 2. Child Outlets Rows -->
              @forelse($outlets as $outlet)
              <tr class="{{ $currentActiveRestaurantId == $outlet->id ? 'table-warning bg-opacity-25' : '' }}">
                <td class="ps-4">
                  <span class="badge bg-info text-dark stat-badge">
                    <i class="fas fa-store me-1"></i> Branch Outlet
                  </span>
                  <div class="small text-muted mt-1 fw-semibold">{{ $outlet->restaurant_id_unique ?? ('BILL-BITE-' . str_pad($outlet->id, 3, '0', STR_PAD_LEFT)) }}</div>
                </td>
                <td>
                  <strong class="text-dark fs-6">{{ $outlet->name }}</strong>
                </td>
                <td>
                  <div class="text-dark">{{ $outlet->address ?? 'N/A' }}</div>
                  <small class="text-muted">Pincode: {{ $outlet->pincode ?? 'N/A' }}</small>
                </td>
                <td>
                  <div><small><strong>GSTIN:</strong> {{ $outlet->gstin ?? 'N/A' }}</small></div>
                  <div><small><strong>FSSAI:</strong> {{ $outlet->fssai_number ?? 'N/A' }}</small></div>
                </td>
                <td>
                  <span class="badge bg-light text-dark border">{{ $outlet->tables_count ?? 0 }} Tables</span>
                  <span class="badge bg-light text-dark border">{{ $outlet->orders_count ?? 0 }} Orders</span>
                </td>
                <td>
                  @if($outlet->status === 'A')
                    <a href="{{ route('restaurant.outlets.status', $outlet->id) }}" class="badge bg-success text-decoration-none" title="Click to deactivate">Active</a>
                  @else
                    <a href="{{ route('restaurant.outlets.status', $outlet->id) }}" class="badge bg-danger text-decoration-none" title="Click to activate">Inactive</a>
                  @endif
                </td>
                <td>
                  @if($currentActiveRestaurantId == $outlet->id)
                    <span class="badge bg-primary px-3 py-2 text-white">
                      <i class="fas fa-check-circle me-1"></i> Currently Active
                    </span>
                  @else
                    <a href="{{ route('restaurant.outlets.switch', $outlet->id) }}" class="btn btn-outline-primary btn-sm">
                      <i class="fas fa-exchange-alt me-1"></i> Switch Here
                    </a>
                  @endif
                </td>
                <td class="text-end pe-4">
                  <button class="btn btn-sm btn-outline-secondary editOutletBtn me-1"
                          data-id="{{ $outlet->id }}"
                          data-name="{{ $outlet->name }}"
                          data-address="{{ $outlet->address }}"
                          data-pincode="{{ $outlet->pincode }}"
                          data-gstin="{{ $outlet->gstin }}"
                          data-fssai="{{ $outlet->fssai_number }}"
                          data-gst="{{ $outlet->gst_percentage }}"
                          data-upi="{{ $outlet->upi_id }}">
                    <i class="fas fa-edit"></i>
                  </button>

                  <a href="{{ route('restaurant.outlets.delete', $outlet->id) }}"
                     class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Are you sure you want to delete outlet: {{ $outlet->name }}?')"
                     title="Delete Outlet">
                    <i class="fas fa-trash"></i>
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                  <i class="fas fa-store-slash fa-2x mb-2 text-muted d-block"></i>
                  No additional branch outlets created yet. Click <strong>"+ Add New Outlet"</strong> to create a branch.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal: Add Outlet -->
<div class="modal fade" id="addOutletModal" tabindex="-1" aria-labelledby="addOutletModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <form action="{{ route('restaurant.outlets.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title text-white" id="addOutletModalLabel"><i class="fas fa-plus-circle me-2"></i> Add New Branch Outlet</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label fw-bold">Outlet Branch Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Tasty Bites - Salt Lake Branch" required>
            </div>

            <div class="col-md-8 mb-3">
              <label class="form-label fw-bold">Outlet Address <span class="text-danger">*</span></label>
              <input type="text" name="address" class="form-control" placeholder="Full address of the outlet" required>
            </div>

            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold">Pincode <span class="text-danger">*</span></label>
              <input type="text" name="pincode" class="form-control" placeholder="e.g. 700091" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">GSTIN (Optional)</label>
              <input type="text" name="gstin" class="form-control" placeholder="e.g. 19AAAAA0000A1Z5">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">FSSAI License No. (Optional)</label>
              <input type="text" name="fssai_number" class="form-control" placeholder="e.g. 12345678901234">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">GST Percentage (%)</label>
              <input type="number" step="0.01" min="0" max="100" name="gst_percentage" class="form-control" value="{{ $mainRestaurant->gst_percentage ?? 0.00 }}">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">UPI ID for Direct Payments (Optional)</label>
              <input type="text" name="upi_id" class="form-control" placeholder="e.g. restaurant@upi">
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Create Outlet</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Edit Outlet -->
<div class="modal fade" id="editOutletModal" tabindex="-1" aria-labelledby="editOutletModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <form id="editOutletForm" method="POST">
        @csrf
        <div class="modal-header bg-secondary text-white">
          <h5 class="modal-title text-white" id="editOutletModalLabel"><i class="fas fa-edit me-2"></i> Edit Outlet Details</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label fw-bold">Outlet Branch Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>

            <div class="col-md-8 mb-3">
              <label class="form-label fw-bold">Outlet Address <span class="text-danger">*</span></label>
              <input type="text" name="address" id="edit_address" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold">Pincode <span class="text-danger">*</span></label>
              <input type="text" name="pincode" id="edit_pincode" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">GSTIN</label>
              <input type="text" name="gstin" id="edit_gstin" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">FSSAI License No.</label>
              <input type="text" name="fssai_number" id="edit_fssai" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">GST Percentage (%)</label>
              <input type="number" step="0.01" min="0" max="100" name="gst_percentage" id="edit_gst_percentage" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">UPI ID</label>
              <input type="text" name="upi_id" id="edit_upi_id" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Outlet</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('script')
@include('includes.script')
<script>
$(document).ready(function() {
    $('.editOutletBtn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var address = $(this).data('address');
        var pincode = $(this).data('pincode');
        var gstin = $(this).data('gstin');
        var fssai = $(this).data('fssai');
        var gst = $(this).data('gst');
        var upi = $(this).data('upi');

        $('#edit_name').val(name);
        $('#edit_address').val(address);
        $('#edit_pincode').val(pincode);
        $('#edit_gstin').val(gstin);
        $('#edit_fssai').val(fssai);
        $('#edit_gst_percentage').val(gst);
        $('#edit_upi_id').val(upi);

        var updateUrl = "{{ url('outlets/update') }}/" + id;
        $('#editOutletForm').attr('action', updateUrl);

        var editModal = new bootstrap.Modal(document.getElementById('editOutletModal'));
        editModal.show();
    });
});
</script>
@endsection
