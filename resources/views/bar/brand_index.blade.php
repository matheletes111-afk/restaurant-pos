<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bar Brand Management • Bill&amp;Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

  <style>
    :root {
      --bar-primary: #8b5cf6;
      --bar-primary-hover: #7c3aed;
      --bar-gradient: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #ec4899 100%);
      --bar-dark: #0f172a;
      --card-bg: #ffffff;
      --border-color: #e2e8f0;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --radius-sm: 8px;
      --radius-md: 14px;
      --radius-lg: 18px;
    }

    * { box-sizing: border-box; }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: #f6f7fb;
      color: var(--text-main);
      overflow-x: hidden;
    }

    .pc-content {
      padding: 24px 28px;
    }

    @media (max-width: 768px) {
      .pc-content { padding: 16px 14px; }
    }

    /* Header Deck */
    .brand-header-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      padding: 22px 26px;
      margin-bottom: 22px;
      box-shadow: 0 4px 20px rgba(124, 58, 237, 0.05);
      border: 1px solid #ede9fe;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }

    .header-left-cluster {
      display: flex;
      align-items: center;
      gap: 16px;
      flex: 1 1 auto;
      min-width: 240px;
    }

    .brand-header-icon {
      width: 54px;
      height: 54px;
      border-radius: 14px;
      background: var(--bar-gradient);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      box-shadow: 0 8px 22px rgba(124, 58, 237, 0.35);
      flex-shrink: 0;
    }

    .brand-main-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.45rem;
      font-weight: 800;
      color: var(--bar-dark);
      margin: 0 0 2px 0;
      letter-spacing: -0.02em;
      line-height: 1.25;
    }

    /* KPI Cards Deck */
    .kpi-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }

    .kpi-card {
      background: #ffffff;
      border-radius: var(--radius-md);
      padding: 18px 20px;
      border: 1px solid #f1f5f9;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
      display: flex;
      align-items: center;
      gap: 16px;
      transition: all 0.2s ease;
    }

    .kpi-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(124, 58, 237, 0.08);
      border-color: #ddd6fe;
    }

    .kpi-icon {
      width: 46px;
      height: 46px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
      flex-shrink: 0;
    }

    .kpi-icon.purple { background: #faf5ff; color: #7c3aed; }
    .kpi-icon.green  { background: #ecfdf5; color: #10b981; }
    .kpi-icon.blue   { background: #eff6ff; color: #3b82f6; }
    .kpi-icon.amber  { background: #fffbeb; color: #f59e0b; }

    .kpi-val {
      font-family: 'Outfit', sans-serif;
      font-size: 1.55rem;
      font-weight: 800;
      color: var(--bar-dark);
      line-height: 1.1;
    }

    .kpi-label {
      font-size: 0.78rem;
      font-weight: 700;
      color: #64748b;
      margin-top: 3px;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    /* Action Buttons */
    .btn-bar-primary {
      background: var(--bar-gradient);
      border: none;
      color: #ffffff;
      padding: 9px 20px;
      border-radius: 30px;
      font-size: 0.88rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 4px 16px rgba(124, 58, 237, 0.32);
      transition: all 0.25s ease;
      cursor: pointer;
      text-decoration: none;
    }

    .btn-bar-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(124, 58, 237, 0.45);
      color: #ffffff;
    }

    .btn-bar-secondary {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      color: #475569;
      padding: 9px 18px;
      border-radius: 30px;
      font-size: 0.86rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.2s ease;
      cursor: pointer;
      text-decoration: none;
    }

    .btn-bar-secondary:hover {
      border-color: var(--bar-primary);
      color: var(--bar-primary);
      background: #faf5ff;
    }

    /* Table Container */
    .brand-table-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      border: 1px solid #f1f5f9;
      padding: 20px 24px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .brand-avatar-circle {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
      color: #ffffff;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      font-family: 'Outfit', sans-serif;
    }

    /* Modal Form Controls */
    .form-control-custom {
      background: #f8fafc;
      border: 1.5px solid #e2e8f0;
      border-radius: 12px;
      padding: 10px 16px;
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--text-main);
      width: 100%;
    }

    .form-control-custom:focus {
      background: #ffffff;
      border-color: var(--bar-primary);
      box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
      outline: none;
    }

    .btn-modal-cancel {
      background: #f1f5f9;
      border: 1px solid #e2e8f0;
      color: #475569;
      border-radius: 30px;
      padding: 9px 20px;
      font-size: 0.86rem;
      font-weight: 700;
      cursor: pointer;
    }
  </style>
</head>

<body data-pc-theme="light">
  <div class="loader-bg">
    <div class="loader-track"><div class="loader-fill"></div></div>
  </div>

  @include('includes.sidebar')

  <div class="pc-container">
    <div class="pc-content">

      <!-- Header Card -->
      <div class="brand-header-card">
        <div class="header-left-cluster">
          <div class="brand-header-icon">
            <i class="fa-solid fa-certificate"></i>
          </div>
          <div>
            <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--bar-primary); letter-spacing: 0.08em; margin-bottom: 2px;">
              <i class="fa-solid fa-martini-glass me-1"></i> Bar Management &bull; Liquor Brands
            </div>
            <h1 class="brand-main-title">Bar Brand Management</h1>
            <p style="font-size: 0.82rem; color: #64748b; margin: 0;">Create and organize distillery, brewery &amp; winery liquor brands with Excel upload.</p>
          </div>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <a href="{{ route('bar.item.index') }}" class="btn-bar-secondary">
            <i class="fa-solid fa-wine-bottle"></i> Manage Bar Items
          </a>
          <button type="button" class="btn-bar-secondary" data-bs-toggle="modal" data-bs-target="#bulkUploadBrandModal">
            <i class="fa-solid fa-file-excel text-success"></i> Bulk Excel Upload
          </button>
          <button type="button" class="btn-bar-primary" data-bs-toggle="modal" data-bs-target="#addBrandModal">
            <i class="fa-solid fa-plus"></i> Add Bar Brand
          </button>
        </div>
      </div>

      <!-- KPI Summary Cards -->
      <div class="kpi-row">
        <div class="kpi-card">
          <div class="kpi-icon purple"><i class="fa-solid fa-certificate"></i></div>
          <div>
            <div class="kpi-val">{{ $stats['total'] }}</div>
            <div class="kpi-label">Total Brands</div>
          </div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon green"><i class="fa-solid fa-circle-check"></i></div>
          <div>
            <div class="kpi-val">{{ $stats['active'] }}</div>
            <div class="kpi-label">Active Brands</div>
          </div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon blue"><i class="fa-solid fa-wine-bottle"></i></div>
          <div>
            <div class="kpi-val">{{ $stats['with_items'] }}</div>
            <div class="kpi-label">Brands with Items</div>
          </div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon amber"><i class="fa-solid fa-pause-circle"></i></div>
          <div>
            <div class="kpi-val">{{ $stats['inactive'] }}</div>
            <div class="kpi-label">Inactive</div>
          </div>
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      <!-- Brands DataTable -->
      <div class="brand-table-card">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="brandsTable">
            <thead>
              <tr style="background: #f8fafc; font-size: 0.78rem; text-transform: uppercase; color: #475569; letter-spacing: 0.05em;">
                <th style="border-radius: 8px 0 0 8px;">#ID</th>
                <th>Brand Name</th>
                <th>Description</th>
                <th class="text-center">Linked Products</th>
                <th class="text-center">Status</th>
                <th class="text-end" style="border-radius: 0 8px 8px 0;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($brands as $brand)
              <tr>
                <td class="font-monospace text-muted fw-bold" style="font-size: 0.8rem;">#BND-{{ $brand->id }}</td>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    @if($brand->image)
                      <img src="{{ URL::to('storage/brands/' . $brand->image) }}" class="rounded-3" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $brand->name }}">
                    @else
                      <div class="brand-avatar-circle">
                        {{ strtoupper(substr($brand->name, 0, 1)) }}
                      </div>
                    @endif
                    <div>
                      <div style="font-weight: 800; color: #0f172a; font-size: 0.95rem;">{{ $brand->name }}</div>
                      <small class="text-muted" style="font-size: 0.74rem;">Created {{ $brand->created_at ? $brand->created_at->diffForHumans() : 'N/A' }}</small>
                    </div>
                  </div>
                </td>
                <td style="max-width: 320px;">
                  <span style="font-size: 0.84rem; color: #64748b;">
                    {{ $brand->description ? Str::limit($brand->description, 70) : '—' }}
                  </span>
                </td>
                <td class="text-center">
                  <a href="{{ route('bar.item.index') }}?brand={{ $brand->id }}" class="badge" style="background: #faf5ff; color: #7c3aed; border: 1px solid #e9d5ff; font-weight: 800; font-size: 0.78rem; padding: 5px 10px; border-radius: 20px; text-decoration: none;">
                    <i class="fa-solid fa-wine-bottle me-1"></i> {{ $brand->items_count }} {{ $brand->items_count == 1 ? 'Item' : 'Items' }}
                  </a>
                </td>
                <td class="text-center">
                  @if($brand->status == 'A')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="border-radius: 20px; font-weight: 800; font-size: 0.72rem;">Active</span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1" style="border-radius: 20px; font-weight: 800; font-size: 0.72rem;">Inactive</span>
                  @endif
                </td>
                <td class="text-end">
                  <div class="d-flex align-items-center justify-content-end gap-2">
                    <button type="button" 
                            class="btn btn-sm btn-outline-secondary editBrandBtn"
                            data-id="{{ $brand->id }}"
                            data-name="{{ $brand->name }}"
                            data-description="{{ $brand->description }}"
                            data-status="{{ $brand->status }}"
                            style="border-radius: 8px; padding: 4px 10px;"
                            title="Edit Brand">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <a href="{{ route('bar.brand.delete', $brand->id) }}" 
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Are you sure you want to delete brand \'{{ addslashes($brand->name) }}\'?')"
                       style="border-radius: 8px; padding: 4px 10px;"
                       title="Delete Brand">
                      <i class="fa-solid fa-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="fa-solid fa-certificate fs-1 d-block mb-3 text-secondary opacity-50"></i>
                  <h6 class="fw-bold">No Bar Brands Found</h6>
                  <p class="small text-muted mb-3">Add your first distillery or brewery brand to start categorizing items.</p>
                  <button type="button" class="btn-bar-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                    <i class="fa-solid fa-plus"></i> Add Brand Now
                  </button>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

  <!-- MODAL: ADD BRAND -->
  <div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" action="{{ route('bar.brand.store') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <div class="modal-content" style="border-radius: var(--radius-lg); overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
          <div class="modal-header" style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 18px 22px;">
            <h5 class="modal-title font-weight-bold" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.15rem;">
              <i class="fa-solid fa-plus-circle text-primary me-2"></i> Add New Bar Brand
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" style="padding: 20px 22px;">
            <div class="mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Brand Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control-custom" required placeholder="e.g. Johnnie Walker, Glenfiddich, Grey Goose" autocomplete="off">
            </div>
            <div class="mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Description <small class="text-muted fw-normal">(Optional)</small></label>
              <textarea name="description" class="form-control-custom" rows="3" placeholder="Distillery details, origin country, brand notes..."></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Brand Logo / Image <small class="text-muted fw-normal">(Optional)</small></label>
              <input type="file" name="image" class="form-control-custom" accept="image/*">
            </div>
            <div class="mb-2">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Status</label>
              <select name="status" class="form-control-custom">
                <option value="A" selected>Active</option>
                <option value="I">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 22px;">
            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-bar-primary">Save Brand</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: EDIT BRAND -->
  <div class="modal fade" id="editBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" action="{{ route('bar.brand.update') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <input type="hidden" name="id" id="editBrandId">
        <div class="modal-content" style="border-radius: var(--radius-lg); overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
          <div class="modal-header" style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 18px 22px;">
            <h5 class="modal-title font-weight-bold" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.15rem;">
              <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Bar Brand
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" style="padding: 20px 22px;">
            <div class="mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Brand Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="editBrandName" class="form-control-custom" required autocomplete="off">
            </div>
            <div class="mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Description <small class="text-muted fw-normal">(Optional)</small></label>
              <textarea name="description" id="editBrandDesc" class="form-control-custom" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Change Logo / Image <small class="text-muted fw-normal">(Optional)</small></label>
              <input type="file" name="image" class="form-control-custom" accept="image/*">
            </div>
            <div class="mb-2">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Status</label>
              <select name="status" id="editBrandStatus" class="form-control-custom">
                <option value="A">Active</option>
                <option value="I">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 22px;">
            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-bar-primary">Update Brand</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: BULK UPLOAD BRANDS -->
  <div class="modal fade" id="bulkUploadBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" action="{{ route('bar.brand.bulk.upload') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <div class="modal-content" style="border-radius: var(--radius-lg); overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
          <div class="modal-header" style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 18px 22px;">
            <h5 class="modal-title font-weight-bold" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.15rem;">
              <i class="fa-solid fa-file-excel text-success me-2"></i> Bulk Upload Bar Brands
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" style="padding: 20px 22px;">
            <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 14px;">
              Upload an Excel (.xlsx, .xls) or CSV spreadsheet containing brand names and descriptions.
            </p>
            <div class="alert alert-light border mb-3" style="border-radius: 12px; background: #faf5ff; border-color: #ddd6fe !important;">
              <div class="d-flex justify-content-between align-items-center">
                <span style="font-size: 0.8rem; font-weight: 700; color: #6d28d9;">
                  <i class="fa-solid fa-download me-1"></i> Pre-Formatted Template
                </span>
                <a href="{{ route('bar.brand.template') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 20px; font-weight: 700; font-size: 0.75rem;">
                  Download .xlsx
                </a>
              </div>
            </div>
            <div class="mb-2">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Select Spreadsheet <span class="text-danger">*</span></label>
              <input type="file" name="bulk_file" class="form-control-custom" required accept=".xlsx,.xls,.csv">
            </div>
          </div>
          <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 22px;">
            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-bar-primary"><i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload &amp; Import</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  @include('includes.script')
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <script>
    $(document).ready(function() {
      if ($('#brandsTable tbody tr').length > 0 && !$('#brandsTable tbody tr td[colspan]').length) {
        $('#brandsTable').DataTable({
          pageLength: 25,
          ordering: true,
          order: [[0, 'desc']],
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search brands..."
          }
        });
      }

      $('.editBrandBtn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const description = $(this).data('description') || '';
        const status = $(this).data('status');

        $('#editBrandId').val(id);
        $('#editBrandName').val(name);
        $('#editBrandDesc').val(description);
        $('#editBrandStatus').val(status);

        const modal = new bootstrap.Modal(document.getElementById('editBrandModal'));
        modal.show();
      });
    });
  </script>
</body>
</html>
