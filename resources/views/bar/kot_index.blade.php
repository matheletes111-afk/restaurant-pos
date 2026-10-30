<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bar KOT Counter • Bill&amp;Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    :root {
      --bar-primary: #8b5cf6;
      --bar-primary-hover: #7c3aed;
      --bar-gradient: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #ec4899 100%);
      --bar-dark: #0f172a;
      --bar-amber: #f59e0b;
      --bar-green: #10b981;
      --bar-border: #e2e8f0;
      --bar-card-bg: #ffffff;
      --radius-sm: 8px;
      --radius-md: 14px;
      --radius-lg: 18px;
    }

    * { box-sizing: border-box; }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: #f8fafc;
      color: #1e293b;
      overflow-x: hidden;
    }

    .pc-content {
      padding: 22px 26px;
    }

    @media (max-width: 768px) {
      .pc-content { padding: 14px 12px; }
    }

    /* Top Header Deck */
    .bar-header-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      padding: 20px 24px;
      margin-bottom: 20px;
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
    }

    .bar-header-icon {
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

    .bar-main-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.45rem;
      font-weight: 800;
      color: var(--bar-dark);
      margin: 0 0 2px 0;
      letter-spacing: -0.02em;
    }

    .btn-bar-action {
      background: #ffffff;
      border: 1.5px solid #cbd5e1;
      color: #475569;
      padding: 8px 16px;
      border-radius: 30px;
      font-size: 0.84rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
    }
    .btn-bar-action:hover {
      background: #f1f5f9;
      border-color: #94a3b8;
      color: #0f172a;
    }

    .btn-bar-primary {
      background: var(--bar-gradient);
      color: #ffffff;
      border: none;
      padding: 9px 18px;
      border-radius: 30px;
      font-size: 0.86rem;
      font-weight: 700;
      box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-bar-primary:hover {
      box-shadow: 0 8px 22px rgba(124, 58, 237, 0.45);
      color: #ffffff;
    }

    /* Live Sync Pill */
    .live-sync-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 0.78rem;
      font-weight: 700;
    }

    .pulse-dot {
      width: 8px;
      height: 8px;
      background: #10b981;
      border-radius: 50%;
      box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
      animation: pulseGreen 1.6s infinite;
    }

    @keyframes pulseGreen {
      0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
      70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
      100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* KPI Deck */
    .kpi-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
      gap: 16px;
      margin-bottom: 20px;
    }

    .kpi-card {
      background: #ffffff;
      border-radius: var(--radius-md);
      padding: 16px 20px;
      border: 1.5px solid #f1f5f9;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
      display: flex;
      align-items: center;
      gap: 14px;
      transition: all 0.2s ease;
      cursor: pointer;
    }
    .kpi-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(124, 58, 237, 0.08);
    }
    .kpi-card.active {
      border-color: #8b5cf6;
      background: #faf5ff;
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
    .kpi-icon.amber  { background: #fffbeb; color: #d97706; }
    .kpi-icon.green  { background: #ecfdf5; color: #059669; }
    .kpi-icon.blue   { background: #eff6ff; color: #2563eb; }

    .kpi-val {
      font-family: 'Outfit', sans-serif;
      font-size: 1.45rem;
      font-weight: 800;
      color: var(--bar-dark);
      line-height: 1.1;
    }
    .kpi-label {
      font-size: 0.74rem;
      font-weight: 700;
      color: #64748b;
      margin-top: 2px;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    /* Filter Card */
    .filter-card {
      background: #ffffff;
      border-radius: var(--radius-md);
      padding: 16px 20px;
      margin-bottom: 20px;
      border: 1px solid var(--bar-border);
      box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }

    /* KOT Grid */
    .kot-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 20px;
    }

    .kot-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      border: 1.5px solid var(--bar-border);
      box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transition: all 0.25s ease;
      position: relative;
    }
    .kot-card:hover {
      box-shadow: 0 10px 30px rgba(124, 58, 237, 0.09);
      border-color: #ddd6fe;
    }
    .kot-card.status-pending {
      border-top: 4px solid var(--bar-amber);
    }
    .kot-card.status-served {
      border-top: 4px solid var(--bar-green);
    }

    .kot-card-header {
      padding: 14px 18px;
      background: #fafafa;
      border-bottom: 1px solid #f1f5f9;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .kot-badge-num {
      font-family: 'JetBrains Mono', monospace;
      font-weight: 800;
      font-size: 0.95rem;
      color: #7c3aed;
      background: #f5f3ff;
      padding: 3px 10px;
      border-radius: 8px;
      border: 1px solid #ddd6fe;
    }

    .kot-table-tag {
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 0.95rem;
      color: #0f172a;
    }

    .kot-card-body {
      padding: 16px 18px;
      flex: 1 1 auto;
      display: flex;
      flex-direction: column;
    }

    .kot-item-row {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      padding: 10px 0;
      border-bottom: 1px dashed #e2e8f0;
    }
    .kot-item-row:last-child {
      border-bottom: none;
    }

    .kot-drink-name {
      font-weight: 700;
      font-size: 0.92rem;
      color: #0f172a;
      line-height: 1.25;
    }

    .kot-variation-tag {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-size: 0.75rem;
      font-weight: 700;
      background: #ede9fe;
      color: #6d28d9;
      padding: 2px 8px;
      border-radius: 6px;
      margin-top: 4px;
    }

    .kot-qty-badge {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      background: #f1f5f9;
      color: #0f172a;
      font-family: 'JetBrains Mono', monospace;
      font-weight: 800;
      font-size: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .kot-card-footer {
      padding: 12px 18px;
      background: #fafafa;
      border-top: 1px solid #f1f5f9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    /* Status Badges */
    .badge-status-pending {
      background: #fffbeb;
      color: #b45309;
      border: 1px solid #fde68a;
      font-weight: 800;
      font-size: 0.74rem;
      padding: 4px 10px;
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .badge-status-served {
      background: #ecfdf5;
      color: #047857;
      border: 1px solid #a7f3d0;
      font-weight: 800;
      font-size: 0.74rem;
      padding: 4px 10px;
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    /* Buttons */
    .btn-mark-served {
      background: #10b981;
      color: #ffffff;
      border: none;
      padding: 7px 14px;
      border-radius: 8px;
      font-size: 0.82rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
    }
    .btn-mark-served:hover {
      background: #059669;
      color: #ffffff;
    }

    .btn-mark-pending {
      background: #f59e0b;
      color: #ffffff;
      border: none;
      padding: 7px 14px;
      border-radius: 8px;
      font-size: 0.82rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
    }
    .btn-mark-pending:hover {
      background: #d97706;
      color: #ffffff;
    }

    .btn-kot-delete {
      border: 1.5px solid #fee2e2;
      background: #fff;
      color: #ef4444;
      padding: 6px 10px;
      border-radius: 8px;
      font-size: 0.82rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .btn-kot-delete:hover:not(:disabled) {
      background: #fee2e2;
      color: #b91c1c;
    }
    .btn-kot-delete:disabled {
      opacity: 0.45;
      cursor: not-allowed;
      border-color: #e2e8f0;
      color: #94a3b8;
    }

    .time-elapsed-pill {
      font-size: 0.72rem;
      color: #64748b;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 4px;
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

      <!-- ===================================================
           TOP HEADER
           =================================================== -->
      <div class="bar-header-card">
        <div class="header-left-cluster">
          <div class="bar-header-icon">
            <i class="fa-solid fa-martini-glass-citrus"></i>
          </div>
          <div>
            <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--bar-primary); letter-spacing: 0.08em; margin-bottom: 2px;">
              <i class="fa-solid fa-wine-glass me-1"></i> Bar Management &bull; Live KOT Counter
            </div>
            <h1 class="bar-main-title">Live Bar KOT Counter</h1>
            <p style="font-size: 0.82rem; color: #64748b; margin: 0;">
              Real-time liquor &amp; beverage orders for bartenders and bar counter staff.
            </p>
          </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
          <div class="live-sync-pill" title="Real-time Bar Orders Connected">
            <span class="pulse-dot"></span>
            <span>Live Auto-Sync</span>
          </div>

          <button type="button" class="btn-bar-action" id="soundToggleBtn">
            <i class="fa-solid fa-volume-high text-primary" id="soundIcon"></i>
            <span id="soundLabel">Sound On</span>
          </button>

          <button type="button" class="btn-bar-action" id="refreshBtn">
            <i class="fa-solid fa-rotate"></i> Refresh
          </button>
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      @php
        $kotList = $kotGroups ?? collect([]);
        $totalKotCount = $kotList->count();
        $pendingKotCount = $kotList->where('status', 'PENDING')->count();
        $servedKotCount = $kotList->where('status', 'SERVED')->count();
        $totalDrinksCount = count($barItems ?? []);
      @endphp

      <!-- ===================================================
           KPI SUMMARY CARDS
           =================================================== -->
      <div class="kpi-row">
        <div class="kpi-card {{ $selectedStatus == 'all' ? 'active' : '' }}" onclick="filterByStatus('all')">
          <div class="kpi-icon purple"><i class="fa-solid fa-receipt"></i></div>
          <div>
            <div class="kpi-val">{{ $totalKotCount }}</div>
            <div class="kpi-label">Total Bar KOTs</div>
          </div>
        </div>

        <div class="kpi-card {{ $selectedStatus == 'PENDING' ? 'active' : '' }}" onclick="filterByStatus('PENDING')">
          <div class="kpi-icon amber"><i class="fa-solid fa-clock"></i></div>
          <div>
            <div class="kpi-val text-warning">{{ $pendingKotCount }}</div>
            <div class="kpi-label">Pending / Pouring</div>
          </div>
        </div>

        <div class="kpi-card {{ $selectedStatus == 'SERVED' ? 'active' : '' }}" onclick="filterByStatus('SERVED')">
          <div class="kpi-icon green"><i class="fa-solid fa-circle-check"></i></div>
          <div>
            <div class="kpi-val text-success">{{ $servedKotCount }}</div>
            <div class="kpi-label">Served Drinks</div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon blue"><i class="fa-solid fa-wine-bottle"></i></div>
          <div>
            <div class="kpi-val">{{ $totalDrinksCount }}</div>
            <div class="kpi-label">Total Drink Items</div>
          </div>
        </div>
      </div>

      <!-- ===================================================
           FILTERS BAR
           =================================================== -->
      <div class="filter-card">
        <form method="GET" action="{{ route('bar.kot.index') }}" class="row g-2 align-items-center" id="filterForm">
          <input type="hidden" name="status" id="statusInput" value="{{ $selectedStatus }}">

          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mb-1">From Date</label>
            <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDateStr }}" onchange="this.form.submit()">
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mb-1">To Date</label>
            <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDateStr }}" onchange="this.form.submit()">
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mb-1">Filter by Table</label>
            <select name="table_id" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">All Tables &amp; Takeaway</option>
              @foreach($tables as $t)
                <option value="{{ $t->id }}" {{ $selectedTable == $t->id ? 'selected' : '' }}>
                  {{ $t->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3 text-end pt-3">
            <a href="{{ route('bar.kot.index') }}" class="btn btn-sm btn-outline-secondary">
              <i class="fa-solid fa-filter-circle-xmark me-1"></i> Reset Filters
            </a>
          </div>
        </form>
      </div>

      <!-- ===================================================
           KOT TICKETS GRID
           =================================================== -->
      <div class="kot-grid" id="barKotGrid">
        @forelse($kotList as $kot)
          @php
            $isServed = ($kot->status === 'SERVED');
            $statusClass = $isServed ? 'status-served' : 'status-pending';
            $timeAgo = \Carbon\Carbon::parse($kot->created_at)->diffForHumans();
          @endphp
          <div class="kot-card {{ $statusClass }}" id="card_{{ $kot->card_id }}">
            
            <div class="kot-card-header">
              <div>
                <span class="kot-badge-num">{{ $kot->kot_no ?: 'BAR-KOT' }}</span>
                <span class="ms-2 kot-table-tag">
                  <i class="fa-solid {{ $kot->is_takeaway ? 'fa-bag-shopping text-info' : 'fa-chair text-primary' }} me-1"></i>
                  {{ $kot->table_name }}
                </span>
              </div>
              <div>
                @if($isServed)
                  <span class="badge-status-served">
                    <i class="fa-solid fa-circle-check"></i> SERVED
                  </span>
                @else
                  <span class="badge-status-pending">
                    <i class="fa-solid fa-clock"></i> PENDING
                  </span>
                @endif
              </div>
            </div>

            <div class="kot-card-body">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small text-muted fw-bold">Guest: {{ $kot->customer_name }}</span>
                <span class="time-elapsed-pill" title="{{ $kot->created_at }}">
                  <i class="fa-solid fa-stopwatch"></i> {{ $timeAgo }}
                </span>
              </div>

              <div class="flex-grow-1">
                @foreach($kot->items as $item)
                  @php
                    $itemServed = ($item->order_status === 'SERVED');
                    $subcat = $item->subcategory;
                    $drinkName = $subcat ? $subcat->name : ($item->bar_variation_name ?: 'Liquor Item');
                    $varName = $item->bar_variation_name ?: ($item->barVariation ? $item->barVariation->name : ($subcat ? $subcat->bottle_size_ml . 'ml Bottle' : 'Standard Peg'));
                    $brand = $subcat && $subcat->brand ? $subcat->brand->name : null;
                  @endphp
                  <div class="kot-item-row" id="item_row_{{ $item->id }}">
                    <div class="kot-qty-badge">
                      {{ $item->quantity }}x
                    </div>
                    <div class="flex-grow-1">
                      <div class="kot-drink-name">
                        {{ $drinkName }}
                        @if($brand)
                          <small class="text-muted fw-normal">({{ $brand }})</small>
                        @endif
                      </div>
                      <div class="kot-variation-tag">
                        <i class="fa-solid fa-martini-glass"></i> {{ $varName }}
                      </div>
                      @if(floatval($item->vat_rate) > 0)
                        <span class="badge ms-1" style="background:#ede9fe; color:#6d28d9; font-size:0.68rem;">{{ floatval($item->vat_rate) }}% State VAT</span>
                      @endif
                    </div>
                    <div>
                      @if(!$itemServed)
                        <button type="button" 
                                class="btn-kot-delete deleteSingleItemBtn" 
                                data-id="{{ $item->id }}" 
                                title="Delete pending drink">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      @else
                        <button type="button" 
                                class="btn-kot-delete" 
                                disabled 
                                title="Served items cannot be deleted">
                          <i class="fa-solid fa-lock"></i>
                        </button>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

            <div class="kot-card-footer">
              <div>
                @if(!$isServed)
                  <button type="button" 
                          class="btn-kot-delete deleteKotTicketBtn" 
                          data-kot="{{ $kot->kot_no }}" 
                          data-order="{{ $kot->order_id }}" 
                          title="Cancel entire pending Bar KOT ticket">
                    <i class="fa-solid fa-trash me-1"></i> Cancel KOT
                  </button>
                @else
                  <span class="text-muted small fw-bold" title="Served items cannot be deleted">
                    <i class="fa-solid fa-lock me-1"></i> Served (Locked)
                  </span>
                @endif
              </div>

              <div>
                @if(!$isServed)
                  <button type="button" 
                          class="btn-mark-served updateKotStatusBtn" 
                          data-kot="{{ $kot->kot_no }}" 
                          data-order="{{ $kot->order_id }}" 
                          data-status="SERVED">
                    <i class="fa-solid fa-check-double"></i> Mark Served
                  </button>
                @else
                  <button type="button" 
                          class="btn-mark-pending updateKotStatusBtn" 
                          data-kot="{{ $kot->kot_no }}" 
                          data-order="{{ $kot->order_id }}" 
                          data-status="PENDING">
                    <i class="fa-solid fa-rotate-left"></i> Reopen Pending
                  </button>
                @endif
              </div>
            </div>

          </div>
        @empty
          <div style="grid-column: 1 / -1; background: #ffffff; border-radius: var(--radius-lg); padding: 50px 24px; text-align: center; border: 1.5px dashed #cbd5e1;">
            <i class="fa-solid fa-martini-glass-citrus fs-1 text-secondary opacity-50 mb-3 d-block"></i>
            <h5 class="fw-bold">No Active Bar KOT Orders Found</h5>
            <p class="text-muted small mb-0">New liquor and beverage orders from the POS will appear here instantly.</p>
          </div>
        @endforelse
      </div>

    </div>
  </div>

  <audio id="orderAudio" preload="auto">
    <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
  </audio>

  <!-- Core Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  @include('includes.script')

  <script>
    function filterByStatus(st) {
      $('#statusInput').val(st);
      $('#filterForm').submit();
    }

    $(document).ready(function() {
      let soundEnabled = true;

      $('#soundToggleBtn').on('click', function() {
        soundEnabled = !soundEnabled;
        if (soundEnabled) {
          $('#soundIcon').attr('class', 'fa-solid fa-volume-high text-primary');
          $('#soundLabel').text('Sound On');
        } else {
          $('#soundIcon').attr('class', 'fa-solid fa-volume-xmark text-danger');
          $('#soundLabel').text('Sound Off');
        }
      });

      $('#refreshBtn').on('click', function() {
        location.reload();
      });

      // Update KOT status (PENDING -> SERVED or vice-versa)
      $(document).on('click', '.updateKotStatusBtn', function() {
        const btn = $(this);
        const kotNo = btn.data('kot');
        const orderId = btn.data('order');
        const targetStatus = btn.data('status');

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Updating...');

        $.ajax({
          url: "{{ route('bar.kot.status.update') }}",
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            kot_no: kotNo,
            order_id: orderId,
            order_status: targetStatus
          },
          success: function(res) {
            location.reload();
          },
          error: function(xhr) {
            btn.prop('disabled', false).text('Retry');
            alert(xhr.responseJSON ? xhr.responseJSON.message : 'Error updating status');
          }
        });
      });

      // Delete Single Drink Item (only if pending)
      $(document).on('click', '.deleteSingleItemBtn', function() {
        const id = $(this).data('id');
        if (!confirm('Are you sure you want to remove this pending drink from the order?')) return;

        $.ajax({
          url: "{{ url('admin/bar/kot-counter/delete-item') }}/" + id,
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}"
          },
          success: function(res) {
            if (res.success) {
              $('#item_row_' + id).slideUp(200, function() { $(this).remove(); });
              setTimeout(function() { location.reload(); }, 600);
            } else {
              alert(res.message);
            }
          },
          error: function(xhr) {
            alert(xhr.responseJSON ? xhr.responseJSON.message : 'Failed to delete drink item.');
          }
        });
      });

      // Delete entire pending Bar KOT ticket
      $(document).on('click', '.deleteKotTicketBtn', function() {
        const kotNo = $(this).data('kot');
        const orderId = $(this).data('order');
        if (!confirm('Cancel all pending drinks in Bar KOT ' + kotNo + '?')) return;

        $.ajax({
          url: "{{ url('admin/bar/kot-counter/delete-item') }}",
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            kot_no: kotNo,
            order_id: orderId
          },
          success: function(res) {
            if (res.success) {
              location.reload();
            } else {
              alert(res.message);
            }
          },
          error: function(xhr) {
            alert(xhr.responseJSON ? xhr.responseJSON.message : 'Failed to cancel Bar KOT ticket.');
          }
        });
      });

      // Real-time polling for new pending bar orders every 10s
      setInterval(function() {
        $.ajax({
          url: "{{ route('bar.kot.refresh') }}",
          method: "GET",
          cache: false,
          success: function(res) {
            if (res.new_orders && res.count > 0) {
              if (soundEnabled) {
                const aud = document.getElementById('orderAudio');
                if (aud) aud.play().catch(function(e) {});
              }
              location.reload();
            }
          }
        });
      }, 10000);

    });
  </script>
</body>
</html>
