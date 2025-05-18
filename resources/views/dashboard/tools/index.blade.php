@extends('layouts.app')

@section('title', 'Cake Tools Dashboard')

@section('content')
<div class="row">
    <!-- Date Range Filter -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-range me-2"></i>
                        <select class="form-select form-select-sm" id="dateRange">
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="input-group date-picker-range d-none">
                        <input type="text" class="form-control form-control-sm" id="startDate" placeholder="Start Date">
                        <span class="input-group-text">to</span>
                        <input type="text" class="form-control form-control-sm" id="endDate" placeholder="End Date">
                        <button class="btn btn-sm btn-primary" id="applyCustomRange">Apply</button>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-primary me-2" id="refreshData">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-export="pdf">PDF Report</a></li>
                                <li><a class="dropdown-item" href="#" data-export="excel">Excel Data</a></li>
                                <li><a class="dropdown-item" href="#" data-export="csv">CSV Data</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Left side columns -->
    <div class="col-lg-8">
        <div class="row">
            <!-- Key Performance Metrics -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Sales Today</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-cart"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ money($stats['sales_today'] ?? 0) }}</h6>
                                @if(($stats['sales_growth'] ?? 0) > 0)
                                    <span class="text-success small pt-1 fw-bold">+{{ number_format($stats['sales_growth'] ?? 0, 1) }}%</span>
                                @else
                                    <span class="text-danger small pt-1 fw-bold">{{ number_format(abs($stats['sales_growth'] ?? 0), 1) }}%</span>
                                @endif
                                <span class="text-muted small pt-2 ps-1">vs yesterday</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Value Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Inventory Value</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ money($stats['inventory_value'] ?? 0) }}</h6>
                                <span class="text-muted small pt-2">{{ number_format($stats['total_items'] ?? 0) }} items in stock</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card customers-card">
                    <div class="card-body">
                        <h5 class="card-title">Low Stock Alerts</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['low_stock'] ?? 0) }}</h6>
                                <span class="text-muted small pt-2">items need attention</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Turnover -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Inventory Turnover</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info-light">
                                <i class="bi bi-arrow-repeat text-info"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['inventory_turnover'] ?? 4.2, 1) }}</h6>
                                <span class="text-muted small pt-2">turns per year</span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 6px">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: {{ min(($stats['inventory_turnover'] ?? 4.2) / 8 * 100, 100) }}%" 
                                     aria-valuenow="{{ $stats['inventory_turnover'] ?? 4.2 }}" aria-valuemin="0" 
                                     aria-valuemax="8"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Low</small>
                                <small class="text-muted">Optimal (4-8)</small>
                                <small class="text-muted">High</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credits Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Credits</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light">
                                <i class="bi bi-credit-card text-success"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ money($stats['total_credits'] ?? 8450.75) }}</h6>
                                <span class="text-muted small pt-2">{{ $stats['credit_count'] ?? 12 }} customers</span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 6px">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ min(($stats['credit_payment_rate'] ?? 85) , 100) }}%" 
                                     aria-valuenow="{{ $stats['credit_payment_rate'] ?? 85 }}" aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Payment Rate: {{ $stats['credit_payment_rate'] ?? 85 }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Debits Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Debits</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-light">
                                <i class="bi bi-cash-stack text-danger"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ money($stats['total_debits'] ?? 5280.25) }}</h6>
                                <span class="text-muted small pt-2">{{ $stats['debit_count'] ?? 8 }} suppliers</span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 6px">
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: {{ min(($stats['debit_due_soon_percent'] ?? 45) , 100) }}%" 
                                     aria-valuenow="{{ $stats['debit_due_soon_percent'] ?? 45 }}" aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Due within 30 days: {{ $stats['debit_due_soon_percent'] ?? 45 }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Trend Chart -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Sales & Revenue Trends</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-graph-up"></i> Metrics
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-metric="sales">Sales Count</a></li>
                                    <li><a class="dropdown-item" href="#" data-metric="revenue">Revenue</a></li>
                                    <li><a class="dropdown-item" href="#" data-metric="average">Average Order Value</a></li>
                                    <li><a class="dropdown-item" href="#" data-metric="combined">Combined View</a></li>
                                </ul>
                            </div>
                        </div>
                        <div id="salesTrendChart"></div>
                    </div>
                </div>
            </div>

            <!-- Product Performance Matrix -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Product Performance Matrix</h5>
                        <div id="productMatrixChart"></div>
                        <div class="row mt-3">
                            <div class="col-md-3 col-sm-6 text-center">
                                <div class="p-3 border rounded">
                                    <h6 class="text-primary">Stars</h6>
                                    <h4>{{ $productMatrix['stars'] ?? 5 }}</h4>
                                    <small class="text-muted">High sales, high profit</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 text-center">
                                <div class="p-3 border rounded">
                                    <h6 class="text-success">Cash Cows</h6>
                                    <h4>{{ $productMatrix['cash_cows'] ?? 8 }}</h4>
                                    <small class="text-muted">High sales, low profit</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 text-center">
                                <div class="p-3 border rounded">
                                    <h6 class="text-warning">Question Marks</h6>
                                    <h4>{{ $productMatrix['question_marks'] ?? 12 }}</h4>
                                    <small class="text-muted">Low sales, high profit</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 text-center">
                                <div class="p-3 border rounded">
                                    <h6 class="text-danger">Dogs</h6>
                                    <h4>{{ $productMatrix['dogs'] ?? 4 }}</h4>
                                    <small class="text-muted">Low sales, low profit</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>

            <!-- Category Distribution Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Category Distribution</h5>
                        <div id="categoryChart"></div>
                    </div>
                </div>
            </div>

            <!-- Supplier Performance -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Customer Insights</h5>
                        <div id="supplierPerformanceChart"></div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Retention Rate</span>
                                <div class="progress" style="height: 8px; width: 60%;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $customerMetrics['retention_rate'] ?? 78 }}%" 
                                         aria-valuenow="{{ $customerMetrics['retention_rate'] ?? 78 }}" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="badge bg-light text-dark">{{ $customerMetrics['retention_rate'] ?? 78 }}%</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Repeat Purchase</span>
                                <div class="progress" style="height: 8px; width: 60%;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ $customerMetrics['repeat_purchase'] ?? 65 }}%" 
                                         aria-valuenow="{{ $customerMetrics['repeat_purchase'] ?? 65 }}" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="badge bg-light text-dark">{{ $customerMetrics['repeat_purchase'] ?? 65 }}%</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Customer Satisfaction</span>
                                <div class="progress" style="height: 8px; width: 60%;">
                                    <div class="progress-bar bg-info" role="progressbar" 
                                         style="width: {{ $customerMetrics['satisfaction'] ?? 92 }}%" 
                                         aria-valuenow="{{ $customerMetrics['satisfaction'] ?? 92 }}" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="badge bg-light text-dark">{{ $customerMetrics['satisfaction'] ?? 92 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products Table -->
            <div class="col-12">
                <div class="card top-selling overflow-auto">
                    <div class="card-body pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Top Selling Tools</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm active" data-view="sales">
                                    By Sales
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-view="revenue">
                                    By Revenue
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-view="profit">
                                    By Profit
                                </button>
                            </div>
                        </div>
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Sold</th>
                                    <th scope="col">Revenue</th>
                                    <th scope="col">Profit Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts ?? [] as $product)
                                <tr>
                                    <td><a href="#" class="text-primary fw-bold">{{ $product->name }}</a></td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ money($product->price) }}</td>
                                    <td class="fw-bold">{{ number_format($product->total_sold) }}</td>
                                    <td>{{ money($product->total_sold * $product->price) }}</td>
                                    <td>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $product->profit_margin ?? 35 }}%" 
                                                 aria-valuenow="{{ $product->profit_margin ?? 35 }}" 
                                                 aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">{{ $product->profit_margin ?? 35 }}%</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No products found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right side columns -->
    <div class="col-lg-4">
        <!-- Profitability Analysis -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Profitability Analysis</h5>
                <div id="profitabilityChart"></div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex flex-column align-items-center">
                                <h6 class="text-muted mb-1">Gross Margin</h6>
                                <h4 class="mb-0">{{ number_format($profitability['gross_margin'] ?? 42.5, 1) }}%</h4>
                                <small class="text-muted">Overall</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex flex-column align-items-center">
                                <h6 class="text-muted mb-1">Net Profit</h6>
                                <h4 class="mb-0">{{ number_format($profitability['net_profit'] ?? 22.8, 1) }}%</h4>
                                <small class="text-muted">After costs</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Health -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Inventory Health</h5>
                <div id="inventoryHealthChart"></div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Optimal Stock</span>
                        <span class="badge bg-success rounded-pill">{{ $inventoryHealth['optimal'] ?? 68 }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Overstocked</span>
                        <span class="badge bg-warning rounded-pill">{{ $inventoryHealth['overstocked'] ?? 22 }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Understocked</span>
                        <span class="badge bg-danger rounded-pill">{{ $inventoryHealth['understocked'] ?? 10 }}%</span>
                    </div>
                </div>
            </div>
            </div>

        <!-- Revenue by Category -->
        <div class="card">
            <div class="card-body pb-0">
                <h5 class="card-title">Revenue by Category</h5>
                <div id="revenueCategoryChart"></div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="card">
            <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                        <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="#" data-filter="critical">Critical Only</a></li>
                    <li><a class="dropdown-item" href="#" data-filter="all">All Low Stock</a></li>
                </ul>
            </div>
            <div class="card-body pb-0">
                <h5 class="card-title">Low Stock Items <span>| Critical Only</span></h5>
                <div class="news">
                    @forelse($lowStock ?? [] as $item)
                    <div class="post-item clearfix">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-circle text-{{ $item->stock_level < 30 ? 'danger' : 'warning' }} me-3 fs-4"></i>
                            <div>
                                <h4><a href="#">{{ $item->name }}</a></h4>
                                <p>Current: {{ $item->stock_quantity }} • Reorder: {{ $item->reorder_point }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">No critical stock alerts</div>
                    @endforelse
                </div>
            </div>
            </div>

        <!-- Recent Orders -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Recent Orders</h5>
                <div class="activity">
                    @forelse($recentOrders ?? [] as $order)
                    <div class="activity-item d-flex">
                        <div class="activite-label">{{ \Carbon\Carbon::now()->diffForHumans($order->created_at ?? now()) }}</div>
                        <i class='bi bi-circle-fill activity-badge 
                            {{ $order->status === 'completed' ? 'text-success' : 
                               ($order->status === 'pending' ? 'text-warning' : 'text-primary') }} align-self-start'></i>
                        <div class="activity-content">
                            <strong>#{{ $order->id }}</strong> - {{ $order->customer_name }}<br>
                            <span class="text-muted">{{ money($order->total) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">No recent orders</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date Range Selector
    const dateRangeSelect = document.getElementById('dateRange');
    const datePickerRange = document.querySelector('.date-picker-range');
    
    dateRangeSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            datePickerRange.classList.remove('d-none');
        } else {
            datePickerRange.classList.add('d-none');
            // Here you would typically make an AJAX call to update the data based on the selected range
        }
    });

    // Sales Trend Chart
    var salesTrendOptions = {
        series: [{
            name: 'Revenue',
            type: 'column',
            data: [44, 55, 57, 56, 61, 58, 63, 60, 66, 72, 68, 74]
        }, {
            name: 'Orders',
            type: 'line',
            data: [23, 32, 27, 38, 27, 32, 27, 38, 42, 44, 41, 48]
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: {
                show: true
            }
        },
        stroke: {
            width: [0, 4]
        },
        dataLabels: {
            enabled: false,
            enabledOnSeries: [1]
        },
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        xaxis: {
            type: 'category'
        },
        yaxis: [{
            title: {
                text: 'Revenue',
            },
        }, {
            opposite: true,
            title: {
                text: 'Orders'
            }
        }],
        colors: ['#4154f1', '#2eca6a']
    };

    var salesTrendChart = new ApexCharts(document.querySelector("#salesTrendChart"), salesTrendOptions);
    salesTrendChart.render();

    // Category Chart
    var categoryOptions = {
        series: [44, 55, 13, 43, 22],
        chart: {
            height: 350,
            type: 'pie',
        },
        labels: ['Baking Molds', 'Decorating Tools', 'Measuring Tools', 'Baking Accessories', 'Packaging'],
        colors: ['#4154f1', '#2eca6a', '#ff771d', '#a566ff', '#0dcaf0'],
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryOptions);
    categoryChart.render();

    // Product Matrix Chart
    var productMatrixOptions = {
        series: [{
            name: 'Profit Margin',
            data: [
                [20, 50, 'Product A', 'Stars'],
                [35, 30, 'Product B', 'Stars'],
                [50, 70, 'Product C', 'Stars'],
                [30, 40, 'Product D', 'Stars'],
                [80, 15, 'Product E', 'Question Marks'],
                [65, 25, 'Product F', 'Question Marks'],
                [75, 10, 'Product G', 'Question Marks'],
                [20, 85, 'Product H', 'Cash Cows'],
                [15, 60, 'Product I', 'Cash Cows'],
                [10, 75, 'Product J', 'Cash Cows'],
                [5, 20, 'Product K', 'Dogs'],
                [10, 15, 'Product L', 'Dogs']
            ]
        }],
        chart: {
            height: 350,
            type: 'scatter',
            zoom: {
                enabled: true,
                type: 'xy'
            }
        },
        xaxis: {
            title: {
                text: 'Profit Margin (%)'
            },
            tickAmount: 10,
            min: 0,
            max: 100
        },
        yaxis: {
            title: {
                text: 'Sales Volume'
            },
            tickAmount: 10,
            min: 0,
            max: 100
        },
        markers: {
            size: 12
        },
        tooltip: {
            custom: function({series, seriesIndex, dataPointIndex, w}) {
                const data = w.globals.initialSeries[seriesIndex].data[dataPointIndex];
                return '<div class="p-2">' +
                    '<div><strong>' + data[2] + '</strong></div>' +
                    '<div>Profit Margin: ' + data[0] + '%</div>' +
                    '<div>Sales Volume: ' + data[1] + '</div>' +
                    '<div>Category: ' + data[3] + '</div>' +
                    '</div>';
            }
        },
        colors: ['#4154f1', '#2eca6a', '#ff771d', '#dc3545'],
        grid: {
            borderColor: '#f1f1f1'
        }
    };

    var productMatrixChart = new ApexCharts(document.querySelector("#productMatrixChart"), productMatrixOptions);
    productMatrixChart.render();

    // Supplier Performance Chart
    var supplierPerformanceOptions = {
        series: [75, 68, 82, 91],
        chart: {
            height: 320,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                offsetY: 0,
                startAngle: 0,
                endAngle: 270,
                hollow: {
                    margin: 5,
                    size: '30%',
                    background: 'transparent',
                    image: undefined,
                },
                dataLabels: {
                    name: {
                        show: false,
                    },
                    value: {
                        show: false,
                    }
                }
            }
        },
        colors: ['#4154f1', '#2eca6a', '#ff771d', '#dc3545'],
        labels: ['Customer A', 'Customer B', 'Customer C', 'Customer D'],
        legend: {
            show: true,
            floating: true,
            fontSize: '12px',
            position: 'left',
            offsetX: -30,
            offsetY: 10,
            labels: {
                useSeriesColors: true,
            },
            markers: {
                size: 0
            },
            formatter: function(seriesName, opts) {
                return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex] + "%";
            },
            itemMargin: {
                vertical: 3
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                    show: false
                }
            }
        }]
    };

    var supplierPerformanceChart = new ApexCharts(document.querySelector("#supplierPerformanceChart"), supplierPerformanceOptions);
    supplierPerformanceChart.render();

    // Revenue by Category Chart
    var revenueCategoryOptions = {
        series: [42, 26, 15, 10, 7],
        chart: {
            height: 320,
            type: 'donut',
        },
        labels: ['Baking Molds', 'Decorating Tools', 'Measuring Tools', 'Baking Accessories', 'Packaging'],
        colors: ['#4154f1', '#2eca6a', '#ff771d', '#a566ff', '#0dcaf0'],
        legend: {
            position: 'bottom'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Revenue',
                            formatter: function (w) {
                                return '$' + w.globals.seriesTotals.reduce((a, b) => a + b, 0).toFixed(2);
                            }
                        }
                    }
                }
            }
        }
    };

    var revenueCategoryChart = new ApexCharts(document.querySelector("#revenueCategoryChart"), revenueCategoryOptions);
    revenueCategoryChart.render();

    // Profitability Chart
    var profitabilityOptions = {
        series: [{
            name: 'Profit',
            data: [2.4, 4.3, 5.1, 3.8, 4.9, 6.5, 5.7, 6.2, 7.1, 6.8, 7.7, 8.3]
        }, {
            name: 'Revenue',
            data: [8.5, 10.2, 12.5, 9.8, 11.5, 15.2, 13.9, 15.1, 17.3, 16.2, 18.4, 20.5]
        }, {
            name: 'Cost',
            data: [6.1, 5.9, 7.4, 6.0, 6.6, 8.7, 8.2, 8.9, 10.2, 9.4, 10.7, 12.2]
        }],
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: false
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        colors: ['#2eca6a', '#4154f1', '#dc3545'],
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        markers: {
            size: 4
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        },
        yaxis: {
            title: {
                text: 'Amount ($ thousands)'
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        }
    };

    var profitabilityChart = new ApexCharts(document.querySelector("#profitabilityChart"), profitabilityOptions);
    profitabilityChart.render();

    // Inventory Health Chart
    var inventoryHealthOptions = {
        series: [{{ $inventoryHealth['optimal'] ?? 68 }}, {{ $inventoryHealth['overstocked'] ?? 22 }}, {{ $inventoryHealth['understocked'] ?? 10 }}],
        chart: {
            height: 250,
            type: 'pie',
        },
        labels: ['Optimal', 'Overstocked', 'Understocked'],
        colors: ['#2eca6a', '#ffc107', '#dc3545'],
        legend: {
            show: false
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                }
            }
        }],
        plotOptions: {
            pie: {
                donut: {
                    size: '0%',
                }
            }
        }
    };

    var inventoryHealthChart = new ApexCharts(document.querySelector("#inventoryHealthChart"), inventoryHealthOptions);
    inventoryHealthChart.render();

    // Handle filter changes
    document.querySelectorAll('.dropdown-item[data-filter]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.dataset.filter;
            const card = this.closest('.card');
            const titleSpan = card.querySelector('.card-title span');
            if (titleSpan) {
                titleSpan.textContent = `| ${filter.charAt(0).toUpperCase() + filter.slice(1)}`;
            }
            // Here you would typically make an AJAX call to update the data
        });
    });

    // Handle view type changes for performance charts
    document.querySelectorAll('.btn-group[role="group"] .btn').forEach(button => {
        button.addEventListener('click', function() {
            const btnGroup = this.closest('.btn-group');
            btnGroup.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            // Here you would update the chart based on the selected view
        });
    });

    // Handle refresh button
    document.getElementById('refreshData').addEventListener('click', function() {
        // Add a spinning animation to the refresh icon
        this.querySelector('i').classList.add('rotating');
        
        // Make an AJAX call to refresh data
        setTimeout(() => {
            // Remove spinning animation after data is loaded
            this.querySelector('i').classList.remove('rotating');
        }, 1000);
    });
});
</script>

<style>
.rotating {
    animation: rotate 1s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush 