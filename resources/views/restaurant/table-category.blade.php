<!DOCTYPE html>
<html lang="en">
<head>
  <title>Table Category Master • Bill&Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  
  <!-- Table Management Design CSS -->
  <link rel="stylesheet" href="{{ asset('admin_template/css/table-manage.css') }}?v={{ time() }}">

  <style>
    .cat-badge-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: 9999px;
      font-size: 0.85rem;
      font-weight: 700;
      background: rgba(255, 94, 20, 0.1);
      color: #ff5e14;
      border: 1px solid rgba(255, 94, 20, 0.22);
    }
    .table-count-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 5px 12px;
      border-radius: 8px;
      font-size: 0.82rem;
      font-weight: 600;
      background: #f1f5f9;
      color: #334155;
      border: 1px solid #e2e8f0;
    }
    .table-count-pill.has-tables {
      background: rgba(2, 132, 199, 0.1);
      color: #0284c7;
      border-color: rgba(2, 132, 199, 0.25);
    }
    .cat-desc-preview {
      max-width: 380px;
      color: #475569;
      font-size: 0.9rem;
      line-height: 1.45;
    }
  </style>
</head>
<body>
@include('includes.sidebar')

@php
  $catList = $categories ?? collect([]);
  $totalCats = count($catList);
  $activeCats = $catList->where('status', 'A')->count();
  $inactiveCats = $catList->where('status', 'I')->count();
  $totalTablesCount = $totalTablesCount ?? 0;
  $generalTablesCount = $generalTablesCount ?? 0;
@endphp

