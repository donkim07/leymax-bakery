@extends('layouts.app')

@section('title', 'Global Inventory Report')

@section('content')
<div class="pagetitle">
    <h1>Global Inventory Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Inventory Status</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">
        <!-- Inventory Value by Business Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Inventory Value by Business</h5>
                    
                    <!-- Bar Chart -->
                    <div id="inventoryValueChart" style="min-height: 400px;" class="echart"></div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const businesses = [
                                @foreach($inventoryValue as $inventory)
                                    "{{ $inventory->business_name }}",
                                @endforeach
                            ];
                            
                            const values = [
                                @foreach($inventoryValue as $inventory)
                                    {{ $inventory->total_value }},
                                @endforeach
                            ];
                            
                            const colors = ['#4154f1', '#2eca6a', '#ff771d'];
                            
                            echarts.init(document.querySelector("#inventoryValueChart")).setOption({
                                tooltip: {
                                    trigger: 'axis',
                                    axisPointer: {
                                        type: 'shadow'
                                    },
                                    formatter: function(params) {
                                        return params[0].name + ': $' + params[0].value.toLocaleString();
                                    }
                                },
                                grid: {
                                    left: '3%',
                                    right: '4%',
                                    bottom: '3%',
                                    containLabel: true
                                },
                                xAxis: {
                                    type: 'category',
                                    data: businesses,
                                    axisTick: {
                                        alignWithLabel: true
                                    }
                                },
                                yAxis: {
                                    type: 'value',
                                    axisLabel: {
                                        formatter: '${value}'
                                    }
                                },
                                series: [{
                                    name: 'Inventory Value',
                                    type: 'bar',
                                    barWidth: '60%',
                                    data: values.map((value, index) => {
                                        return {
                                            value: value,
                                            itemStyle: {
                                                color: colors[index % colors.length]
                                            }
                                        }
                                    })
                                }]
                            });
                        });
                    </script>
                    <!-- End Bar Chart -->
                </div>
            </div>
        </div>

        <!-- Inventory Status Summary Card -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Inventory Status Summary</h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Items <span>| All Businesses</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-box"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $inventoryStatus->count() }}</h6>
                                            <span class="text-success small pt-1 fw-bold">SKUs</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Value <span>| All Businesses</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-currency-dollar"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>${{ number_format($inventoryValue->sum('total_value'), 2) }}</h6>
                                            <span class="text-primary small pt-1 fw-bold">Inventory Value</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card info-card revenue-card">
                                <div class="card-body">
                                    <h5 class="card-title">Low Stock <span>| Need Reorder</span></h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-exclamation-triangle"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $lowStockProducts->count() }}</h6>
                                            <span class="text-danger small pt-1 fw-bold">Items</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                {{ $lowStockProducts->count() }} items have reached their reorder level and need attention.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Low Stock Items Card -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Low Stock Items <span>| Need Reordering</span></h5>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="businessFilter">Business</label>
                                <select class="form-select" id="businessFilter">
                                    <option value="">All Businesses</option>
                                    @foreach($inventoryValue->pluck('business_name')->unique() as $business)
                                    <option value="{{ $business }}">{{ $business }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="categoryFilter">Category</label>
                                <select class="form-select" id="categoryFilter">
                                    <option value="">All Categories</option>
                                    <option value="bakery">Bakery</option>
                                    <option value="tools">Tools</option>
                                    <option value="ingredients">Ingredients</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="stockLevel">Stock Level</label>
                                <select class="form-select" id="stockLevel">
                                    <option value="low">Low Stock Only</option>
                                    <option value="all">All Stock Levels</option>
                                    <option value="critical">Critical (< 10% of reorder)</option>
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
                                    <th scope="col">Business</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">SKU</th>
                                    <th scope="col">Current Stock</th>
                                    <th scope="col">Reorder Level</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $product)
                                <tr>
                                    <td>{{ $product->business_name }}</td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ number_format($product->quantity) }}</td>
                                    <td>{{ number_format($product->reorder_level) }}</td>
                                    <td>
                                        @php
                                            $percentage = $product->reorder_level > 0 ? ($product->quantity / $product->reorder_level) * 100 : 100;
                                        @endphp
                                        
                                        @if($percentage < 50)
                                            <span class="badge bg-danger">Critical</span>
                                        @elseif($percentage < 100)
                                            <span class="badge bg-warning">Low</span>
                                        @else
                                            <span class="badge bg-success">OK</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">Reorder</a>
                                        <a href="#" class="btn btn-sm btn-info">Details</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- All Inventory Items Card -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">All Inventory Items</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Business</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">SKU</th>
                                    <th scope="col">Current Stock</th>
                                    <th scope="col">Reorder Level</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventoryStatus as $item)
                                <tr>
                                    <td>{{ $item->business_name }}</td>
                                    <td>{{ ucfirst($item->business_type) }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->sku }}</td>
                                    <td>{{ number_format($item->quantity) }}</td>
                                    <td>{{ number_format($item->reorder_level) }}</td>
                                    <td>
                                        @php
                                            $percentage = $item->reorder_level > 0 ? ($item->quantity / $item->reorder_level) * 100 : 100;
                                        @endphp
                                        
                                        @if($percentage < 50)
                                            <span class="badge bg-danger">Critical</span>
                                        @elseif($percentage < 100)
                                            <span class="badge bg-warning">Low</span>
                                        @else
                                            <span class="badge bg-success">OK</span>
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