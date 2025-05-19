@extends('layouts.app')

@section('content')
<div class="row">
    <!-- Left side columns -->
    <div class="col-lg-8">
        <div class="row">
            <!-- Sales Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card sales-card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#" data-filter="today">Today</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="month">This Month</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="year">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Total Sales <span>| Today</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-cart"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($totalSales) }}</h6>
                                <span class="text-success small pt-1 fw-bold">{{ number_format($salesGrowth, 1) }}%</span>
                                <span class="text-muted small pt-2 ps-1">increase</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#" data-filter="today">Today</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="month">This Month</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="year">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Total Revenue <span>| This Month</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="ps-3">
                                <h6>${{ number_format($totalRevenue) }}</h6>
                                <span class="text-success small pt-1 fw-bold">{{ number_format($revenueGrowth, 1) }}%</span>
                                <span class="text-muted small pt-2 ps-1">increase</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customers Card -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card customers-card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#" data-filter="today">Today</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="month">This Month</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="year">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Total Customers <span>| This Year</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($totalCustomers) }}</h6>
                                <span class="text-danger small pt-1 fw-bold">{{ number_format($customerGrowth, 1) }}%</span>
                                <span class="text-muted small pt-2 ps-1">increase</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Units Performance -->
            <div class="col-12">
                <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#" data-filter="today">Today</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="month">This Month</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="year">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Business Units Performance <span>| This Month</span></h5>

                        <!-- Line Chart -->
                        <div id="businessUnitsChart"></div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-12">
                <div class="card top-selling overflow-auto">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#" data-filter="today">Today</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="month">This Month</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="year">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body pb-0">
                        <h5 class="card-title">Top Selling Products <span>| Today</span></h5>
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Business Unit</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Sold</th>
                                    <th scope="col">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                <tr>
                                    <td><a href="#" class="text-primary fw-bold">{{ $product->name }}</a></td>
                                    <td>{{ $product->business_unit }}</td>
                                    <td>${{ number_format($product->price, 2) }}</td>
                                    <td class="fw-bold">{{ number_format($product->quantity_sold) }}</td>
                                    <td>${{ number_format($product->revenue) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right side columns -->
    <div class="col-lg-4">
        <!-- Revenue Distribution -->
        <div class="card">
            <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                        <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="#" data-filter="today">Today</a></li>
                    <li><a class="dropdown-item" href="#" data-filter="month">This Month</a></li>
                    <li><a class="dropdown-item" href="#" data-filter="year">This Year</a></li>
                </ul>
            </div>

            <div class="card-body pb-0">
                <h5 class="card-title">Revenue Distribution <span>| This Month</span></h5>
                <div id="revenueChart"></div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="card">
            <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                        <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="#" data-filter="all">All Units</a></li>
                    <li><a class="dropdown-item" href="#" data-filter="bakery">Bakery Shop</a></li>
                    <li><a class="dropdown-item" href="#" data-filter="tools">Cake Tools</a></li>
                </ul>
            </div>

            <div class="card-body pb-0">
                <h5 class="card-title">Low Stock Alerts <span>| All Units</span></h5>

                <div class="news">
                    @foreach($lowStockAlerts as $alert)
                    <div class="post-item clearfix">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-circle text-warning me-3 fs-4"></i>
                            <div>
                                <h4><a href="#">{{ $alert->product_name }}</a></h4>
                                <p>{{ $alert->business_unit }} • Current Stock: {{ $alert->current_stock }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Business Units Growth -->
        <div class="card">
            <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                        <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="#" data-filter="month">This Month</a></li>
                    <li><a class="dropdown-item" href="#" data-filter="year">This Year</a></li>
                </ul>
            </div>

            <div class="card-body pb-0">
                <h5 class="card-title">Business Units Growth <span>| This Year</span></h5>

                <div id="businessGrowthChart"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Business Units Performance Chart
    var businessUnitsOptions = {
        series: [{
            name: 'Bakery Shop',
            data: @json($businessUnitsData['bakery'] ?? [])
        }, {
            name: 'Cake Tools',
            data: @json($businessUnitsData['tools'] ?? [])
        }, {
            name: 'Academy',
            data: @json($businessUnitsData['academy'] ?? [])
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: {
                show: false
            }
        },
        markers: {
            size: 4
        },
        colors: ['#4154f1', '#2eca6a', '#ff771d'],
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.3,
                opacityTo: 0.4,
                stops: [0, 90, 100]
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: @json($businessUnitsData['labels'])
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy'
            }
        }
    };

    var businessUnitsChart = new ApexCharts(document.querySelector("#businessUnitsChart"), businessUnitsOptions);
    businessUnitsChart.render();

    // Revenue Distribution Chart
    var revenueOptions = {
        series: @json($revenueDistribution['values'] ?? []),
        chart: {
            height: 350,
            type: 'donut',
        },
        labels: @json($revenueDistribution['labels'] ?? []),
        colors: ['#4154f1', '#2eca6a', '#ff771d'],
        legend: {
            position: 'bottom'
        }
    };

    var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
    revenueChart.render();

    // Business Growth Chart
    var growthOptions = {
        series: [{
            name: 'Growth',
            data: @json($businessGrowth['values'])
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
            }
        },
        colors: ['#4154f1'],
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: @json($businessGrowth['labels']),
        }
    };

    var growthChart = new ApexCharts(document.querySelector("#businessGrowthChart"), growthOptions);
    growthChart.render();

    // Handle Filter Changes
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
});
</script>
@endpush 