<div class="pc-container">
  <div class="pc-content">
    
    <!-- Hero Header Architecture -->
    <div class="table-header-card">
      <div class="table-header-left">
        <div class="table-header-icon" style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);">
          <i class="fa-solid fa-layer-group"></i>
        </div>
        <div class="table-header-meta">
          <span class="table-eyebrow-tag"><i class="fa-solid fa-chair me-1"></i> Floor &amp; Seating Management</span>
          <h1 class="table-main-title">Table Category Master</h1>
          <p class="table-sub-text">
            @if(isset($restaurant))
              <span class="text-white fw-bold"><i class="fa-solid fa-store text-warning me-1"></i> {{ $restaurant->name }}</span>
              <span class="opacity-75">&bull; Group dining tables into zones like AC Hall, VIP Lounge, Rooftop, Garden &amp; Family</span>
            @else
              <span>Group dining tables into zones (e.g. AC Section, Rooftop, Outdoor, VIP Lounge)</span>
            @endif
          </p>
        </div>
      </div>
      
      <div class="table-header-actions">
        <a href="{{ route('table.manage') }}" class="btn-header-secondary" title="Switch to Floor Tables Directory">
          <i class="fa-solid fa-chair"></i> Manage Tables
        </a>

        @if(auth()->user()->hasPermission('table_master', 'add'))
          <button type="button" class="btn-header-primary" data-toggle="modal" data-target="#addCategoryModal" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fa-solid fa-plus-circle"></i> Add Table Category
          </button>
        @endif
      </div>
    </div>

    <!-- Alert Messages -->
    @include('includes.message')

    <!-- KPI Live Stats Deck -->
    <div class="table-kpi-grid">
      <div class="table-kpi-card kpi-table-total">
        <div class="table-kpi-icon">
          <i class="fa-solid fa-layer-group"></i>
        </div>
        <div class="table-kpi-info">
          <span class="table-kpi-number">{{ $totalCats }}</span>
          <span class="table-kpi-label">Total Categories</span>
        </div>
      </div>

      <div class="table-kpi-card kpi-table-active">
        <div class="table-kpi-icon">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="table-kpi-info">
          <span class="table-kpi-number">{{ $activeCats }}</span>
          <span class="table-kpi-label">Active Categories</span>
        </div>
      </div>

      <div class="table-kpi-card kpi-table-quota">
        <div class="table-kpi-icon">
          <i class="fa-solid fa-chair"></i>
        </div>
        <div class="table-kpi-info">
          <span class="table-kpi-number">{{ $totalTablesCount }}</span>
          <span class="table-kpi-label">Total Tables Active</span>
        </div>
      </div>

      <div class="table-kpi-card kpi-table-inactive">
        <div class="table-kpi-icon">
          <i class="fa-solid fa-tag"></i>
        </div>
        <div class="table-kpi-info">
          <span class="table-kpi-number">{{ $generalTablesCount }}</span>
          <span class="table-kpi-label">General Category Tables</span>
        </div>
      </div>
    </div>

    <!-- Categories Data Card -->
    <div class="table-data-card">
      <div class="table-data-header">
        <h3 class="table-data-title">
          <i class="fa-solid fa-list-check text-primary"></i> Table Categories Directory
        </h3>
        <span class="text-muted small">
          <i class="fa-solid fa-circle-info text-info me-1"></i> Tables without a specific category automatically show as <strong>General Category</strong>.
        </span>
      </div>

      @if($totalCats > 0)
        <div class="table-responsive">
          <table id="categoryManage" class="table custom-pos-table nowrap">
            <thead>
              <tr>
                <th style="width: 60px;">#</th>
                <th>Category Name</th>
                <th>Description</th>
                <th style="width: 150px;">Tables Assigned</th>
                <th style="width: 120px;">Status</th>
                <th style="width: 130px;" class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($categories as $key => $cat)
              <tr>
                <td>
                  <span class="table-index-pill">{{ $key + 1 }}</span>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="cat-badge-pill">
                      <i class="fa-solid fa-layer-group"></i> {{ $cat->name }}
                    </span>
                    <span class="table-id-tag">#{{ $cat->id }}</span>
                  </div>
                </td>
                <td>
                  @if(!empty($cat->description))
                    <div class="cat-desc-preview">{{ $cat->description }}</div>
                  @else
                    <span class="text-muted small"><em>No description provided</em></span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('table.manage') }}" class="table-count-pill {{ ($cat->tables_count ?? 0) > 0 ? 'has-tables' : '' }}" title="View tables under this category">
                    <i class="fa-solid fa-chair"></i>
                    <span>{{ $cat->tables_count ?? 0 }} {{ Str::plural('Table', $cat->tables_count ?? 0) }}</span>
                  </a>
                </td>
                <td>
                  @if(auth()->user()->hasPermission('table_master', 'edit'))
                    <a href="{{ route('table.category.status', $cat->id) }}"
                       onclick="return confirm('Change status for category \'{{ addslashes($cat->name) }}\'?')"
                       class="table-status-pill {{ $cat->status == 'A' ? 'active' : 'inactive' }}"
                       title="Click to toggle status">
                       <i class="fa-solid {{ $cat->status == 'A' ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                       {{ $cat->status == 'A' ? 'Active' : 'Inactive' }}
                    </a>
                  @else
                    <span class="table-status-pill {{ $cat->status == 'A' ? 'active' : 'inactive' }}" style="cursor: default;">
                       <i class="fa-solid {{ $cat->status == 'A' ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                       {{ $cat->status == 'A' ? 'Active' : 'Inactive' }}
                    </span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="table-actions-dock justify-content-center">
                    @if(auth()->user()->hasPermission('table_master', 'edit'))
                      <button type="button" 
                              class="table-action-btn edit-btn editCatBtn"
                              data-bs-toggle="modal"
                              data-bs-target="#editCategoryModal"
                              data-toggle="modal"
                              data-target="#editCategoryModal"
                              data-id="{{ $cat->id }}"
                              data-name="{{ $cat->name }}"
                              data-description="{{ $cat->description }}"
                              title="Edit Category">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                    @endif

                    @if(auth()->user()->hasPermission('table_master', 'delete'))
                      <a href="{{ route('table.category.delete', $cat->id) }}"
                         onclick="return confirm('Delete table category \'{{ addslashes($cat->name) }}\'? Any assigned tables will fall back to General Category.')"
                         class="table-action-btn delete-btn"
                         title="Delete Category">
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
          <div class="table-empty-icon" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
            <i class="fa-solid fa-layer-group"></i>
          </div>
          <h4 class="table-empty-title">No Table Categories Yet</h4>
          <p class="table-empty-desc">Create categories like <strong>AC Section</strong>, <strong>Rooftop</strong>, <strong>Garden Lounge</strong>, or <strong>Family Hall</strong> to organize dining areas across your restaurant.</p>
          @if(auth()->user()->hasPermission('table_master', 'add'))
            <button type="button" class="btn-pos-primary" data-toggle="modal" data-target="#addCategoryModal" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
              <i class="fa-solid fa-plus-circle me-1"></i> Add First Table Category
            </button>
          @endif
        </div>
      @endif
    </div>

  </div>
