<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Tables • Bill&Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN Loaded after includes.style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  
  <!-- External Table Management CSS -->
  <link rel="stylesheet" href="{{ asset('admin_template/css/table-manage.css') }}?v={{ time() }}">
</head>
<body>
@include('includes.sidebar')

@php
  $tableList = $tables ?? collect([]);
  $totalTables = count($tableList);
  $activeTables = $tableList->where('status', 'A')->count();
  $inactiveTables = $tableList->where('status', 'I')->count();
  $quotaLimit = (isset($plan_details) && isset($plan_details->total_number_of_table)) ? (int)$plan_details->total_number_of_table : 0;
  $isUnlimited = ($quotaLimit <= 0);
  $isQuotaReached = (!$isUnlimited && $totalTables >= $quotaLimit);
@endphp

<div class="pc-container">
  <div class="pc-content">
    
    <!-- Hero Header Architecture -->
    <div class="table-header-card">
      <div class="table-header-left">
        <div class="table-header-icon">
          <i class="fa-solid fa-table-cells-large"></i>
        </div>
        <div class="table-header-meta">
          <span class="table-eyebrow-tag"><i class="fa-solid fa-utensils me-1"></i> Floor &amp; Seating Master</span>
          <h1 class="table-main-title">Manage Floor Tables &amp; QR Standees</h1>
          <p class="table-sub-text">
            @if(isset($restaurant))
              <span class="text-white fw-bold"><i class="fa-solid fa-store text-warning me-1"></i> {{ $restaurant->name }}</span>
              @if($restaurant->logo)
                <span class="table-sub-badge"><i class="fa-solid fa-circle-check"></i> Custom Logo Active</span>
              @endif
              <span class="opacity-75">&bull; Auto-branded QR Standees</span>
            @else
              <span>Configure restaurant dining tables and generate contactless QR order standees</span>
            @endif
          </p>
        </div>
      </div>
      
      <div class="table-header-actions">
        @if(isset($plan_details))
          <div class="plan-quota-pill {{ $isQuotaReached ? 'quota-exceeded' : '' }}" title="Subscription Plan Quota">
            <i class="fa-solid {{ $isQuotaReached ? 'fa-triangle-exclamation text-danger' : 'fa-gem text-warning' }}"></i>
            <span>{{ $isUnlimited ? 'Unlimited Tables' : "{$totalTables} / {$quotaLimit} Tables Used" }}</span>
          </div>
        @endif

        @if($totalTables > 0 && auth()->user()->hasPermission('table_master', 'edit'))
          <a href="{{ route('table.manage.regenerate.all') }}" 
             onclick="return confirm('Regenerate QR standees for ALL active tables with the latest logo and restaurant branding?')"
             class="btn-header-secondary"
             title="Delete old and regenerate all QR standees">
             <i class="fa-solid fa-arrows-rotate"></i> Regenerate All QRs
          </a>
        @endif

        @if(auth()->user()->hasPermission('table_master', 'add'))
          @if(!$isQuotaReached)
            <button type="button" class="btn-header-primary" data-toggle="modal" data-target="#addTableModal" data-bs-toggle="modal" data-bs-target="#addTableModal">
              <i class="fa-solid fa-plus-circle"></i> Add Table
            </button>
          @else
            <button type="button" class="btn-header-primary disabled" disabled title="Table quota limit reached under current subscription plan">
              <i class="fa-solid fa-lock"></i> Quota Reached
            </button>
          @endif
        @endif
      </div>
    </div>

    <!-- Alert Messages -->
    @include('includes.message')

    <!-- KPI Live Stats Deck -->
    <div class="table-kpi-grid">
      <div class="table-kpi-card kpi-table-total">
        <div class="table-kpi-icon">
          <i class="fa-solid fa-chair"></i>
        </div>
        <div class="table-kpi-info">
          <span class="table-kpi-number">{{ $totalTables }}</span>
          <span class="table-kpi-label">Total Tables</span>
        </div>
      </div>

      <div class="table-kpi-card kpi-table-active">
        <div class="table-kpi-icon">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="table-kpi-info">
          <span class="table-kpi-number">{{ $activeTables }}</span>
          <span class="table-kpi-label">Active &amp; Ready</span>
        </div>
      </div>

      <div class="table-kpi-card kpi-table-inactive">
        <div class="table-kpi-icon">
          <i class="fa-solid fa-circle-pause"></i>
        </div>
        <div class="table-kpi-info">
          <span class="table-kpi-number">{{ $inactiveTables }}</span>
          <span class="table-kpi-label">Inactive / Reserved</span>
        </div>
      </div>

      <div class="table-kpi-card kpi-table-quota">
        <div class="table-kpi-icon">
          <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div class="table-kpi-info">
          <span class="table-kpi-number">{{ $isUnlimited ? '∞' : ($quotaLimit - $totalTables) }}</span>
          <span class="table-kpi-label">{{ $isUnlimited ? 'Unlimited Quota' : 'Slots Available' }}</span>
        </div>
      </div>
    </div>

    <!-- Tables Data Card -->
    <div class="table-data-card">
      <div class="table-data-header">
        <h3 class="table-data-title">
          <i class="fa-solid fa-list-check text-primary"></i> Floor Tables Directory
        </h3>
        <span class="text-muted small">
          <i class="fa-solid fa-circle-info text-info me-1"></i> Click on any QR standee preview to inspect in high resolution.
        </span>
      </div>

      @if($totalTables > 0)
        <div class="table-responsive">
          <table id="tableManage" class="table custom-pos-table nowrap">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th>Table &amp; Seating Info</th>
                <th>Description</th>
                <th>Branded QR Standee</th>
                <th style="width: 120px;">Status</th>
                <th style="width: 130px;">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($tables as $key => $table)
              <tr>
                <td>
                  <span class="table-index-pill">{{ $key + 1 }}</span>
                </td>
                <td>
                  <div class="table-name-cell">
                    <div class="table-name-title">
                      <i class="fa-solid fa-utensils text-primary me-1" style="font-size: 0.85rem;"></i>
                      {{ $table->name }}
                      <span class="table-id-tag">ID: #{{ $table->id }}</span>
                    </div>
                  </div>
                </td>
                <td>
                  @if(!empty($table->description))
                    <div class="table-desc-text">{{ $table->description }}</div>
                  @else
                    <span class="text-muted small"><em>No notes specified</em></span>
                  @endif
                </td>
                <td>
                  <div class="standee-preview-dock">
                    @if($table->qr_code && file_exists(public_path('qrcodes/' . $table->qr_code)))
                      <div class="standee-thumb-wrap viewStandeeBtn" 
                           data-src="{{ asset('qrcodes/'.$table->qr_code) }}"
                           data-title="{{ $table->name }} QR Standee"
                           data-download="{{ asset('qrcodes/'.$table->qr_code) }}"
                           data-filename="{{ Str::slug($table->name) }}-qr-standee.svg"
                           title="Click to preview full standee">
                        <img src="{{ asset('qrcodes/'.$table->qr_code) }}" alt="{{ $table->name }} Standee">
                      </div>
                      <div class="standee-actions-group">
                        <a href="{{ asset('qrcodes/'.$table->qr_code) }}" 
                           download="{{ Str::slug($table->name) }}-qr-standee.svg" 
                           class="btn-download-standee" 
                           title="Download printable SVG standee">
                          <i class="fa-solid fa-arrow-down-to-bracket"></i> Download SVG
                        </a>
                      </div>
                    @else
                      <div class="d-flex flex-column gap-1">
                        <span class="badge bg-light text-danger border border-danger small py-1 px-2">
                          <i class="fa-solid fa-circle-exclamation me-1"></i> Standee Missing
                        </span>
                        <a href="{{ route('table.manage.regenerate.qr', $table->id) }}" class="btn-download-standee" style="background:#0284c7;">
                          <i class="fa-solid fa-wand-magic-sparkles"></i> Generate Now
                        </a>
                      </div>
                    @endif
                  </div>
                </td>
                <td>
                  @if(auth()->user()->hasPermission('table_master', 'edit'))
                    <a href="{{ route('table.manage.status', $table->id) }}"
                       onclick="return confirm('Are you sure you want to change status for {{ addslashes($table->name) }}?')"
                       class="table-status-pill {{ $table->status == 'A' ? 'active' : 'inactive' }}"
                       title="Click to toggle table availability">
                       <i class="fa-solid {{ $table->status == 'A' ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                       {{ $table->status == 'A' ? 'Active' : 'Inactive' }}
                    </a>
                  @else
                    <span class="table-status-pill {{ $table->status == 'A' ? 'active' : 'inactive' }}" style="cursor: default;">
                       <i class="fa-solid {{ $table->status == 'A' ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                       {{ $table->status == 'A' ? 'Active' : 'Inactive' }}
                    </span>
                  @endif
                </td>
                <td>
                  <div class="table-actions-dock">
                    @if(auth()->user()->hasPermission('table_master', 'edit'))
                      <a href="{{ route('table.manage.regenerate.qr', $table->id) }}"
                         onclick="return confirm('Regenerate QR standee for {{ addslashes($table->name) }} with latest logo &amp; details?')"
                         class="table-action-btn regen-btn"
                         title="Regenerate Standee QR">
                        <i class="fa-solid fa-arrows-rotate"></i>
                      </a>
                      <button type="button" 
                              class="table-action-btn edit-btn editBtn"
                              data-id="{{ $table->id }}"
                              data-name="{{ $table->name }}"
                              data-description="{{ $table->description }}"
                              title="Edit Table Info">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                    @endif
                    @if(auth()->user()->hasPermission('table_master', 'delete'))
                      <a href="{{ route('table.manage.delete', $table->id) }}"
                         onclick="return confirm('Are you sure you want to delete table {{ addslashes($table->name) }}?')"
                         class="table-action-btn delete-btn"
                         title="Delete Table">
                        <i class="fa-solid fa-trash"></i>
                      </a>
                    @endif
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="table-empty-box">
          <div class="table-empty-icon">
            <i class="fa-solid fa-table-cells"></i>
          </div>
          <h4 class="table-empty-title">No Tables Configured Yet</h4>
          <p class="table-empty-desc">Create your dining floor tables to generate branded contactless QR code standees for instant table ordering.</p>
          @if(auth()->user()->hasPermission('table_master', 'add') && !$isQuotaReached)
            <button type="button" class="btn-pos-primary" data-toggle="modal" data-target="#addTableModal" data-bs-toggle="modal" data-bs-target="#addTableModal">
              <i class="fa-solid fa-plus-circle me-1"></i> Add Your First Table
            </button>
          @endif
        </div>
      @endif
    </div>

  </div>
