@extends('layouts.app')

@section('title', 'Manufacturing Efficiency Metrics')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('bakery.dashboard') }}">Bakery</a></li>
<li class="breadcrumb-item"><a href="{{ route('bakery.manufacturing.assembly') }}">Manufacturing</a></li>
<li class="breadcrumb-item active">Efficiency Metrics</li>
@endsection

@section('content')
<div class="row">
    <!-- Efficiency Overview Card -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title m-0">Manufacturing Efficiency Overview</h5>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterMetricsModal">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Total Production Runs</h6>
                                <h3 class="card-title mb-0">{{ $totalRuns ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Average Waste (%)</h6>
                                <h3 class="card-title mb-0">{{ number_format($averageWaste ?? 0, 1) }}%</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Production Efficiency</h6>
                                <h3 class="card-title mb-0">{{ number_format($productionEfficiency ?? 0, 1) }}%</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Most Efficient Product</h6>
                                <h3 class="card-title mb-0">{{ $mostEfficientProduct ?? 'None' }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Efficiency Trend Chart -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title">Efficiency Trends (Last 30 Days)</h6>
                                <canvas id="efficiencyChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Efficiency Table -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Product Efficiency</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="productEfficiencyTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Production Runs</th>
                                <th>Avg. Time</th>
                                <th>Efficiency</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productEfficiency ?? [] as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->runs }}</td>
                                <td>{{ $product->avg_time }} min</td>
                                <td>{{ number_format($product->efficiency, 1) }}%</td>
                                <td>
                                    @if($product->trend > 0)
                                        <span class="text-success"><i class="bi bi-arrow-up"></i> {{ number_format($product->trend, 1) }}%</span>
                                    @elseif($product->trend < 0)
                                        <span class="text-danger"><i class="bi bi-arrow-down"></i> {{ number_format(abs($product->trend), 1) }}%</span>
                                    @else
                                        <span class="text-secondary"><i class="bi bi-dash"></i> 0%</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No product efficiency data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ingredient Usage Efficiency -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Ingredient Usage Efficiency</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="ingredientEfficiencyTable">
                        <thead>
                            <tr>
                                <th>Ingredient</th>
                                <th>Total Used</th>
                                <th>Waste %</th>
                                <th>Cost Impact</th>
                                <th>Recommendation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ingredientEfficiency ?? [] as $ingredient)
                            <tr>
                                <td>{{ $ingredient->name }}</td>
                                <td>{{ $ingredient->total_used }} {{ $ingredient->unit }}</td>
                                <td>{{ number_format($ingredient->waste_percentage, 1) }}%</td>
                                <td>${{ number_format($ingredient->cost_impact, 2) }}</td>
                                <td>
                                    @if($ingredient->waste_percentage > 10)
                                        <span class="badge bg-danger">Reduce Waste</span>
                                    @elseif($ingredient->waste_percentage > 5)
                                        <span class="badge bg-warning">Monitor Usage</span>
                                    @else
                                        <span class="badge bg-success">Efficient</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No ingredient efficiency data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Metrics Modal -->
<div class="modal fade" id="filterMetricsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="filterMetricsForm" action="{{ route('bakery.manufacturing.metrics') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Metrics</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assembled_item_id" class="form-label">Product</label>
                        <select class="form-select" id="assembled_item_id" name="assembled_item_id">
                            <option value="">All Products</option>
                            @foreach($assembledItems ?? [] as $item)
                                <option value="{{ $item->id }}" {{ request('assembled_item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('bakery.manufacturing.metrics') }}" class="btn btn-secondary">Reset</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize efficiency trend chart if there's data
        const efficiencyChartCanvas = document.getElementById('efficiencyChart');
        if (efficiencyChartCanvas) {
            const efficiencyChartData = @json($efficiencyChartData ?? []);
            
            if (efficiencyChartData && efficiencyChartData.labels) {
                new Chart(efficiencyChartCanvas, {
                    type: 'line',
                    data: {
                        labels: efficiencyChartData.labels,
                        datasets: [{
                            label: 'Production Efficiency',
                            data: efficiencyChartData.efficiency,
                            borderColor: 'rgb(54, 162, 235)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            tension: 0.3,
                            fill: true
                        }, {
                            label: 'Waste Percentage',
                            data: efficiencyChartData.waste,
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
        
        // Initialize DataTables if they exist
        if ($.fn.DataTable) {
            if (document.getElementById('productEfficiencyTable')) {
                $('#productEfficiencyTable').DataTable({
                    paging: false,
                    searching: true,
                    ordering: true,
                    info: false
                });
            }
            
            if (document.getElementById('ingredientEfficiencyTable')) {
                $('#ingredientEfficiencyTable').DataTable({
                    paging: false,
                    searching: true,
                    ordering: true,
                    info: false
                });
            }
        }
    });
</script>
@endpush 