</div>

<!-- ===================================================
     ADD TABLE CATEGORY MODAL
     =================================================== -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content modal-pos-content">
      <form action="{{ route('table.category.insert') }}" method="POST">
        @csrf
        <div class="modal-pos-header">
          <h5 class="modal-pos-title" id="addCategoryModalLabel">
            <i class="fa-solid fa-plus-circle text-warning"></i> Add New Table Category
          </h5>
          <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
        </div>

        <div class="modal-pos-body">
          <div class="modal-info-callout">
            <i class="fa-solid fa-layer-group fa-lg mt-1 text-primary"></i>
            <div>
              <strong>Seating Organization:</strong> Use table categories to categorize dining areas in floor plans, rapid billing, and QR ordering.
            </div>
          </div>

          <div class="row g-3">
            <div class="col-12 mb-3">
              <label class="form-pos-label">
                Category Name <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" class="form-pos-control" required placeholder="e.g. AC Section, VIP Lounge, Rooftop, Garden">
              <small class="text-muted">Enter a unique name for this dining area or zone.</small>
            </div>

            <div class="col-12">
              <label class="form-pos-label">
                Description <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <textarea name="description" class="form-pos-control" rows="3" placeholder="e.g. First floor air-conditioned dining area with window view"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-pos-footer">
          <button type="button" class="btn-pos-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-pos-primary">
            <i class="fa-solid fa-plus-circle"></i> Save Category
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ===================================================
     EDIT TABLE CATEGORY MODAL
     =================================================== -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content modal-pos-content">
      <form action="{{ route('table.category.update') }}" method="POST">
        @csrf
        <input type="hidden" name="id" id="edit_cat_id">
        
        <div class="modal-pos-header">
          <h5 class="modal-pos-title" id="editCategoryModalLabel">
            <i class="fa-solid fa-pen-to-square text-warning"></i> Edit Table Category
          </h5>
          <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
        </div>

        <div class="modal-pos-body">
          <div class="row g-3">
            <div class="col-12 mb-3">
              <label class="form-pos-label">
                Category Name <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" id="edit_cat_name" class="form-pos-control" required placeholder="e.g. AC Section">
            </div>

            <div class="col-12">
              <label class="form-pos-label">
                Description <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <textarea name="description" id="edit_cat_description" class="form-pos-control" rows="3" placeholder="Enter optional notes"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-pos-footer">
          <button type="button" class="btn-pos-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-pos-primary">
            <i class="fa-solid fa-floppy-disk"></i> Update Category
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
  // Initialize DataTables
  if ($('#categoryManage').length) {
    $('#categoryManage').DataTable({
      responsive: true,
      pageLength: 25,
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search table categories...",
        lengthMenu: "Show _MENU_ entries"
      }
    });
  }

  // Edit Category Modal trigger
  $(document).on('click', '.editCatBtn', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var description = $(this).data('description');

    $('#edit_cat_id').val(id);
    $('#edit_cat_name').val(name);
    $('#edit_cat_description').val(description || '');

    var modalEl = document.getElementById('editCategoryModal');
    if (window.bootstrap && bootstrap.Modal) {
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else if ($.fn.modal) {
      $('#editCategoryModal').modal('show');
    }
  });

  // Submit button spinner prevention
  $('form').on('submit', function() {
    var form = $(this);
    var submitBtn = form.find('button[type="submit"]');
    submitBtn.prop('disabled', true);

    if (submitBtn.text().toLowerCase().indexOf('update') !== -1) {
      submitBtn.html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Updating...');
    } else {
      submitBtn.html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Saving...');
    }
  });
});
</script>

</body>
</html>
