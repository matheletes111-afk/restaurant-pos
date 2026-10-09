@extends('layouts.app')

@section('title')
<title>Admin - Debit Note Details</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin_template/css/inventory-modules.css') }}">
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="inv-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        {{-- 1. Header Deck --}}
        <div class="inv-header-deck">
            <div class="inv-header-left">
                <div class="inv-header-icon icon-debit">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="inv-header-title-meta">
                    <span class="inv-header-eyebrow">Debit Note Record</span>
                    <h1 class="inv-header-title">{{ $debitNote->debit_note_no }}</h1>
                    <p class="inv-header-sub">Issued on {{ $debitNote->debit_date ? $debitNote->debit_date->format('d M, Y') : '-' }} to {{ $debitNote->supplier->supplier_name ?? 'Vendor' }}</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('debit-notes.index') }}" class="btn-inv-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Debit Notes</span>
                </a>
            </div>
        </div>

        {{-- 2. Metadata Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="inv-card p-4 h-100">
                    <h6 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif; color: var(--inv-dark);">
                        <i class="fas fa-info-circle text-primary me-2"></i> Note Summary
                    </h6>
                    <table class="inv-table">
                        <tr>
                            <td class="text-muted fw-semibold" style="width: 40%;">Debit Note No:</td>
                            <td><strong class="text-dark">{{ $debitNote->debit_note_no }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Issue Date:</td>
                            <td><span>{{ $debitNote->debit_date ? $debitNote->debit_date->format('d-m-Y') : '-' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Target Supplier:</td>
                            <td><strong class="text-primary">{{ $debitNote->supplier->supplier_name ?? 'N/A' }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="inv-card p-4 h-100">
                    <h6 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif; color: var(--inv-dark);">
                        <i class="fas fa-user-shield text-primary me-2"></i> Audit & Remarks
                    </h6>
                    <table class="inv-table">
                        <tr>
                            <td class="text-muted fw-semibold" style="width: 40%;">Created By:</td>
                            <td><span class="text-dark fw-bold">{{ $debitNote->user->name ?? 'System' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Timestamp:</td>
                            <td><small class="text-muted">{{ $debitNote->created_at->format('d-m-Y h:i A') }}</small></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Remarks:</td>
                            <td><span>{{ $debitNote->remarks ?? 'No remarks provided' }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- 3. Items Returned Table --}}
        <div class="inv-card">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif; color: var(--inv-dark);">
                    <i class="fas fa-boxes text-danger me-2"></i> Returned Line Items ({{ $debitNote->items->count() }})
                </h6>
            </div>
            <div class="table-responsive">
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Product Name</th>
                            <th>Measurement Unit</th>
                            <th class="text-end">Returned Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debitNote->items as $index => $item)
                        <tr>
                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <strong class="text-dark">{{ $item->product->product_name ?? 'Item #'.$item->product_id }}</strong>
                            </td>
                            <td>
                                <span class="inv-badge badge-unit">{{ $item->unit->name ?? 'N/A' }}</span>
                            </td>
                            <td class="text-end">
                                <strong class="text-danger" style="font-family: 'Outfit', sans-serif; font-size: 1.05rem;">
                                    -{{ number_format($item->quantity, 2) }}
                                </strong>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No line items attached.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
</div>

@endsection

@section('script')
@include('includes.script')
@endsection