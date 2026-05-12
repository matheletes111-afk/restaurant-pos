<!DOCTYPE html>
<html lang="en">
<head>
    <title>Item GST Summary Report</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #17a2b8;
            --gray: #64748b;
        }

        body {
            background: linear-gradient(135deg, #f5f7fb 0%, #eef2f8 100%);
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        .page-header-custom {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 20px;
            padding: 24px 30px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        
        .page-header-custom::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(52,152,219,0.15), transparent);
            border-radius: 50%;
        }

        .page-header-custom h3 {
            color: white;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .filter-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        .summary-card {
            background: white;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-top: 4px solid;
            transition: transform 0.3s;
        }

        .summary-card:hover {
            transform: translateY(-3px);
        }

        .summary-value {
            font-size: 1.6rem;
            font-weight: 700;
        }

        .summary-label {
            color: var(--gray);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dataTables_wrapper {
            padding: 20px;
            background: white;
            border-radius: 16px;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary), #34495e);
            color: white;
            font-weight: 500;
            border: none;
            padding: 12px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 10px 12px;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .dt-buttons .btn {
            margin-right: 5px;
            border-radius: 8px;
        }

        .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 12px;
            margin-left: 8px;
        }

        .total-row {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            font-weight: bold;
        }

        .total-row td {
            font-weight: 700;
            background: #f1f5f9;
        }

        .badge-gst {
            background: #fef3c7;
            color: #92400e;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        @media (max-width: 768px) {
            .page-header-custom {
                padding: 20px;
            }
            .summary-card {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <!-- Page Header -->
        <div class="page-header-custom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3><i class="fas fa-file-invoice-dollar me-2"></i>Item GST Summary Report</h3>
                    <p class="text-white-50 mb-0">Detailed breakdown of items with GST calculations</p>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="filter-card">
            <form method="GET" action="{{ route('report.item.gst.summary') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Generate Report
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" id="exportExcelBtn" class="btn btn-success w-100">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" id="printReport" class="btn btn-secondary w-100">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="summary-card" style="border-top-color: #3b82f6;">
                    <div class="summary-value text-primary">₹{{ number_format($totals['total_taxable'], 2) }}</div>
                    <div class="summary-label">Total Taxable Amount</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card" style="border-top-color: #10b981;">
                    <div class="summary-value text-success">₹{{ number_format($totals['total_discount'], 2) }}</div>
                    <div class="summary-label">Total Discount</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card" style="border-top-color: #f59e0b;">
                    <div class="summary-value text-warning">₹{{ number_format($totals['total_gst'], 2) }}</div>
                    <div class="summary-label">Total GST Amount</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card" style="border-top-color: #8b5cf6;">
                    <div class="summary-value text-purple">₹{{ number_format($totals['total_amount'], 2) }}</div>
                    <div class="summary-label">Total Final Amount</div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <div class="dataTables_wrapper">
            <table id="itemGSTTable" class="table table-bordered table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Qty</th>
                        <th>Price (₹)</th>
                        <th>Disc %</th>
                        <th>Disc Amt (₹)</th>
                        <th>Taxable (₹)</th>
                        <th>GST %</th>
                        <th>GST Amt (₹)</th>
                        <th>Total (₹)</th>
                        <th>Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="fw-semibold">{{ $item['invoice_no'] }}</span></td>
                        <td>{{ $item['item_name'] }}</td>
                        <td>{{ $item['category'] }}</td>
                        <td class="text-center">{{ $item['quantity'] }}</td>
                        <td class="text-end">₹{{ number_format($item['original_price'], 2) }}</td>
                        <td class="text-center">
                            @if($item['discount_percentage'] > 0)
                                <span class="badge bg-success">{{ $item['discount_percentage'] }}%</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end text-danger">₹{{ number_format($item['discount_amount'], 2) }}</td>
                        <td class="text-end">₹{{ number_format($item['taxable_amount'], 2) }}</td>
                        <td class="text-center">
                            <span class="badge-gst">{{ $item['gst_rate'] }}%</span>
                        </td>
                        <td class="text-end">₹{{ number_format($item['gst_amount'], 2) }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($item['total_amount'], 2) }}</td>
                        <td>{{ $item['order_date'] ? $item['order_date']->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <th colspan="7" class="text-end">Total:</th>
                        <th class="text-end">₹{{ number_format($totals['total_discount'], 2) }}</th>
                        <th class="text-end">₹{{ number_format($totals['total_taxable'], 2) }}</th>
                        <th></th>
                        <th class="text-end">₹{{ number_format($totals['total_gst'], 2) }}</th>
                        <th class="text-end">₹{{ number_format($totals['total_amount'], 2) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
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
    // Initialize DataTable
    const table = $('#itemGSTTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[12, 'desc']], // Order by date desc
        "dom": '<"d-flex justify-content-between align-items-center mb-3"<"dt-buttons"B><"dt-search"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Export Excel',
                className: 'btn btn-success btn-sm',
                title: 'Item_GST_Summary_{{ now()->format('Y-m-d') }}',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                    format: {
                        body: function(data, row, column, node) {
                            // Clean data for export
                            let $node = $(node);
                            if (column === 6) {
                                // Discount percentage badge
                                return $node.text().trim().replace('%', '');
                            }
                            if (column === 7 || column === 8 || column === 10 || column === 11) {
                                // Amount columns - remove ₹ symbol
                                return data.replace('₹', '').trim();
                            }
                            if (column === 12) {
                                // Date column
                                return $node.text().trim();
                            }
                            return data;
                        }
                    }
                },
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    // Add summary row at the end
                    var lastRow = $('row:last', sheet);
                    var summaryRow = $('<row></row>');
                    summaryRow.append('<c t="inlineStr"><is><t>Total</t></is></c>');
                    summaryRow.append('<c t="inlineStr"></c>');
                    summaryRow.append('<c t="inlineStr"></c>');
                    summaryRow.append('<c t="inlineStr"></c>');
                    summaryRow.append('<c t="inlineStr"></c>');
                    summaryRow.append('<c t="inlineStr"></c>');
                    summaryRow.append('<c t="inlineStr"></c>');
                    summaryRow.append('<c t="inlineStr"><is><t>{{ number_format($totals['total_discount'], 2) }}</t></is></c>');
                    summaryRow.append('<c t="inlineStr"><is><t>{{ number_format($totals['total_taxable'], 2) }}</t></is></c>');
                    summaryRow.append('<c t="inlineStr"></c>');
                    summaryRow.append('<c t="inlineStr"><is><t>{{ number_format($totals['total_gst'], 2) }}</t></is></c>');
                    summaryRow.append('<c t="inlineStr"><is><t>{{ number_format($totals['total_amount'], 2) }}</t></is></c>');
                    summaryRow.append('<c t="inlineStr"></c>');
                    $(sheet).find('sheetData').append(summaryRow);
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-secondary btn-sm',
                title: 'Item GST Summary Report',
                customize: function(win) {
                    $(win.document.body).find('table').addClass('table table-bordered');
                    $(win.document.body).find('h1').css({
                        'text-align': 'center',
                        'font-size': '18px',
                        'margin-bottom': '20px'
                    });
                    
                    // Add summary to print
                    $(win.document.body).prepend(`
                        <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Date Range:</strong> {{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}</p>
                                    <p><strong>Total Items:</strong> {{ count($reportData) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Taxable:</strong> ₹{{ number_format($totals['total_taxable'], 2) }}</p>
                                    <p><strong>Total GST:</strong> ₹{{ number_format($totals['total_gst'], 2) }}</p>
                                    <p><strong>Total Amount:</strong> ₹{{ number_format($totals['total_amount'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    `);
                }
            }
        ],
        "language": {
            "search": "<i class='fas fa-search me-1'></i> Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });

    // Export to Excel button click
    $('#exportExcelBtn').click(function() {
        table.button('.buttons-excel').trigger();
    });

    // Print button click
    $('#printReport').click(function() {
        table.button('.buttons-print').trigger();
    });
});
</script>

<style>
    .text-purple {
        color: #8b5cf6;
    }
    .dataTables_wrapper {
        overflow-x: auto;
    }
    .dt-buttons .btn {
        margin-right: 8px;
    }
    .dataTables_filter {
        text-align: right;
    }
    .dataTables_filter label {
        font-weight: normal;
    }
    .badge-gst {
        background: #fef3c7;
        color: #92400e;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .table tfoot th {
        background: #f1f5f9;
        font-weight: 700;
    }
</style>

</body>
</html>