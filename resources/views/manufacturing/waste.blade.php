@extends('layouts.app')

@section('title', 'Manufacturing Waste Management')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('bakery.dashboard') }}">Bakery</a></li>
<li class="breadcrumb-item"><a href="{{ route('bakery.manufacturing.assembly') }}">Manufacturing</a></li>
<li class="breadcrumb-item active">Waste Management</li>
@endsection

@section('content')
<div class="row">
    <!-- Waste Overview Card -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title m-0">Waste Overview</h5>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterWasteModal">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Total Waste Records</h6>
                                <h3 class="card-title mb-0">{{ $wasteCount ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Average Waste (%)</h6>
                                <h3 class="card-title mb-0">{{ number_format($averageWastePercentage ?? 0, 1) }}%</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Total Waste Value</h6>
                                <h3 class="card-title mb-0">${{ number_format($totalWasteValue ?? 0, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h6 class="card-subtitle mb-2 text-muted">Highest Waste Ingredient</h6>
                                <h3 class="card-title mb-0">{{ $highestWasteIngredient ?? 'None' }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Waste Trend Chart -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title">Waste Trends (Last 30 Days)</h6>
                                <canvas id="wasteChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Waste Records Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Waste Records</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="wasteTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Batch #</th>
                                <th>Product</th>
                                <th>Ingredient</th>
                                <th>Used Amount</th>
                                <th>Waste Amount</th>
                                <th>Waste %</th>
                                <th>Value Lost</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wasteRecords ?? [] as $waste)
                            <tr>
                                <td>{{ $waste->recorded_at ? $waste->recorded_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>{{ $waste->manufacturingProcess->batch_number ?? 'N/A' }}</td>
                                <td>{{ $waste->manufacturingProcess->assembledItem->name ?? 'N/A' }}</td>
                                <td>{{ $waste->source_name }}</td>
                                <td>{{ $waste->used_amount }} {{ $waste->unit }}</td>
                                <td>{{ $waste->waste_amount }} {{ $waste->unit }}</td>
                                <td>{{ number_format($waste->waste_percentage, 1) }}%</td>
                                <td>${{ number_format($waste->waste_value ?? 0, 2) }}</td>
                                <td>{{ $waste->reason }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No waste records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(isset($wasteRecords) && $wasteRecords->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $wasteRecords->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Filter Waste Modal -->
<div class="modal fade" id="filterWasteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="filterWasteForm" action="{{ route('bakery.manufacturing.waste') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Waste Records</h5>
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
                    
                    <div class="mb-3">
                        <label for="ingredient_type" class="form-label">Ingredient Type</label>
                        <select class="form-select" id="ingredient_type" name="ingredient_type">
                            <option value="">All Types</option>
                            <option value="ingredient" {{ request('ingredient_type') == 'ingredient' ? 'selected' : '' }}>Raw Ingredient</option>
                            <option value="product" {{ request('ingredient_type') == 'product' ? 'selected' : '' }}>Product</option>
                            <option value="assembled_item" {{ request('ingredient_type') == 'assembled_item' ? 'selected' : '' }}>Assembled Item</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="min_waste_percentage" class="form-label">Minimum Waste Percentage</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="min_waste_percentage" name="min_waste_percentage" min="0" max="100" step="0.1" value="{{ request('min_waste_percentage') }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('bakery.manufacturing.waste') }}" class="btn btn-secondary">Reset</a>
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
        // Initialize waste trend chart if there's data
        const wasteChartCanvas = document.getElementById('wasteChart');
        if (wasteChartCanvas) {
            const wasteChartData = @json($wasteChartData ?? []);
            
            if (wasteChartData && wasteChartData.labels) {
                new Chart(wasteChartCanvas, {
                    type: 'line',
                    data: {
                        labels: wasteChartData.labels,
                        datasets: [{
                            label: 'Waste Percentage',
                            data: wasteChartData.percentages,
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
        
        // Initialize DataTable for waste records if it exists
        if ($.fn.DataTable && document.getElementById('wasteTable')) {
            $('#wasteTable').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                info: false
            });
        }
    });
</script>
@endpush 