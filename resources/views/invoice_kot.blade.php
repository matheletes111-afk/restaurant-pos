<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KOT #{{ $item->kot_no ?? $item->id }}</title>
    <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            width: 100%;
            margin: 0;
            padding: 5px;
            background: #fff;
            color: #000;
        }
        .kot-receipt {
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
            background: white;
        }
        .center { 
            text-align: center; 
        }
        .left { 
            text-align: left; 
        }
        .right { 
            text-align: right; 
        }
        .bold {
            font-weight: bold;
        }
        .line {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }
        .line-double {
            border-bottom: 3px double #000;
            margin: 5px 0;
        }
        
        /* Header Section */
        .restaurant-name {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .kot-title {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
            padding: 3px;
            border: 1px solid #000;
            background: #f5f5f5;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Info Rows */
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 10px;
        }
        .info-label {
            font-weight: bold;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        .items-table th {
            text-align: left;
            font-size: 10px;
            padding: 4px 0;
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
        }
        .items-table td {
            padding: 5px 0;
            vertical-align: top;
            font-size: 11px;
        }
        .item-name {
            font-size: 11px;
            font-weight: bold;
        }
        .item-qty {
            font-size: 12px;
            font-weight: bold;
        }
        
        /* Note Section */
        .note-section {
            margin: 8px 0;
            padding: 6px;
            border: 1px dashed #000;
            background: #fafafa;
        }
        .note-title {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .note-text {
            font-size: 10px;
            font-style: italic;
        }
        
        /* Status badge styling for print compatibility */
        .status-text {
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="kot-receipt">
        <!-- Restaurant Header -->
        <div class="center">
            <div class="restaurant-name">{{ $restaurant_details->name ?? 'RESTAURANT' }}</div>
            <div class="kot-title">KITCHEN ORDER TICKET</div>
        </div>
        
        <!-- KOT and Order Details -->
        <div class="info-row">
            <span class="info-label">KOT NO:</span>
            <span class="bold">{{ $item->kot_no ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">ORDER NO:</span>
            <span>#{{ $item->order->order_id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">DATE:</span>
            <span>{{ $item->created_at->format('d/m/Y h:i A') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">TABLE:</span>
            <span class="bold">
                @if($item->order->table)
                    {{ $item->order->table->name }}
                @else
                    TAKE AWAY
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">STATUS:</span>
            <span class="status-text bold">
                @if($item->order_status == 'PENDING')
                    PENDING (⏳)
                @elseif($item->order_status == 'COOKING')
                    COOKING (👨‍🍳)
                @else
                    DONE (✅)
                @endif
            </span>
        </div>
        
        <div class="line"></div>
        
        <!-- Dishes Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>ITEM DESCRIPTION</th>
                    <th class="right" style="width: 50px;">QTY</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <span class="item-name">{{ $item->subcategory->name }}</span>
                        <br>
                        <small style="font-size: 8px;">Category: {{ $item->subcategory->category->name ?? 'N/A' }} ({{ $item->subcategory->food_type }})</small>
                    </td>
                    <td class="right bold item-qty">{{ $item->quantity }}</td>
                </tr>
            </tbody>
        </table>
        
        <div class="line"></div>
        
        <!-- Note/Instructions -->
        @if($item->note)
        <div class="note-section">
            <div class="note-title">Instructions / Note:</div>
            <div class="note-text">{{ $item->note }}</div>
        </div>
        <div class="line"></div>
        @endif
        
        <!-- KOT Footer -->
        <div class="center" style="margin-top: 10px; font-size: 8px; text-transform: uppercase;">
            KOT printed successfully
        </div>
    </div>
</body>
</html>