</div>

<!-- ===================================================
     ADD TABLE MODAL
     =================================================== -->
<div class="modal fade" id="addTableModal" tabindex="-1" role="dialog" aria-labelledby="addTableModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content modal-pos-content">
      <form action="{{ route('table.manage.insert') }}" method="POST">
        @csrf
        <div class="modal-pos-header">
          <h5 class="modal-pos-title" id="addTableModalLabel">
            <i class="fa-solid fa-plus-circle text-warning"></i> Add New Floor Table
          </h5>
          <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
        </div>

        <div class="modal-pos-body">
          <div class="modal-info-callout">
            <i class="fa-solid fa-wand-magic-sparkles fa-lg mt-1 text-success"></i>
            <div>
              <strong>Instant Branded Standee:</strong> An ultra-crisp SVG QR standee card featuring your <strong>Restaurant Logo</strong> on top and <strong>Table Name</strong> below will be automatically generated upon creation.
            </div>
          </div>

          <div class="row g-3">
            <div class="col-12 mb-3">
              <label class="form-pos-label">
                Table Name / Number <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" class="form-pos-control" required placeholder="e.g. Table 01, VIP Rooftop 4, Patio Table A">
              <small class="text-muted">This label will be printed directly at the bottom of the QR standee.</small>
            </div>

            <div class="col-12">
              <label class="form-pos-label">
                Seating Capacity / Location Notes <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <textarea name="description" class="form-pos-control" rows="3" placeholder="e.g. 4-Seater near window, Indoor AC section"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-pos-footer">
          <button type="button" class="btn-pos-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-pos-primary">
            <i class="fa-solid fa-plus-circle"></i> Save &amp; Generate Standee
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ===================================================
     EDIT TABLE MODAL
     =================================================== -->
