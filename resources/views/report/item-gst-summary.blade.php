@extends('layouts.app')

@section('title')
<title>Item GST Summary Report - Admin</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('admin_template/css/report-analytics.css') }}">
<style>
  .dataTables_wrapper {
    padding: 16px 20px;
  }
  .dataTables_filter input {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 6px 14px;
    outline: none;
    font-size: 0.85rem;
  }
  .dataTables_filter input:focus {
    border-color: var(--rpt-primary);
    background: #ffffff;
  }
  .dt-buttons .dt-button {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    padding: 6px 14px !important;
    font-size: 0.8rem !important;
    font-weight: 700 !important;
    color: var(--rpt-slate) !important;
    margin-right: 6px !important;
    transition: all 0.2s ease !important;
  }
  .dt-buttons .dt-button:hover {
    background: #f8fafc !important;
    border-color: var(--rpt-primary) !important;
    color: var(--rpt-primary) !important;
  }
  .table tfoot th {
    background: #f8fafc;
    border-top: 2px solid var(--rpt-border);
    font-weight: 800;
    color: var(--rpt-dark);
    font-size: 0.88rem;
  }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="rpt-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        {{-- 1. Header Deck --}}
        <div class="rpt-header-deck">
            <div class="rpt-header-left">
                <div class="rpt-header-icon icon-gst">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="rpt-header-title-meta">
                    <span class="rpt-header-eyebrow">Tax Audit & Statutory Ledger</span>
                    <h1 class="rpt-header-title">Item GST Summary Report</h1>
                    <p class="rpt-header-sub">Itemized breakdown of menu sales, taxable bases, and GST tax liability on paid orders</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btnExportExcel" class="btn-rpt-secondary">
                    <i class="fas fa-file-excel text-success"></i> Export Excel
                </button>
                <button type="button" id="btnPrintReport" class="btn-rpt-secondary">
                    <i class="fas fa-print text-primary"></i> Print
                </button>
            </div>
        </div>

        {{-- 2. Filter Form --}}
        <div class="rpt-filter-card">
            <form method="GET" action="{{ route('report.item.gst.summary') }}" class="row g-3 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label class="rpt-filter-label">From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="rpt-filter-label">To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-4 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn-rpt-primary flex-fill">
                        <i class="fas fa-search"></i> Generate Report
                    </button>
                    <a href="{{ route('report.item.gst.summary') }}" class="btn-rpt-secondary" title="Reset Filters">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 3. Summary Stats Grid --}}
        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-info">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Taxable Amount</span>
                    <span class="rpt-stat-val">₹{{ number_format($totals['total_taxable'], 2) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-success">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Item Discounts</span>
                    <span class="rpt-stat-val">₹{{ number_format($totals['total_discount'], 2) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-warning">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total GST Collected</span>
                    <span class="rpt-stat-val">₹{{ number_format($totals['total_gst'], 2) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-percent"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-purple">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Gross Final Amount</span>
                    <span class="rpt-stat-val">₹{{ number_format($totals['total_amount'], 2) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-calculator"></i>
                </div>
            </div>
        </div>

        {{-- 4. Item GST Table Card --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <h3 class="rpt-card-title">
                    <i class="fas fa-table-list text-primary"></i>
                    Itemized GST Breakdown
                    <span class="badge bg-light text-dark fw-bold border ms-2">{{ count($reportData) }} Records</span>
                </h3>
            </div>

            <div class="table-responsive">
                <table id="itemGSTTable" class="rpt-table" style="width:100%">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Invoice No</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Base Price</th>
                            <th class="text-center">Disc %</th>
                            <th class="text-end">Disc Amt</th>
                            <th class="text-end">Taxable Val</th>
                            <th class="text-center">GST Rate</th>
                            <th class="text-end">GST Amount</th>
                            <th class="text-end">Gross Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold text-dark font-monospace">{{ $item['invoice_no'] }}</span>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $item['item_name'] }}</strong>
                            </td>
                            <td>
                                <span class="text-muted small fw-semibold">{{ $item['category'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $item['quantity'] }}</span>
                            </td>
                            <td class="text-end fw-semibold">₹{{ number_format($item['original_price'], 2) }}</td>
                            <td class="text-center">
                                @if($item['discount_percentage'] > 0)
                                    <span class="rpt-badge badge-paid">{{ $item['discount_percentage'] }}%</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end text-danger fw-semibold">₹{{ number_format($item['discount_amount'], 2) }}</td>
                            <td class="text-end fw-bold text-dark">₹{{ number_format($item['taxable_amount'], 2) }}</td>
                            <td class="text-center">
                                <span class="rpt-badge badge-gst">{{ $item['gst_rate'] }}%</span>
                            </td>
                            <td class="text-end text-primary fw-bold">₹{{ number_format($item['gst_amount'], 2) }}</td>
                            <td class="text-end text-dark fw-extrabold" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">
                                ₹{{ number_format($item['total_amount'], 2) }}
                            </td>
                            <td class="text-muted small">
                                {{ $item['order_date'] ? $item['order_date']->format('d M Y') : 'N/A' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-end">Grand Totals:</th>
                            <th class="text-end text-danger">₹{{ number_format($totals['total_discount'], 2) }}</th>
                            <th class="text-end text-dark">₹{{ number_format($totals['total_taxable'], 2) }}</th>
                            <th></th>
                            <th class="text-end text-primary">₹{{ number_format($totals['total_gst'], 2) }}</th>
                            <th class="text-end text-dark">₹{{ number_format($totals['total_amount'], 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
    const table = $('#itemGSTTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[12, 'desc']],
        dom: '<"d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2"<"dt-buttons"B><"dt-search"f>>rt<"row align-items-center mt-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-end"p>>',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-1"></i> Excel',
                className: 'dt-button',
                title: 'Item_GST_Summary_{{ now()->format('Y-m-d') }}',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                    format: {
                        body: function(data, row, column, node) {
                            let $node = $(node);
                            if (column === 6) return $node.text().trim().replace('%', '');
                            if (column === 7 || column === 8 || column === 10 || column === 11) return data.replace('₹', '').trim();
                            if (column === 12) return $node.text().trim();
                            return data;
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print me-1"></i> Print',
                className: 'dt-button',
                title: 'Item GST Summary Report ({{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }})',
                customize: function(win) {
                    $(win.document.body).find('table').addClass('table table-bordered');
                    $(win.document.body).find('h1').css({
                        'text-align': 'center',
                        'font-size': '18px',
                        'margin-bottom': '16px'
                    });
                }
            }
        ],
        language: {
            search: "<i class='fas fa-search text-muted me-1'></i>",
            searchPlaceholder: "Search item, invoice...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            emptyTable: "<div class='py-4 text-center text-muted'><i class='fas fa-receipt fa-2x mb-2 d-block text-muted opacity-50'></i>No paid order items found for the selected date range.</div>",
            zeroRecords: "<div class='py-4 text-center text-muted'><i class='fas fa-search fa-2x mb-2 d-block text-muted opacity-50'></i>No matching items found.</div>",
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            }
        }
    });

    $('#btnExportExcel').on('click', function() {
        table.button('.buttons-excel').trigger();
    });

    $('#btnPrintReport').on('click', function() {
        table.button('.buttons-print').trigger();
    });
});
</script>
@endsection