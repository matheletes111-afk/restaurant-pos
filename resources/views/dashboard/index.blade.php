<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Restaurant Dashboard | FoodFlow</title>

    @include('includes.style')

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #E85D2C;
            --primary-light: #FF8A5C;
            --secondary: #2EC4B6;
            --accent: #FF9F1C;
            --dark: #1A2C3E;
            --gray: #6C7A8A;
            --light: #F7F9FC;
            --white: #FFFFFF;
            --success: #2E9E4F;
            --warning: #F4A261;
            --danger: #E76F51;
            --info: #4895EF;
            --card-radius: 24px;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 16px 40px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #F5F7FB 0%, #EEF2F8 100%);
            color: var(--dark);
        }

        /* Dashboard Header */
        .dashboard-header {
            margin-bottom: 2rem;
            padding: 0.5rem 0;
        }

        .dashboard-header h5 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .dashboard-header p {
            color: var(--gray);
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }

        /* Stat Cards - Modern Glassmorphism */
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1.25rem;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
            background: linear-gradient(135deg, rgba(255,107,53,0.1), rgba(46,196,182,0.1));
        }

        .stat-icon i {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 0.25rem;
            letter-spacing: -1px;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--gray);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stat-trend {
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 20px;
            background: rgba(46,196,182,0.15);
            color: var(--secondary);
        }

        /* Chart Containers */
        .chart-card {
            background: var(--white);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            height: 100%;
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .chart-card:hover {
            box-shadow: var(--shadow-lg);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .chart-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-title i {
            color: var(--primary);
            font-size: 1.2rem;
        }

        /* Custom Tabs */
        .custom-tabs {
            display: flex;
            gap: 8px;
            background: var(--light);
            padding: 4px;
            border-radius: 60px;
        }

        .tab-btn {
            padding: 0.5rem 1.2rem;
            border: none;
            background: transparent;
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--gray);
            border-radius: 40px;
            transition: var(--transition);
            cursor: pointer;
        }

        .tab-btn.active {
            background: var(--white);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        /* Hot Dishes List */
        .hot-dishes-list {
            max-height: 320px;
            overflow-y: auto;
        }

        .hot-dish-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.9rem;
            background: var(--light);
            border-radius: 16px;
            margin-bottom: 0.75rem;
            transition: var(--transition);
        }

        .hot-dish-item:hover {
            background: rgba(255,107,53,0.08);
            transform: translateX(4px);
        }

        .hot-dish-rank {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            margin-right: 12px;
        }

        .hot-dish-name {
            font-weight: 600;
            color: var(--dark);
        }

        .hot-dish-count {
            background: var(--white);
            padding: 4px 12px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.8rem;
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        /* Select Dropdown */
        .trend-select {
            padding: 0.8rem 1rem;
            border-radius: 16px;
            border: 1px solid #E2E8F0;
            background: var(--white);
            font-weight: 500;
            color: var(--dark);
            width: 100%;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .trend-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255,107,53,0.1);
            outline: none;
        }

        /* DataTable Styling */
        .data-table-wrapper {
            background: var(--white);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            overflow: hidden;
        }

        .data-table-wrapper h6 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #orderReportTable {
            width: 100%;
        }

        #orderReportTable thead th {
            background: var(--light);
            color: var(--dark);
            font-weight: 600;
            font-size: 0.8rem;
            padding: 1rem;
            border-bottom: 2px solid #E2E8F0;
        }

        #orderReportTable tbody td {
            padding: 1rem;
            vertical-align: middle;
            font-size: 0.85rem;
            border-bottom: 1px solid #F0F2F5;
        }

        #orderReportTable tbody tr:hover {
            background: var(--light);
        }

        .order-id {
            font-weight: 700;
            color: var(--primary);
            font-family: monospace;
        }

        .order-status {
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-paid {
            background: rgba(46,196,182,0.15);
            color: var(--secondary);
        }

        .status-pending {
            background: rgba(244,162,97,0.15);
            color: var(--warning);
        }

        /* Notification Button */
        .notification-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 56px;
            height: 56px;
            border-radius: 28px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            color: white;
            font-size: 1.3rem;
            box-shadow: 0 8px 20px rgba(255,107,53,0.3);
            transition: var(--transition);
            z-index: 100;
            cursor: pointer;
        }

        .notification-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 28px rgba(255,107,53,0.4);
        }

        /* Scrollbar */
        .hot-dishes-list::-webkit-scrollbar {
            width: 4px;
        }

        .hot-dishes-list::-webkit-scrollbar-track {
            background: var(--light);
            border-radius: 10px;
        }

        .hot-dishes-list::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-value {
                font-size: 1.5rem;
            }
            .chart-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--gray);
        }
    </style>
</head>

<body>
@include('includes.sidebar')
<div class="pc-container">
<div class="pc-content">

<div class="dashboard-header">
    <h5>🍽️ Restaurant Dashboard</h5>
    <p>Welcome back! Track your performance, orders, and insights at a glance.</p>
</div>

