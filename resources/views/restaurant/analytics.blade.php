<!DOCTYPE html>
<html lang="en">
<head>
    <title>Restaurant Analytics Dashboard</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary), #34495e);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Card Styling */
        .analytics-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .analytics-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), #34495e);
            color: white;
            border-bottom: none;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .stat-icon.revenue { background: rgba(39, 174, 96, 0.1); color: var(--success); }
        .stat-icon.orders { background: rgba(52, 152, 219, 0.1); color: var(--secondary); }
        .stat-icon.categories { background: rgba(155, 89, 182, 0.1); color: #9b59b6; }
        .stat-icon.dishes { background: rgba(241, 196, 15, 0.1); color: var(--warning); }
        .stat-icon.tables { background: rgba(231, 76, 60, 0.1); color: var(--danger); }
        .stat-icon.paid { background: rgba(46, 204, 113, 0.1); color: #2ecc71; }
        .stat-icon.misc { background: rgba(243, 156, 18, 0.1); color: var(--warning); }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .stat-change {
            font-size: 0.85rem;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .change-positive { background: rgba(39, 174, 96, 0.1); color: var(--success); }
        .change-negative { background: rgba(231, 76, 60, 0.1); color: var(--danger); }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        /* Table Styling */
        .analytics-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .analytics-table thead {
            background: linear-gradient(135deg, var(--primary), #34495e);
        }

        .analytics-table th {
            color: white;
            font-weight: 500;
            padding: 12px 15px;
            text-align: left;
        }

        .analytics-table tbody tr {
            transition: background 0.3s;
        }

        .analytics-table tbody tr:hover {
            background: #f8f9fa;
        }

        .analytics-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e6ed;
        }

        .analytics-table tr:last-child td {
            border-bottom: none;
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Trending Items */
        .trending-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #e0e6ed;
        }

        .trending-item:last-child {
            border-bottom: none;
        }

        .trending-rank {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, var(--secondary), #2980b9);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .trending-info {
            flex: 1;
        }

        .trending-stats {
            text-align: right;
            font-weight: 600;
            color: var(--primary);
        }

        /* Badges */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-paid { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-miscorder { background: #fce7f3; color: #9d174d; }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-value {
                font-size: 1.5rem;
            }
            
            .chart-container {
                height: 250px;
            }
        }

        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Print Button */
        .print-btn {
            background: linear-gradient(135deg, var(--primary), #34495e);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body data-pc-theme="light">
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 text-white"><i class="fas fa-chart-line me-2"></i>Restaurant Analytics Dashboard</h2>
                    <p class="mb-0 opacity-75">Comprehensive analytics and insights for restaurant performance</p>
                </div>
                
            </div>
            
        </div>
        <p>
                <a href="{{ route('order.management.dashboard') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to Orders
                    </a>
            </p>

        <!-- Date Filter -->
<!--         <div class="filter-section">
            <form action="{{ route('restaurant.analytics.filter', $restaurantId) }}" method="POST" id="filterForm">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" name="from_date" 
                               value="{{ $fromDate ?? now()->subDays(30)->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" name="to_date" 
                               value="{{ $toDate ?? now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filter Data
                        </button>
                    </div>
                    <div class="col-md-3">
                        @if(isset($isFiltered))
                        <a href="{{ route('restaurant.analytics', $restaurantId) }}" class="btn btn-secondary w-100">
                            <i class="fas fa-redo me-2"></i>Reset Filter
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div> -->

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <!-- Total Revenue -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon revenue">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-value">₹{{ number_format(isset($isFiltered) ? $filteredRevenue : $totalRevenue) }}</div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">
                            @if(isset($isFiltered))
                            Filtered Period
                            @else
                            All Time
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-value">{{ number_format(isset($isFiltered) ? $filteredOrders : $totalOrders) }}</div>
                    <div class="stat-label">Total Orders</div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">
                            @if(isset($isFiltered))
                            Filtered Period
                            @else
                            All Time
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <!-- Today's Revenue -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon revenue">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-value">₹{{ number_format($todayRevenue) }}</div>
                    <div class="stat-label">Today's Revenue</div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Today's Orders: {{ $todayOrders }}</small>
                    </div>
                </div>
            </div>

            <!-- Month Revenue -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon revenue">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-value">₹{{ number_format($monthRevenue) }}</div>
                    <div class="stat-label">This Month Revenue</div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Month Orders: {{ $monthOrders }}</small>
                    </div>
                </div>
            </div>

            <!-- Categories & Dishes -->
            <div class="col-xl-2 col-md-4 mb-4">
                <div class="stat-card">
                    <div class="stat-icon categories">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stat-value">{{ $totalCategories }}</div>
                    <div class="stat-label">Total Categories</div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="stat-card">
                    <div class="stat-icon dishes">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-value">{{ $totalDishes }}</div>
                    <div class="stat-label">Total Dishes</div>
                </div>
            </div>


            <!-- Paid Orders -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon paid">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value">
                        @if(isset($isFiltered))
                        {{ $filteredPaidOrders }}
                        @else
                        {{ $todayPaidOrders }}
                        @endif
                    </div>
                    <div class="stat-label">Paid Orders</div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">
                            @if(isset($isFiltered))
                            Filtered Period
                            @else
                            Today
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <!-- Misc Orders -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon misc">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="stat-value">
                        @if(isset($isFiltered))
                        {{ $filteredMiscOrders }}
                        @else
                        {{ $todayMiscOrders }}
                        @endif
                    </div>
                    <div class="stat-label">Misc Orders</div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">
                            @if(isset($isFiltered))
                            Filtered Period
                            @else
                            Today
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics -->
        <div class="row mb-4">
            <!-- Revenue Chart -->
            <div class="col-lg-8 mb-4">
                <div class="analytics-card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line me-2"></i>Revenue Trend</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="col-lg-4 mb-4">
                <div class="analytics-card">
                    <div class="card-header">
                        <h5><i class="fas fa-credit-card me-2"></i>Payment Methods</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="paymentMethodChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trending Dishes & Recent Orders -->
        <div class="row mb-4">
            <!-- Trending Dishes -->
            <div class="col-lg-4 mb-4">
                <div class="analytics-card">
                    <div class="card-header">
                        <h5 class="text-white"><i class="fas fa-fire me-2"></i>Trending Dishes</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $trendingData = isset($isFiltered) ? $filteredTrendingDishes : $trendingDishes;
                        @endphp
                        
                        @if($trendingData->count() > 0)
                            @foreach($trendingData as $index => $dish)
                            <div class="trending-item">
                                <div class="trending-rank">{{ $index + 1 }}</div>
                                <div class="trending-info">
                                    <h6 class="mb-1">{{ $dish->subcategory->name ?? 'Unknown' }}</h6>
                                    <small class="text-muted">Ordered {{ $dish->order_count }} times</small>
                                </div>
                                
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-utensils fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No trending dishes data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="col-lg-8 mb-4">
                <div class="analytics-card">
                    <div class="card-header">
                        <h5 class="text-white"><i class="fas fa-history me-2"></i>Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="analytics-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $ordersData = isset($isFiltered) ? $filteredOrdersList : $recentOrders;
                                    @endphp
                                    
                                    @foreach($ordersData as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>₹{{ number_format($order->grand_total, 2) }}</td>
                                        <td>
                                            @if($order->payment_status == 'PAID')
                                                <span class="status-badge status-paid">
                                                    <i class="fas fa-check-circle"></i> PAID
                                                </span>
                                            @elseif($order->payment_status == 'PENDING')
                                                <span class="status-badge status-pending">
                                                    <i class="fas fa-clock"></i> PENDING
                                                </span>
                                            @elseif($order->payment_status == 'MISCORDER')
                                                <span class="status-badge status-miscorder">
                                                    <i class="fas fa-exclamation-circle"></i> MISCORDER
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('h:i A') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <!-- <div class="row">
            <div class="col-12">
                <div class="analytics-card">
                    <div class="card-header">
                        <h5 class="text-white"><i class="fas fa-chart-pie me-2"></i>Order Status Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <canvas id="orderStatusChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="analytics-table">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>Orders</th>
                                                <th>Percentage</th>
                                                <th>Total Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalOrders = isset($isFiltered) ? $filteredOrders : $totalOrders;
                                                $paymentMethodsData = isset($isFiltered) ? $filteredPaymentMethods : $paymentMethods;
                                            @endphp
                                            
                                            @foreach($paymentMethodsData as $method)
                                            <tr>
                                                <td>{{ $method->payment_method }}</td>
                                                <td>{{ $method->count }}</td>
                                                <td>{{ $totalOrders > 0 ? round(($method->count / $totalOrders) * 100, 1) : 0 }}%</td>
                                                <td>₹{{ number_format($method->total, 2) }}</td>
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
        </div> -->
    </div>
</div>

@include('includes.script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// Revenue Chart
// Revenue Chart - Changed to Bar Graph
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = @json(isset($isFiltered) ? $filteredDailyRevenue : $dailyRevenue);

const revenueChart = new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: revenueData.map(item => {
            // Format date to be more readable (e.g., "Jan 15")
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'Daily Revenue',
            data: revenueData.map(item => item.revenue),
            backgroundColor: revenueData.map((item, index) => {
                // Gradient colors for better visualization
                return index % 2 === 0 ? 'rgba(52, 152, 219, 0.8)' : 'rgba(41, 128, 185, 0.8)';
            }),
            borderColor: '#2980b9',
            borderWidth: 1,
            borderRadius: 6, // Rounded corners for bars
            hoverBackgroundColor: '#3498db',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false, // Hide legend for cleaner look
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: ₹' + context.parsed.y.toLocaleString();
                    },
                    afterLabel: function(context) {
                        const item = revenueData[context.dataIndex];
                        return `Orders: ${item.orders || 0}`;
                    }
                },
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
                    callback: function(value) {
                        return '₹' + value.toLocaleString();
                    },
                    font: {
                        size: 11
                    }
                },
                title: {
                    display: true,
                    text: 'Revenue (₹)',
                    font: {
                        size: 12,
                        weight: 'bold'
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 11
                    },
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        },
        animation: {
            duration: 1000,
            easing: 'easeOutQuart'
        }
    }
});
// Payment Methods Chart
const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
const paymentData = @json(isset($isFiltered) ? $filteredPaymentMethods : $paymentMethods);

const paymentChart = new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: paymentData.map(item => item.payment_method),
        datasets: [{
            data: paymentData.map(item => item.total),
            backgroundColor: [
                '#3498db',
                '#2ecc71',
                '#9b59b6',
                '#e74c3c',
                '#f39c12',
                '#1abc9c'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${context.label}: ₹${value.toLocaleString()} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Order Status Chart
const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
const statusData = @json($orderStatus ?? []);

const statusChart = new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: statusData.map(item => item.payment_status),
        datasets: [{
            data: statusData.map(item => item.count),
            backgroundColor: [
                '#2ecc71', // PAID
                '#f39c12', // PENDING
                '#e74c3c', // MISCORDER
                '#3498db', // Others
                '#9b59b6'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
            }
        }
    }
});

// Auto-refresh data every 5 minutes
setTimeout(() => {
    window.location.reload();
}, 300000); // 5 minutes


</script>
</body>
</html>