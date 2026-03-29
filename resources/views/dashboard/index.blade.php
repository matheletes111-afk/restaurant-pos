<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Restaurant Dashboard</title>

    @include('includes.style')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
            --info-color: #4895ef;
            --dark-color: #2b2d42;
            --light-color: #f8f9fa;
            --gray-color: #8d99ae;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: #333;
        }

        .dashboard-header {
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }

        .dashboard-header h5 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
        }

        .dashboard-header p {
            color: var(--gray-color);
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }

        /* COUNTER CARDS */
        .counter-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.7);
            transition: var(--transition);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .counter-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .counter-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--primary-color);
            border-radius: 16px 0 0 16px;
        }

        .counter-card h3 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .counter-card p {
            color: var(--gray-color);
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .counter-card i {
            margin-right: 8px;
            font-size: 1.2rem;
        }

        .counter-icon {
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        /* CHART CONTAINERS */
        .chart-container {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
            height: 100%;
        }

        .chart-container h6 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
        }

        .chart-container h6 i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        /* HOT DISHES */
        .hot-dish-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border-radius: 12px;
            background-color: #f8f9ff;
            margin-bottom: 0.8rem;
            transition: var(--transition);
        }

        .hot-dish-item:hover {
            background-color: #edf2ff;
        }

        .hot-dish-item:last-child {
            margin-bottom: 0;
        }

        .hot-dish-info {
            display: flex;
            align-items: center;
        }

        .hot-dish-rank {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .hot-dish-name {
            font-weight: 500;
            color: var(--dark-color);
        }

        .hot-dish-count {
            background: var(--primary-color);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* TABS */
        .chart-tabs {
            display: flex;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            background: #f0f3ff;
            padding: 5px;
            width: fit-content;
        }

        .chart-tab {
            padding: 0.6rem 1.5rem;
            border: none;
            background: transparent;
            font-weight: 500;
            color: var(--gray-color);
            transition: var(--transition);
            border-radius: 10px;
        }

        .chart-tab.active {
            background: white;
            color: var(--primary-color);
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.15);
        }

        /* TABLE STYLING */
        .table-container {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .table-container h6 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
        }

        .table-container h6 i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        #orderReportTable {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        #orderReportTable thead th {
            background-color: #f8f9ff;
            color: var(--dark-color);
            font-weight: 600;
            border: none;
            padding: 1rem;
            border-top: 1px solid #eaeaea;
        }

        #orderReportTable tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        #orderReportTable tbody tr:hover {
            background-color: #f8f9ff;
        }

        .order-id {
            font-weight: 600;
            color: var(--primary-color);
        }

        .order-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-paid {
            background-color: rgba(76, 201, 240, 0.15);
            color: #0a8ea8;
        }

        .status-pending {
            background-color: rgba(247, 37, 133, 0.15);
            color: #c2185b;
        }

        /* SELECT STYLING */
        .trend-select {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border: 1px solid #ddd;
            background-color: white;
            font-weight: 500;
            color: var(--dark-color);
            width: 100%;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .trend-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .counter-card h3 {
                font-size: 1.8rem;
            }
            
            .counter-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
            
            .chart-tabs {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
@include('includes.sidebar')
<div class="pc-container">
<div class="pc-content">

<div class="dashboard-header">
    <h5>Restaurant Dashboard</h5>
    <p>Welcome back! Here's what's happening today.</p>
</div>
<button id="enable-notifications-btn" class="btn btn-primary">
    Enable Notifications
</button>


{{-- COUNTERS --}}
<div class="row g-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="counter-card">
            {{-- <div class="counter-icon" style="background-color: #4361ee;">
                <i class="fas fa-utensils"></i>
            </div> --}}
            <h3>{{ $totalDishes }}</h3>
            <p><i class="fas fa-list"></i> Total Dishes</p>
        </div>
    </div>

    <div class="col-6 col-md-4 col-lg-2">
        <div class="counter-card">
            {{-- <div class="counter-icon" style="background-color: #4cc9f0;">
                <i class="fas fa-leaf"></i>
            </div> --}}
            <h3>{{ $totalVeg }}</h3>
            <p><i class="fas fa-carrot"></i> Veg Dishes</p>
        </div>
    </div>

    <div class="col-6 col-md-4 col-lg-2">
        <div class="counter-card">
            {{-- <div class="counter-icon" style="background-color: #f72585;">
                <i class="fas fa-drumstick-bite"></i>
            </div> --}}
            <h3>{{ $totalNonVeg }}</h3>
            <p><i class="fas fa-bacon"></i> Non-Veg Dishes</p>
        </div>
    </div>

    <div class="col-6 col-md-4 col-lg-2">
        <div class="counter-card">
            {{-- <div class="counter-icon" style="background-color: #4895ef;">
                <i class="fas fa-shopping-cart"></i>
            </div> --}}
            <h3>{{ $totalOrdersToday }}</h3>
            <p><i class="fas fa-calendar-day"></i> Orders Today</p>
        </div>
    </div>

    <div class="col-6 col-md-4 col-lg-2">
        <div class="counter-card">
           {{--  <div class="counter-icon" style="background-color: #3f37c9;">
                <i class="fas fa-money-bill-wave"></i>
            </div> --}}
            <h3>₹{{ number_format($totalRevenueToday,0) }}</h3>
            <p><i class="fas fa-chart-line"></i> Revenue Today</p>
        </div>
    </div>

    <div class="col-6 col-md-4 col-lg-2">
        <div class="counter-card">
            {{-- <div class="counter-icon" style="background-color: #7209b7;">
                <i class="fas fa-users"></i>
            </div> --}}
            <h3>{{ $totalStaff }}</h3>
            <p><i class="fas fa-user-tie"></i> Total Staff</p>
        </div>
    </div>
</div>

{{-- TOP PRODUCTS & HOT DISHES --}}
<div class="row g-4 mt-2">
    <div class="col-lg-8">
        <div class="chart-container">
            <h6><i class="fas fa-chart-bar"></i> Top Products</h6>
            
            <div class="chart-tabs">
                <button class="chart-tab active" id="tab-daily">Daily</button>
                <button class="chart-tab" id="tab-monthly">Monthly</button>
                <button class="chart-tab" id="tab-yearly">Yearly</button>
            </div>

            <canvas id="topProductsChart" height="160"></canvas>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="chart-container">
            <h6><i class="fas fa-fire"></i> Hot Dishes Today</h6>
            
            <div class="hot-dishes-list">
                @foreach($hotDaily as $index => $h)
                <div class="hot-dish-item">
                    <div class="hot-dish-info">
                        <div class="hot-dish-rank">{{ $index + 1 }}</div>
                        <div class="hot-dish-name">{{ $h->subcategory->name ?? 'Unknown' }}</div>
                    </div>
                    <div class="hot-dish-count">{{ $h->total }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- DISH TREND & LATEST ORDERS --}}
<div class="row g-4 mt-2">
    <div class="col-lg-12">
        <div class="chart-container">
            <h6><i class="fas fa-chart-line"></i> Dish Monthly Trend</h6>
            
            <select id="dishSelect" class="trend-select">
                <option value="">-- Select Dish to View Trend --</option>
                @foreach($dishes as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>

            <canvas id="dishMonthlyChart" height="140"></canvas>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="table-container">
            <h6><i class="fas fa-history"></i> Latest Orders</h6>
            <div class="table-responsive">
                <table id="orderReportTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order ID</th>
                            <th>Type</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>GST</th>
                            <th>Final</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $i => $o)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td class="order-id">{{@$o->order_id}}</td>
                            <td><span class="badge bg-light text-dark">{{ $o->order_type }}</span></td>
                            <td>{{ $o->customer_name }}</td>
                            <td>₹{{ $o->total_amount }}</td>
                            <td>₹{{ $o->gst_amount }}</td>
                            <td><strong>₹{{ $o->grand_total }}</strong></td>
                            <td>
                                <span class="order-status status-{{ strtolower($o->payment_status) }}">
                                    {{ $o->payment_status }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($o->created_at)->format('h:i A') }}</td>
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