{{-- STATS CARDS --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-utensils"></i></div>
            <div class="stat-value">{{ $totalDishes ?? 0 }}</div>
            <div class="stat-label">Total Dishes</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-leaf"></i></div>
            <div class="stat-value">{{ $totalVeg ?? 0 }}</div>
            <div class="stat-label">Veg Dishes</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-drumstick-bite"></i></div>
            <div class="stat-value">{{ $totalNonVeg ?? 0 }}</div>
            <div class="stat-label">Non-Veg Dishes</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-value">{{ $totalOrdersToday ?? 0 }}</div>
            <div class="stat-label">Orders Today</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-value">₹{{ number_format($totalRevenueToday ?? 0, 0) }}</div>
            <div class="stat-label">Revenue Today</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
            <div class="stat-value">{{ $totalStaff ?? 0 }}</div>
            <div class="stat-label">Active Staff</div>
        </div>
    </div>
</div>

{{-- TOP PRODUCTS & HOT DISHES --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-chart-bar"></i> Top Selling Products
                </div>
                <div class="custom-tabs">
                    <button class="tab-btn active" id="tab-daily">Daily</button>
                    <button class="tab-btn" id="tab-monthly">Monthly</button>
                    <button class="tab-btn" id="tab-yearly">Yearly</button>
                </div>
            </div>
            <canvas id="topProductsChart" height="180"></canvas>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-title">
                <i class="fas fa-fire" style="color: #FF6B35;"></i> Hot Dishes Today
            </div>
            <div class="hot-dishes-list mt-3">
                @forelse($hotDaily ?? [] as $index => $h)
                <div class="hot-dish-item">
                    <div class="hot-dish-info d-flex align-items-center">
                        <div class="hot-dish-rank">{{ $index + 1 }}</div>
                        <div class="hot-dish-name">{{ $h->subcategory->name ?? 'Unknown' }}</div>
                    </div>
                    <div class="hot-dish-count">{{ $h->total }} sold</div>
                </div>
                @empty
                <div class="empty-state">No data available</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- DISH MONTHLY TREND --}}
<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="chart-card">
            <div class="chart-title mb-3">
                <i class="fas fa-chart-line"></i> Dish Monthly Performance Trend
            </div>
            <select id="dishSelect" class="trend-select mb-3">
                <option value="">-- Select a Dish to View Trend --</option>
                @foreach($dishes ?? [] as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>
            <canvas id="dishMonthlyChart" height="120"></canvas>
        </div>
    </div>
</div>

{{-- LATEST ORDERS TABLE --}}
<div class="row g-4">
    <div class="col-lg-12">
        <div class="data-table-wrapper">
            <h6><i class="fas fa-history"></i> Recent Orders</h6>
            <div class="table-responsive">
                <table id="orderReportTable" class="table">
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
                        @foreach($orders ?? [] as $i => $o)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td class="order-id">{{ $o->order_id ?? 'N/A' }}</td>
                            <td><span class="badge bg-light">{{ $o->order_type ?? 'N/A' }}</span></td>
                            <td>{{ $o->customer_name ?? 'Guest' }}</td>
                            <td>₹{{ number_format($o->total_amount ?? 0, 2) }}</td>
                            <td>₹{{ number_format($o->gst_amount ?? 0, 2) }}</td>
                            <td><strong>₹{{ number_format($o->grand_total ?? 0, 2) }}</strong></td>
                            <td>
                                <span class="order-status status-{{ strtolower($o->payment_status ?? 'pending') }}">
                                    {{ $o->payment_status ?? 'Pending' }}
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

{{-- Floating Notification Button --}}
<button id="enable-notifications-btn" class="notification-btn" title="Enable Notifications">
    <i class="fas fa-bell"></i>
</button>

{{-- SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
    // Initialize DataTable
    $("#orderReportTable").DataTable({
        "pageLength": 5,
        "lengthMenu": [5, 10, 25, 50],
        "order": [[0, "asc"]],
        "language": {
            "search": "🔍 Search orders:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries"
        }
    });

    // Chart colors
    const chartColors = {
        primary: '#FF6B35',
        primaryLight: 'rgba(255, 107, 53, 0.2)',
        secondary: '#2EC4B6',
        accent: '#FF9F1C'
    };

    // Top Products Data
    const topDaily   = @json($topDailySeries ?? []);
    const topMonthly = @json($topMonthlySeries ?? []);
    const topYearly  = @json($topYearlySeries ?? []);

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
                    borderRadius: 10,
                    barPercentage: 0.65,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(26, 44, 62, 0.95)',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                return `Sold: ${context.raw} units`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { font: { size: 11 }, stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, maxRotation: 45, minRotation: 45 }
                    }
                }
            }
        }
    );

    // Tab Switching
    $(".tab-btn").click(function() {
        $(".tab-btn").removeClass("active");
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
                    borderColor: chartColors.primary,
                    backgroundColor: chartColors.primaryLight,
                    borderWidth: 3,
                    pointBackgroundColor: chartColors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(26, 44, 62, 0.95)',
                        titleFont: { size: 13 },
                        bodyFont: { size: 12 },
                        cornerRadius: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false }
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

    // Notification Button
    $("#enable-notifications-btn").click(function() {
        if ("Notification" in window) {
            if (Notification.permission === "granted") {
                new Notification("🔔 Notifications Enabled", {
                    body: "You will now receive real-time updates.",
                    icon: "{{ asset('favicon.ico') }}"
                });
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        new Notification("✅ Notifications Enabled", {
                            body: "Stay updated with your restaurant activity!",
                            icon: "{{ asset('favicon.ico') }}"
                        });
                    }
                });
            }
        } else {
            alert("Your browser does not support desktop notifications.");
        }
    });
});
</script>

</body>
</html>