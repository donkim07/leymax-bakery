@extends('layouts.app')

@section('title', 'Global Financial Report')

@section('content')
<div class="pagetitle">
    <h1>Global Financial Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Financial Summary</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">
        <!-- Financial Overview Cards -->
        <div class="col-xxl-4 col-md-4">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Total Income <span>| Last 12 Months</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="ps-3">
                            <h6>${{ number_format($financialData->sum('total_income'), 2) }}</h6>
                            <span class="text-success small pt-1 fw-bold">+8%</span>
                            <span class="text-muted small pt-2 ps-1">from previous period</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-4">
            <div class="card info-card revenue-card">
                <div class="card-body">
                    <h5 class="card-title">Total Expenses <span>| Last 12 Months</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-cart"></i>
                        </div>
                        <div class="ps-3">
                            <h6>${{ number_format($financialData->sum('total_expenses'), 2) }}</h6>
                            <span class="text-danger small pt-1 fw-bold">+3%</span>
                            <span class="text-muted small pt-2 ps-1">from previous period</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-4">
            <div class="card info-card customers-card">
                <div class="card-body">
                    <h5 class="card-title">Net Profit <span>| Last 12 Months</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="ps-3">
                            <h6>${{ number_format($financialData->sum('net_profit'), 2) }}</h6>
                            <span class="text-success small pt-1 fw-bold">+11%</span>
                            <span class="text-muted small pt-2 ps-1">from previous period</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Income vs Expenses Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Income vs Expenses <span>| Last 12 Months</span></h5>
                    
                    <!-- Bar Chart -->
                    <div id="financialChart" style="min-height: 400px;" class="echart"></div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const months = [
                                @foreach($financialData as $data)
                                    "{{ date('M Y', mktime(0, 0, 0, $data->month, 1, $data->year)) }}",
                                @endforeach
                            ].reverse();
                            
                            const incomeData = [
                                @foreach($financialData as $data)
                                    {{ $data->total_income }},
                                @endforeach
                            ].reverse();
                            
                            const expenseData = [
                                @foreach($financialData as $data)
                                    {{ $data->total_expenses }},
                                @endforeach
                            ].reverse();
                            
                            const profitData = [
                                @foreach($financialData as $data)
                                    {{ $data->net_profit }},
                                @endforeach
                            ].reverse();
                            
                            echarts.init(document.querySelector("#financialChart")).setOption({
                                tooltip: {
                                    trigger: 'axis',
                                    axisPointer: {
                                        type: 'shadow'
                                    },
                                    formatter: function(params) {
                                        let tooltip = params[0].name + "<br/>";
                                        params.forEach(function(param) {
                                            tooltip += param.seriesName + ': $' + param.value.toLocaleString() + '<br/>';
                                        });
                                        return tooltip;
                                    }
                                },
                                legend: {
                                    data: ['Income', 'Expenses', 'Net Profit']
                                },
                                grid: {
                                    left: '3%',
                                    right: '4%',
                                    bottom: '3%',
                                    containLabel: true
                                },
                                xAxis: {
                                    type: 'category',
                                    data: months
                                },
                                yAxis: {
                                    type: 'value',
                                    axisLabel: {
                                        formatter: '${value}'
                                    }
                                },
                                series: [
                                    {
                                        name: 'Income',
                                        type: 'bar',
                                        stack: 'total',
                                        data: incomeData,
                                        itemStyle: {
                                            color: '#2eca6a'
                                        }
                                    },
                                    {
                                        name: 'Expenses',
                                        type: 'bar',
                                        stack: 'total',
                                        data: expenseData.map(value => -value),
                                        itemStyle: {
                                            color: '#ff771d'
                                        }
                                    },
                                    {
                                        name: 'Net Profit',
                                        type: 'line',
                                        data: profitData,
                                        itemStyle: {
                                            color: '#4154f1'
                                        }
                                    }
                                ]
                            });
                        });
                    </script>
                    <!-- End Bar Chart -->
                </div>
            </div>
        </div>

        <!-- Top Expense Categories Chart -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top Expense Categories</h5>
                    
                    <!-- Pie Chart -->
                    <div id="expenseCategoriesChart" style="min-height: 400px;" class="echart"></div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const categories = [
                                @foreach($topExpenses as $expense)
                                    "{{ ucfirst($expense->category) }}",
                                @endforeach
                            ];
                            
                            const amounts = [
                                @foreach($topExpenses as $expense)
                                    {{ $expense->total_amount }},
                                @endforeach
                            ];
                            
                            echarts.init(document.querySelector("#expenseCategoriesChart")).setOption({
                                tooltip: {
                                    trigger: 'item',
                                    formatter: function(param) {
                                        return param.name + ': $' + param.value.toLocaleString() + 
                                               ' (' + param.percent + '%)';
                                    }
                                },
                                legend: {
                                    orient: 'vertical',
                                    left: 'left'
                                },
                                series: [{
                                    name: 'Expense Categories',
                                    type: 'pie',
                                    radius: '60%',
                                    data: categories.map((category, index) => {
                                        return {
                                            value: amounts[index],
                                            name: category
                                        }
                                    }),
                                    emphasis: {
                                        itemStyle: {
                                            shadowBlur: 10,
                                            shadowOffsetX: 0,
                                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                                        }
                                    }
                                }]
                            });
                        });
                    </script>
                    <!-- End Pie Chart -->
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Business Type Financial Summary -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Financial Summary by Business Type</h5>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="daterange">Date Range</label>
                                <input type="text" class="form-control" id="daterange" name="daterange" value="Last 12 Months">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="compareWith">Compare With</label>
                                <select class="form-select" id="compareWith">
                                    <option value="previous_year">Previous Year</option>
                                    <option value="previous_quarter">Previous Quarter</option>
                                    <option value="previous_month">Previous Month</option>
                                    <option value="none">No Comparison</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="viewBy">View By</label>
                                <select class="form-select" id="viewBy">
                                    <option value="month">Monthly</option>
                                    <option value="quarter">Quarterly</option>
                                    <option value="year">Yearly</option>
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
                                    <th scope="col">Business Type</th>
                                    <th scope="col">Income</th>
                                    <th scope="col">Expenses</th>
                                    <th scope="col">Net Profit</th>
                                    <th scope="col">Profit Margin</th>
                                    <th scope="col">YoY Change</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($businessSummary as $summary)
                                <tr>
                                    <td>{{ ucfirst($summary->business_type) }}</td>
                                    <td>${{ number_format($summary->total_income, 2) }}</td>
                                    <td>${{ number_format($summary->total_expenses, 2) }}</td>
                                    <td>${{ number_format($summary->net_profit, 2) }}</td>
                                    <td>
                                        @if($summary->total_income > 0)
                                            {{ number_format(($summary->net_profit / $summary->total_income) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                    <td>
                                        @if($summary->business_type === 'bakery')
                                        <span class="badge bg-success">+15%</span>
                                        @elseif($summary->business_type === 'tools')
                                        <span class="badge bg-primary">+8%</span>
                                        @else
                                        <span class="badge bg-warning">+5%</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="table-primary">
                                    <td><strong>Total</strong></td>
                                    <td><strong>${{ number_format($businessSummary->sum('total_income'), 2) }}</strong></td>
                                    <td><strong>${{ number_format($businessSummary->sum('total_expenses'), 2) }}</strong></td>
                                    <td><strong>${{ number_format($businessSummary->sum('net_profit'), 2) }}</strong></td>
                                    <td>
                                        @if($businessSummary->sum('total_income') > 0)
                                            <strong>{{ number_format(($businessSummary->sum('net_profit') / $businessSummary->sum('total_income')) * 100, 1) }}%</strong>
                                        @else
                                            <strong>0%</strong>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-success">+11%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Financial Details Table -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Monthly Financial Details <span>| Last 12 Months</span></h5>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Period</th>
                                    <th scope="col">Income</th>
                                    <th scope="col">Expenses</th>
                                    <th scope="col">Net Profit</th>
                                    <th scope="col">Profit Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($financialData as $data)
                                <tr>
                                    <td>{{ date('F Y', mktime(0, 0, 0, $data->month, 1, $data->year)) }}</td>
                                    <td>${{ number_format($data->total_income, 2) }}</td>
                                    <td>${{ number_format($data->total_expenses, 2) }}</td>
                                    <td>${{ number_format($data->net_profit, 2) }}</td>
                                    <td>
                                        @if($data->total_income > 0)
                                            {{ number_format(($data->net_profit / $data->total_income) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td><strong>Total</strong></td>
                                    <td><strong>${{ number_format($financialData->sum('total_income'), 2) }}</strong></td>
                                    <td><strong>${{ number_format($financialData->sum('total_expenses'), 2) }}</strong></td>
                                    <td><strong>${{ number_format($financialData->sum('net_profit'), 2) }}</strong></td>
                                    <td>
                                        @if($financialData->sum('total_income') > 0)
                                            <strong>{{ number_format(($financialData->sum('net_profit') / $financialData->sum('total_income')) * 100, 1) }}%</strong>
                                        @else
                                            <strong>0%</strong>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
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
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'Last 3 Months': [moment().subtract(3, 'month'), moment()],
                'Last 6 Months': [moment().subtract(6, 'month'), moment()],
                'Last 12 Months': [moment().subtract(12, 'month'), moment()],
                'This Year': [moment().startOf('year'), moment()]
            },
            startDate: moment().subtract(12, 'month'),
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