@extends('layouts.app')

@section('title', 'Bakery Dashboard')

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
            <!-- Sales Today Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Sales Today</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light">
                                <i class="bi bi-cart text-success"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ money($stats['sales_today'] ?? 2450) }}</h6>
                                @if(($stats['sales_growth'] ?? 15) > 0)
                                    <span class="text-success small pt-1 fw-bold">+{{ number_format($stats['sales_growth'] ?? 15, 1) }}%</span>
                                @else
                                    <span class="text-danger small pt-1 fw-bold">{{ number_format($stats['sales_growth'] ?? 15, 1) }}%</span>
                                @endif
                                <span class="text-muted small pt-2 ps-1">vs yesterday</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light">
                                <i class="bi bi-currency-dollar text-primary"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ money($stats['total_revenue'] ?? 38450) }}</h6>
                                @if(($stats['revenue_growth'] ?? 8.5) > 0)
                                    <span class="text-success small pt-1 fw-bold">+{{ number_format($stats['revenue_growth'] ?? 8.5, 1) }}%</span>
                                    @else
                                    <span class="text-danger small pt-1 fw-bold">{{ number_format(abs($stats['revenue_growth'] ?? 8.5), 1) }}%</span>
                                    @endif
                                <span class="text-muted small pt-2 ps-1">this month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Orders Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card customers-card">
                    <div class="card-body">
                        <h5 class="card-title">Active Orders</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning-light">
                                <i class="bi bi-clock-history text-warning"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $stats['active_orders'] ?? 24 }}</h6>
                                <span class="text-muted small pt-2">{{ $stats['urgent_orders'] ?? 5 }} urgent</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Production -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card production-card">
                    <div class="card-body">
                        <h5 class="card-title">Daily Production</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light">
                                <i class="bi bi-box text-primary"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $stats['daily_production'] ?? 0 }}</h6>
                                @if(($stats['production_growth'] ?? 0) > 0)
                                    <span class="text-success small pt-1 fw-bold">+{{ number_format($stats['production_growth'] ?? 0, 1) }}%</span>
                                @else
                                    <span class="text-danger small pt-1 fw-bold">{{ number_format(abs($stats['production_growth'] ?? 0), 1) }}%</span>
                                @endif
                                <span class="text-muted small pt-2 ps-1">vs last month</span>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manufacturing Efficiency -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Manufacturing Efficiency</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light">
                                <i class="bi bi-bar-chart-line text-success"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['efficiency_rate'] ?? 85.7, 1) }}%</h6>
                                @if(($stats['efficiency_growth'] ?? 0) > 0)
                                    <span class="text-success small pt-1 fw-bold">+{{ number_format($stats['efficiency_growth'] ?? 0, 1) }}%</span>
                                    @else
                                    <span class="text-danger small pt-1 fw-bold">{{ number_format(abs($stats['efficiency_growth'] ?? 0), 1) }}%</span>
                                    @endif
                                    <span class="text-muted small pt-2 ps-1">vs last month</span>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material Waste -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card waste-card">
                    <div class="card-body">
                        <h5 class="card-title">Material Waste</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-light">
                                <i class="bi bi-trash text-danger"></i>
                    </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['waste_rate'] ?? 4.2, 1) }}%</h6>
                                <span class="text-muted small pt-2">{{ $stats['waste_target'] ?? '5' }}% target</span>
                </div>
            </div>
                            </div>
                        </div>
                                                </div>

            <!-- Low Stock Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Low Stock Items</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning-light">
                                <i class="bi bi-exclamation-triangle text-warning"></i>
                                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['low_stock'] ?? 8) }}</h6>
                                <span class="text-muted small pt-2">{{ $stats['out_of_stock'] ?? 2 }} out of stock</span>
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
                                <h6>{{ money($stats['total_credits'] ?? 7385.50) }}</h6>
                                <span class="text-muted small pt-2">{{ $stats['credit_count'] ?? 14 }} customers</span>
                                    </div>
                                </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 6px">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ min(($stats['credit_payment_rate'] ?? 82) , 100) }}%" 
                                     aria-valuenow="{{ $stats['credit_payment_rate'] ?? 82 }}" aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Payment Rate: {{ $stats['credit_payment_rate'] ?? 82 }}%</small>
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
                                <h6>{{ money($stats['total_debits'] ?? 4280.75) }}</h6>
                                <span class="text-muted small pt-2">{{ $stats['debit_count'] ?? 6 }} suppliers</span>
                                </div>
                                    </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 6px">
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: {{ min(($stats['debit_due_soon_percent'] ?? 40) , 100) }}%" 
                                     aria-valuenow="{{ $stats['debit_due_soon_percent'] ?? 40 }}" aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                                </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Due within 30 days: {{ $stats['debit_due_soon_percent'] ?? 40 }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Production Trend Chart -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Production & Efficiency Trends</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-graph-up"></i> Metrics
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-metric="production">Production</a></li>
                                    <li><a class="dropdown-item" href="#" data-metric="efficiency">Efficiency</a></li>
                                    <li><a class="dropdown-item" href="#" data-metric="waste">Waste</a></li>
                                    <li><a class="dropdown-item" href="#" data-metric="combined">Combined View</a></li>
                                </ul>
                            </div>
                        </div>
                        <div id="productionChart"></div>
                    </div>
                            </div>
                        </div>
                        
            <!-- Manufacturing Analytics Tabs -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manufacturing Analytics</h5>
                        
                        <!-- Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-tab-content" type="button" role="tab" aria-controls="categories" aria-selected="true">Production by Category</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly-tab-content" type="button" role="tab" aria-controls="weekly" aria-selected="false">Weekly Trends</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="efficiency-tab" data-bs-toggle="tab" data-bs-target="#efficiency-tab-content" type="button" role="tab" aria-controls="efficiency" aria-selected="false">Efficiency Metrics</button>
                            </li>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content pt-2">
                            <div class="tab-pane fade show active" id="categories-tab-content" role="tabpanel" aria-labelledby="categories-tab">
                                <div id="productCategoryChart" style="height: 350px;"></div>
                                </div>
                            <div class="tab-pane fade" id="weekly-tab-content" role="tabpanel" aria-labelledby="weekly-tab">
                                <div id="weeklyProductionChart" style="height: 350px;"></div>
                            </div>
                            <div class="tab-pane fade" id="efficiency-tab-content" role="tabpanel" aria-labelledby="efficiency-tab">
                                <div id="efficiencyMetricsChart" style="height: 350px;"></div>
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>

            <!-- Product Quality Chart -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Product Quality Rates</h5>
                        <div id="qualityRatesChart"></div>
                    </div>
                </div>
            </div>

            <!-- Production Distribution -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Production Distribution</h5>
                        <div id="productionDistributionChart"></div>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-6 border-end">
                                    <div class="d-flex flex-column align-items-center">
                                        <h6 class="text-muted mb-1">Top Category</h6>
                                        <h4 class="mb-0">{{ $productionData['top_category'] ?? 'Cakes' }}</h4>
                                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column align-items-center">
                                        <h6 class="text-muted mb-1">Batch Size</h6>
                                        <h4 class="mb-0">{{ $productionData['avg_batch_size'] ?? '152' }}</h4>
                                        <small class="text-muted">units/batch</small>
                                    </div>
                                </div>
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
                            <h5 class="card-title">Top Products</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm active" data-view="volume">
                                    By Volume
                                                        </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-view="efficiency">
                                    By Efficiency
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-view="quality">
                                    By Quality
                                                        </button>
                                                    </div>
                                </div>
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Units</th>
                                    <th scope="col">Efficiency</th>
                                    <th scope="col">Quality</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts ?? [] as $product)
                                <tr>
                                    <td><a href="#" class="text-primary fw-bold">{{ $product->name ?? 'Chocolate Cake' }}</a></td>
                                    <td>{{ $product->category ?? 'Cakes' }}</td>
                                    <td class="fw-bold">{{ number_format($product->units ?? 1250) }}</td>
                                    <td>{{ number_format($product->efficiency ?? 92.5, 1) }}%</td>
                                    <td>{{ number_format($product->quality ?? 98.7, 1) }}%</td>
                                    <td>
                                        @if(($product->status ?? 'active') == 'active')
                                            <span class="badge bg-success">Active</span>
                                            @else
                                            <span class="badge bg-secondary">Inactive</span>
                                            @endif
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
        <!-- Product Category Distribution -->
                <div class="card">
            <div class="card-body pb-0">
                <h5 class="card-title">Product Categories</h5>
                <div id="categoryDistributionChart"></div>
                </div>
            </div>

        <!-- Production Efficiency -->
                <div class="card">
                    <div class="card-body">
                <h5 class="card-title">Production Efficiency</h5>
                <div id="efficiencyChart"></div>
                        <div class="mt-3">
                    <div class="row">
                        <div class="col-6 border-end">
                            <div class="d-flex flex-column align-items-center">
                                <h6 class="text-muted mb-1">Target</h6>
                                <h4 class="mb-0">{{ $efficiency['target'] ?? 90 }}%</h4>
                                    </div>
                                </div>
                                <div class="col-6">
                            <div class="d-flex flex-column align-items-center">
                                <h6 class="text-muted mb-1">Current</h6>
                                <h4 class="mb-0">{{ number_format($efficiency['current'] ?? 85.7, 1) }}%</h4>
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Material Usage -->
        <div class="card">
                    <div class="card-body">
                <h5 class="card-title">Material Usage</h5>
                <div id="materialUsageChart"></div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Flour</span>
                        <span class="badge bg-primary rounded-pill">{{ $materialUsage['flour'] ?? 35 }}%</span>
                            </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Sugar</span>
                        <span class="badge bg-success rounded-pill">{{ $materialUsage['sugar'] ?? 25 }}%</span>
                        </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Dairy</span>
                        <span class="badge bg-info rounded-pill">{{ $materialUsage['dairy'] ?? 20 }}%</span>
                                </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Other Ingredients</span>
                        <span class="badge bg-secondary rounded-pill">{{ $materialUsage['other'] ?? 20 }}%</span>
                            </div>
                                </div>
            </div>
        </div>

        <!-- Payment Methods Card -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Payment Methods</h5>
                <div id="paymentMethodsChart"></div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-credit-card-fill text-primary me-2"></i>
                            <span>Credit Card</span>
                                                    </div>
                            <div>
                            <span class="badge bg-primary rounded-pill">{{ $paymentMethods['credit'] ?? 45 }}%</span>
                            <span class="ms-2">{{ money($paymentTotals['credit'] ?? 12500) }}</span>
                            </div>
                                </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-credit-card text-success me-2"></i>
                            <span>Debit Card</span>
                            </div>
                        <div>
                            <span class="badge bg-success rounded-pill">{{ $paymentMethods['debit'] ?? 30 }}%</span>
                            <span class="ms-2">{{ money($paymentTotals['debit'] ?? 8300) }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-cash text-warning me-2"></i>
                            <span>Cash</span>
                    </div>
                        <div>
                            <span class="badge bg-warning rounded-pill">{{ $paymentMethods['cash'] ?? 20 }}%</span>
                            <span class="ms-2">{{ money($paymentTotals['cash'] ?? 5500) }}</span>
                                </div>
                            </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-wallet2 text-info me-2"></i>
                            <span>Other</span>
                        </div>
                        <div>
                            <span class="badge bg-info rounded-pill">{{ $paymentMethods['other'] ?? 5 }}%</span>
                            <span class="ms-2">{{ money($paymentTotals['other'] ?? 1400) }}</span>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <!-- Recent Production Batches -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Recent Production Batches</h5>
                <div class="activity">
                    @forelse($recentBatches ?? [] as $batch)
                    <div class="activity-item d-flex">
                        <div class="activite-label">{{ \Carbon\Carbon::now()->diffForHumans($batch->created_at ?? now()) }}</div>
                        <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                        <div class="activity-content">
                            <strong>Batch #{{ $batch->id ?? '12345' }}</strong><br>
                            {{ $batch->product ?? 'Chocolate Cake' }} - {{ $batch->units ?? 150 }} units
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">No recent batches</div>
                    @endforelse
                    </div>
            </div>
        </div>

        <!-- Upcoming Maintenance -->
        <div class="card">
            <div class="card-body pb-0">
                <h5 class="card-title">Upcoming Maintenance</h5>
                <div class="news">
                    @forelse($upcomingMaintenance ?? [] as $maintenance)
                    <div class="post-item clearfix">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-tools text-primary me-3 fs-4"></i>
                            <div>
                                <h4><a href="#">{{ $maintenance->equipment ?? 'Mixer #3' }}</a></h4>
                                <p>{{ $maintenance->date ? $maintenance->date->format('M d, Y') : 'June 15, 2023' }} • {{ $maintenance->type ?? 'Preventive' }}</p>
                </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted mb-3">No upcoming maintenance</div>
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

    // Production Chart
    var productionOptions = {
        series: [{
            name: 'Production',
            type: 'column',
            data: [1240, 1350, 1270, 1450, 1380, 1250, 1480, 1520, 1350, 1650, 1580, 1740]
        }, {
            name: 'Efficiency',
            type: 'line',
            data: [82, 83, 81, 84, 86, 85, 87, 88, 86, 89, 88, 90]
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
                text: 'Production (units)',
            },
        }, {
            opposite: true,
            title: {
                text: 'Efficiency (%)'
            }
        }],
        colors: ['#4154f1', '#2eca6a']
    };

    var productionChart = new ApexCharts(document.querySelector("#productionChart"), productionOptions);
    productionChart.render();

    // Quality Rates Chart
    var qualityOptions = {
        series: [
            {
                name: 'Quality Rate',
                data: [98.5, 97.8, 99.2, 96.5, 97.2, 98.8, 97.9, 99.4, 98.2]
            }
        ],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
                distributed: true,
        dataLabels: {
                    position: 'bottom'
                },
            }
        },
        colors: ['#4154f1', '#2eca6a', '#ff771d', '#0dcaf0', '#a566ff', '#6f42c1', '#fd7e14', '#20c997', '#0d6efd'],
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
                colors: ['#fff']
            },
            formatter: function(val, opt) {
                return val + '%';
            },
            offsetX: 0
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        xaxis: {
            categories: ['Cakes', 'Pastries', 'Bread', 'Cookies', 'Cupcakes', 'Doughnuts', 'Pies', 'Muffins', 'Specialty'],
            labels: {
                formatter: function(val) {
                    return val + '%';
                }
            }
        },
        yaxis: {
            labels: {
                show: true
            }
        },
        tooltip: {
            theme: 'dark',
            x: {
                show: false
            },
            y: {
                title: {
                    formatter: function() {
                        return 'Quality';
                    }
                },
                formatter: function(val) {
                    return val + '%';
                }
            }
        }
    };

    var qualityRatesChart = new ApexCharts(document.querySelector("#qualityRatesChart"), qualityOptions);
    qualityRatesChart.render();

    // Production Distribution Chart
    var productionDistributionOptions = {
        series: [45, 35, 20],
        chart: {
            height: 240,
            type: 'donut',
        },
        labels: ['Made to Order', 'Daily Production', 'Special Products'],
        colors: ['#ff6384', '#36a2eb', '#ffcd56'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%'
                }
            }
                },
                legend: {
                    position: 'bottom'
                }
    };

    var productionDistributionChart = new ApexCharts(document.querySelector("#productionDistributionChart"), productionDistributionOptions);
    productionDistributionChart.render();

    // Product Category Chart for Manufacturing Analytics
    var productCategoryOptions = {
        series: [{
            name: 'Production Volume',
            data: [3200, 2800, 1950, 1600, 1250, 980, 840, 720, 650]
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '60%',
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: ['Cakes', 'Pastries', 'Bread', 'Cookies', 'Cupcakes', 'Doughnuts', 'Pies', 'Muffins', 'Specialty'],
            labels: {
                rotate: -45,
                rotateAlways: true
            }
        },
        yaxis: {
            title: {
                text: 'Units Produced'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " units"
                }
            }
        },
        colors: ['#4154f1']
    };

    var productCategoryChart = new ApexCharts(document.querySelector("#productCategoryChart"), productCategoryOptions);
    productCategoryChart.render();

    // Weekly Production Chart for Manufacturing Analytics
    var weeklyProductionOptions = {
        series: [{
            name: 'This Week',
            data: [290, 350, 320, 380, 410, 450, 280]
        }, {
            name: 'Last Week',
            data: [270, 320, 310, 360, 390, 420, 250]
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            type: 'category',
            categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]
        },
        yaxis: {
            title: {
                text: 'Production (units)'
            }
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
        },
        colors: ['#4154f1', '#8f92db']
    };

    var weeklyProductionChart = new ApexCharts(document.querySelector("#weeklyProductionChart"), weeklyProductionOptions);
    weeklyProductionChart.render();

    // Efficiency Metrics Chart for Manufacturing Analytics
    var efficiencyMetricsOptions = {
        series: [{
            name: 'Current',
            data: [85.7, 92.3, 75.5, 78.9, 98.5]
        }, {
            name: 'Target',
            data: [90, 95, 80, 85, 95]
        }],
        chart: {
            type: 'radar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        stroke: {
            width: 2
        },
        fill: {
            opacity: 0.1
        },
        markers: {
            size: 0
        },
        xaxis: {
            categories: ['Overall', 'Production', 'Resource Usage', 'Labor', 'Quality']
        },
        colors: ['#4154f1', '#ff771d']
    };

    var efficiencyMetricsChart = new ApexCharts(document.querySelector("#efficiencyMetricsChart"), efficiencyMetricsOptions);
    efficiencyMetricsChart.render();

    // Category Distribution Chart
    var categoryOptions = {
        series: [30, 25, 15, 10, 10, 10],
        chart: {
            height: 350,
            type: 'donut',
        },
        labels: ['Cakes', 'Pastries', 'Bread', 'Cookies', 'Cupcakes', 'Other'],
        colors: ['#4154f1', '#2eca6a', '#ff771d', '#a566ff', '#0dcaf0', '#ffc107'],
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
        }],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Products',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        }
    };

    var categoryDistributionChart = new ApexCharts(document.querySelector("#categoryDistributionChart"), categoryOptions);
    categoryDistributionChart.render();

    // Efficiency Chart
    var efficiencyOptions = {
        series: [{{ $efficiency['current'] ?? 85.7 }}],
        chart: {
            height: 220,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '70%',
                },
                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        show: true,
                        fontSize: '28px',
                        fontWeight: 'bold',
                        formatter: function(val) {
                            return val + '%';
                        }
                    }
                }
            }
        },
        fill: {
            colors: ['#2eca6a']
        },
        labels: ['Efficiency'],
    };

    var efficiencyChart = new ApexCharts(document.querySelector("#efficiencyChart"), efficiencyOptions);
    efficiencyChart.render();

    // Material Usage Chart
    var materialUsageOptions = {
        series: [{{ $materialUsage['flour'] ?? 35 }}, {{ $materialUsage['sugar'] ?? 25 }}, {{ $materialUsage['dairy'] ?? 20 }}, {{ $materialUsage['other'] ?? 20 }}],
        chart: {
            height: 240,
            type: 'pie',
        },
        labels: ['Flour', 'Sugar', 'Dairy', 'Other Ingredients'],
        colors: ['#4154f1', '#2eca6a', '#0dcaf0', '#adb5bd'],
        legend: {
            show: false
        },
        plotOptions: {
            pie: {
                customScale: 0.9,
                donut: {
                    size: '0%'
                }
            }
        }
    };

    var materialUsageChart = new ApexCharts(document.querySelector("#materialUsageChart"), materialUsageOptions);
    materialUsageChart.render();

    // Payment Methods Chart
    var paymentMethodsOptions = {
        series: [
            {{ $paymentMethods['credit'] ?? 45 }}, 
            {{ $paymentMethods['debit'] ?? 30 }}, 
            {{ $paymentMethods['cash'] ?? 20 }}, 
            {{ $paymentMethods['other'] ?? 5 }}
        ],
        chart: {
            height: 240,
            type: 'donut',
        },
        labels: ['Credit Card', 'Debit Card', 'Cash', 'Other'],
        colors: ['#4154f1', '#2eca6a', '#ffc107', '#0dcaf0'],
        legend: {
            show: false
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Sales',
                            formatter: function () {
                                return '{{ money($paymentTotals["total"] ?? 27700) }}';
                            }
                        }
                    }
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                }
            }
        }]
    };

    var paymentMethodsChart = new ApexCharts(document.querySelector("#paymentMethodsChart"), paymentMethodsOptions);
    paymentMethodsChart.render();

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

    // Handle metric changes for production chart
    document.querySelectorAll('.dropdown-item[data-metric]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const metric = this.dataset.metric;
            // Update chart based on selected metric
            if (metric === 'production') {
                productionChart.updateOptions({
                    yaxis: [{
                        title: {
                            text: 'Production (units)'
                        }
                    }]
                });
            } else if (metric === 'efficiency') {
                productionChart.updateOptions({
                    yaxis: [{
                        title: {
                            text: 'Efficiency (%)'
                        }
                    }]
                });
            }
        });
    });

    // Handle view type changes for products table
    document.querySelectorAll('.btn-group[role="group"] .btn').forEach(button => {
        button.addEventListener('click', function() {
            const btnGroup = this.closest('.btn-group');
            btnGroup.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            // Here you would update the table based on the selected view
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

.stars {
    color: #ffc107;
}
</style>
@endpush 