<div class="modal fade" id="editTableModal" tabindex="-1" role="dialog" aria-labelledby="editTableModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content modal-pos-content">
      <form action="{{ route('table.manage.update') }}" method="POST">
        @csrf
        <input type="hidden" name="id" id="edit_id">
        
        <div class="modal-pos-header">
          <h5 class="modal-pos-title" id="editTableModalLabel">
            <i class="fa-solid fa-pen-to-square text-warning"></i> Edit Table Information
          </h5>
          <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
        </div>

        <div class="modal-pos-body">
          <div class="modal-info-callout" style="background:#eff6ff; border-color:#bfdbfe; color:#1e40af;">
            <i class="fa-solid fa-arrows-rotate fa-lg mt-1 text-primary"></i>
            <div>
              <strong>Automatic QR Sync:</strong> Modifying the table name will instantly refresh the branded QR Standee with the updated title.
            </div>
          </div>

          <div class="row g-3">
            <div class="col-12 mb-3">
              <label class="form-pos-label">
                Table Name / Number <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" id="edit_name" class="form-pos-control" required placeholder="e.g. Table 01">
            </div>

            <div class="col-12">
              <label class="form-pos-label">
                Seating Capacity / Location Notes <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <textarea name="description" id="edit_description" class="form-pos-control" rows="3" placeholder="Enter seating notes or zone info"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-pos-footer">
          <button type="button" class="btn-pos-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-pos-primary">
            <i class="fa-solid fa-floppy-disk"></i> Update &amp; Refresh QR
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ===================================================
     STANDEE FULL PREVIEW MODAL
     =================================================== -->
