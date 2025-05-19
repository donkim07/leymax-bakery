@extends('layouts.app')

@section('title', 'Global Sales Report')

@section('content')
<div class="pagetitle">
    <h1>Global Sales Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Sales Overview</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">
        <!-- Monthly Sales Overview Card -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Monthly Sales Trend <span>| Last 12 Months</span></h5>
                    
                    <!-- Line Chart -->
                    <div id="monthlySalesChart" style="min-height: 400px;" class="echart"></div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const months = [
                                @foreach($monthlySales as $sale)
                                    "{{ date('M Y', mktime(0, 0, 0, $sale->month, 1, $sale->year)) }}",
                                @endforeach
                            ].reverse();
                            
                            const salesData = [
                                @foreach($monthlySales as $sale)
                                    {{ $sale->total_sales }},
                                @endforeach
                            ].reverse();
                            
                            const orderData = [
                                @foreach($monthlySales as $sale)
                                    {{ $sale->order_count }},
                                @endforeach
                            ].reverse();
                            
                            echarts.init(document.querySelector("#monthlySalesChart")).setOption({
                                tooltip: {
                                    trigger: 'axis',
                                    formatter: function(params) {
                                        var tooltip = params[0].name + "<br/>";
                                        params.forEach(function(param) {
                                            tooltip += param.seriesName + ': ' + 
                                                (param.seriesIndex === 0 ? '$' + param.value.toLocaleString() : param.value) + 
                                                '<br/>';
                                        });
                                        return tooltip;
                                    }
                                },
                                legend: {
                                    data: ['Sales Amount', 'Order Count']
                                },
                                grid: {
                                    left: '3%',
                                    right: '4%',
                                    bottom: '3%',
                                    containLabel: true
                                },
                                xAxis: {
                                    type: 'category',
                                    boundaryGap: false,
                                    data: months
                                },
                                yAxis: [
                                    {
                                        type: 'value',
                                        name: 'Sales Amount',
                                        axisLabel: {
                                            formatter: '${value}'
                                        }
                                    },
                                    {
                                        type: 'value',
                                        name: 'Order Count',
                                        position: 'right'
                                    }
                                ],
                                series: [
                                    {
                                        name: 'Sales Amount',
                                        type: 'line',
                                        smooth: true,
                                        data: salesData,
                                        yAxisIndex: 0,
                                        itemStyle: {
                                            color: '#4154f1'
                                        }
                                    },
                                    {
                                        name: 'Order Count',
                                        type: 'line',
                                        smooth: true,
                                        data: orderData,
                                        yAxisIndex: 1,
                                        itemStyle: {
                                            color: '#2eca6a'
                                        }
                                    }
                                ]
                            });
                        });
                    </script>
                    <!-- End Line Chart -->
                </div>
            </div>
        </div>

        <!-- Top Products Card -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top Selling Products</h5>
                    
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">SKU</th>
                                <th scope="col">Quantity Sold</th>
                                <th scope="col">Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $key => $product)
                            <tr>
                                <th scope="row">{{ $key + 1 }}</th>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ number_format($product->total_quantity) }}</td>
                                <td>${{ number_format($product->total_sales, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sales by Business Type Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sales by Business Type</h5>
                    
                    <!-- Doughnut Chart -->
                    <div id="salesByBusinessChart" style="min-height: 400px;" class="echart"></div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const businessTypes = [
                                @foreach($salesByBusinessType as $business)
                                    "{{ ucfirst($business->type) }}",
                                @endforeach
                            ];
                            
                            const salesData = [
                                @foreach($salesByBusinessType as $business)
                                    {{ $business->total_sales }},
                                @endforeach
                            ];
                            
                            echarts.init(document.querySelector("#salesByBusinessChart")).setOption({
                                tooltip: {
                                    trigger: 'item',
                                    formatter: function(param) {
                                        return param.name + ': $' + param.value.toLocaleString() + 
                                               ' (' + param.percent + '%)';
                                    }
                                },
                                legend: {
                                    top: 'bottom'
                                },
                                series: [{
                                    name: 'Sales by Business Type',
                                    type: 'pie',
                                    radius: ['40%', '70%'],
                                    avoidLabelOverlap: false,
                                    itemStyle: {
                                        borderRadius: 10,
                                        borderColor: '#fff',
                                        borderWidth: 2
                                    },
                                    label: {
                                        show: false,
                                        position: 'center'
                                    },
                                    emphasis: {
                                        label: {
                                            show: true,
                                            fontSize: '18',
                                            fontWeight: 'bold'
                                        }
                                    },
                                    labelLine: {
                                        show: false
                                    },
                                    data: businessTypes.map((type, index) => {
                                        return {
                                            value: salesData[index],
                                            name: type
                                        }
                                    })
                                }]
                            });
                        });
                    </script>
                    <!-- End Doughnut Chart -->
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sales Data Table -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sales Data Summary</h5>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="daterange">Date Range</label>
                                <input type="text" class="form-control" id="daterange" name="daterange" value="Last 30 Days">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="businessType">Business Type</label>
                                <select class="form-select" id="businessType">
                                    <option value="">All Business Types</option>
                                    <option value="bakery">Bakery</option>
                                    <option value="tools">Tools</option>
                                    <option value="academy">Academy</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="reportType">Report Type</label>
                                <select class="form-select" id="reportType">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly" selected>Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Period</th>
                                    <th scope="col">Business Type</th>
                                    <th scope="col">Orders</th>
                                    <th scope="col">Total Sales</th>
                                    <th scope="col">Avg. Order Value</th>
                                    <th scope="col">Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesByBusinessType as $business)
                                <tr>
                                    <td>Last 12 Months</td>
                                    <td>{{ ucfirst($business->type) }}</td>
                                    <td>{{ number_format($business->order_count) }}</td>
                                    <td>${{ number_format($business->total_sales, 2) }}</td>
                                    <td>${{ number_format($business->order_count > 0 ? $business->total_sales / $business->order_count : 0, 2) }}</td>
                                    <td>
                                        @if($business->type === 'bakery')
                                        <span class="badge bg-success">+12%</span>
                                        @elseif($business->type === 'tools')
                                        <span class="badge bg-primary">+8%</span>
                                        @else
                                        <span class="badge bg-warning">+4%</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-outline-primary me-2">
                            <i class="bi bi-file-excel me-1"></i> Export Excel
                        </button>
                        <button type="button" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-file-pdf me-1"></i> Export PDF
                        </button>
                        <button type="button" class="btn btn-outline-info">
                            <i class="bi bi-send me-1"></i> Email Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize date range picker
        $('input[name="daterange"]').daterangepicker({
            opens: 'left',
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        });
        
        // Initialize DataTable
        $('.datatable').DataTable({
            paging: true,
            pageLength: 10,
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>
@endsection 