{{-- SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')
<!-- Enable Notifications Button -->
<button id="enable-notifications-btn" class="btn btn-primary">
    Enable Notifications
</button>







<script>
$(document).ready(function() {
    // Initialize DataTable
    $("#orderReportTable").DataTable({
        "pageLength": 5,
        "lengthMenu": [5, 10, 25, 50],
        "order": [[0, "asc"]],
        "language": {
            "search": "Search orders:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries"
        }
    });

    // Chart colors
    const chartColors = {
        primary: 'rgba(67, 97, 238, 0.8)',
        primaryLight: 'rgba(67, 97, 238, 0.2)',
        secondary: 'rgba(76, 201, 240, 0.8)',
        warning: 'rgba(247, 37, 133, 0.8)',
        success: 'rgba(72, 149, 239, 0.8)'
    };

    // Top Products Data
    const topDaily   = @json($topDailySeries);
    const topMonthly = @json($topMonthlySeries);
    const topYearly  = @json($topYearlySeries);

    function formatData(series) {
        return {
            labels: series.map(s => s.subcategory?.name ?? "Unknown"),
            data: series.map(s => Number(s.total ?? 0))
        };
    }

    // Top Products Chart
    let tp = formatData(topDaily);
    let topChart = new Chart(
        document.getElementById("topProductsChart"),
        {
            type: "bar",
            data: { 
                labels: tp.labels, 
                datasets: [{
                    label: "Quantity Sold",
                    data: tp.data,
                    backgroundColor: chartColors.primary,
                    borderColor: chartColors.primary,
                    borderWidth: 1,
                    borderRadius: 8,
                    hoverBackgroundColor: chartColors.secondary
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 6
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: { size: 12 }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 12 }
                        }
                    }
                }
            }
        }
    );

    // Tab Switching
    $(".chart-tab").click(function() {
        $(".chart-tab").removeClass("active");
        $(this).addClass("active");
        
        let tabId = $(this).attr("id").replace("tab-", "");
        let seriesData;
        
        if (tabId === "daily") seriesData = topDaily;
        else if (tabId === "monthly") seriesData = topMonthly;
        else seriesData = topYearly;
        
        let formattedData = formatData(seriesData);
        topChart.data.labels = formattedData.labels;
        topChart.data.datasets[0].data = formattedData.data;
        topChart.update();
    });

    // Dish Monthly Chart
    let dishChart = new Chart(
        document.getElementById("dishMonthlyChart"),
        {
            type: "line",
            data: { 
                labels: [], 
                datasets: [{
                    label: "Quantity Sold",
                    data: [],
                    backgroundColor: chartColors.primaryLight,
                    borderColor: chartColors.primary,
                    borderWidth: 3,
                    pointBackgroundColor: chartColors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        }
    );

    // Load Dish Monthly Data
    $("#dishSelect").change(function () {
        let id = $(this).val();
        if (!id) {
            dishChart.data.labels = [];
            dishChart.data.datasets[0].data = [];
            dishChart.update();
            return;
        }

        let url = "{{ route('dashboard.dish.monthly', ':id') }}";
        url = url.replace(":id", id);

        $.getJSON(url, function (res) {
            dishChart.data.labels = res.labels;
            dishChart.data.datasets[0].data = res.data;
            dishChart.update();
        });
    });
});
</script>

</body>
</html>