<div class="modal fade" id="standeePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
    <div class="modal-content modal-pos-content">
      <div class="modal-pos-header">
        <h5 class="modal-pos-title" id="standeePreviewTitle">
          <i class="fa-solid fa-qrcode text-warning"></i> Branded QR Standee
        </h5>
        <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
      </div>

      <div class="modal-pos-body text-center">
        <div class="standee-preview-card mb-3">
          <img id="standeePreviewImg" src="" class="standee-preview-img" alt="Branded Standee Preview">
        </div>
        <p class="text-muted small mb-0">
          <i class="fa-solid fa-print me-1"></i> Print ready vector graphic (SVG). Scale to any size without blur.
        </p>
      </div>

      <div class="modal-pos-footer justify-content-between">
        <button type="button" class="btn-pos-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
        <a id="standeeDownloadLink" href="" download="" class="btn-pos-primary">
          <i class="fa-solid fa-arrow-down-to-bracket"></i> Download SVG File
        </a>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
  // Initialize DataTables with clean options
  if ($('#tableManage').length) {
    $('#tableManage').DataTable({
      responsive: true,
      pageLength: 25,
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search floor tables...",
        lengthMenu: "Show _MENU_ entries"
      }
    });
  }

  // Edit Table modal trigger
  $(document).on('click', '.editBtn', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var description = $(this).data('description');

    $('#edit_id').val(id);
    $('#edit_name').val(name);
    $('#edit_description').val(description);

    $('#editTableModal').modal('show');
  });

  // Standee Full Preview Modal
  $(document).on('click', '.viewStandeeBtn', function() {
    var src = $(this).data('src');
    var title = $(this).data('title');
    var downloadUrl = $(this).data('download');
    var filename = $(this).data('filename');

    $('#standeePreviewImg').attr('src', src);
    $('#standeePreviewTitle').html('<i class="fa-solid fa-qrcode text-warning me-1"></i> ' + title);
    $('#standeeDownloadLink').attr('href', downloadUrl).attr('download', filename);

    $('#standeePreviewModal').modal('show');
  });

  // Prevent double submissions with loading spinner
  $('form').on('submit', function() {
    var form = $(this);
    var submitBtn = form.find('button[type="submit"]');
    submitBtn.prop('disabled', true);

    if (submitBtn.text().toLowerCase().indexOf('update') !== -1) {
      submitBtn.html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Updating &amp; Syncing...');
    } else {
      submitBtn.html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Generating QR...');
    }
  });
});
</script>

</body>
</html>
