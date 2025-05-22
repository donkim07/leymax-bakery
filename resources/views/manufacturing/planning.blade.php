@extends('layouts.app')

@section('title', 'Production Planning')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('bakery.dashboard') }}">Bakery</a></li>
<li class="breadcrumb-item"><a href="{{ route('bakery.manufacturing.process') }}">Manufacturing</a></li>
<li class="breadcrumb-item active">Production Planning</li>
@endsection

@section('content')
<div class="row">
    <!-- Calendar View -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Production Calendar</h5>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleProductionModal">
                            <i class="bi bi-plus-circle me-1"></i> Schedule Production
                        </button>
                    </div>
                </div>
                <p class="card-text">Plan and schedule manufacturing activities across days and weeks.</p>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" id="prevMonth"><i class="bi bi-arrow-left"></i></button>
                            <input type="month" class="form-control" id="monthPicker" value="{{ date('Y-m') }}">
                            <button class="btn btn-outline-secondary" type="button" id="nextMonth"><i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" data-view="month">Month</button>
                            <button type="button" class="btn btn-outline-primary" data-view="week">Week</button>
                            <button type="button" class="btn btn-outline-primary" data-view="day">Day</button>
                            <button type="button" class="btn btn-outline-primary" data-view="list">List</button>
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-outline-secondary" id="todayBtn">Today</button>
                        <div class="btn-group ms-2">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><input type="checkbox" checked> Bread Products</a></li>
                                <li><a class="dropdown-item" href="#"><input type="checkbox" checked> Cake Products</a></li>
                                <li><a class="dropdown-item" href="#"><input type="checkbox" checked> Pastries</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><input type="checkbox" checked> High Priority</a></li>
                                <li><a class="dropdown-item" href="#"><input type="checkbox" checked> Normal Priority</a></li>
                                <li><a class="dropdown-item" href="#"><input type="checkbox" checked> Low Priority</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div id="productionCalendar" style="height: 600px;" class="border rounded p-2"></div>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Productions -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Upcoming Production Schedule</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Product</th>
                                <th>Batch Size</th>
                                <th>Priority</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Tomorrow, 6:00 AM</td>
                                <td>Whole Wheat Bread</td>
                                <td>40 loaves</td>
                                <td><span class="badge bg-warning">High</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Tomorrow, 8:00 AM</td>
                                <td>Vanilla Cake Base</td>
                                <td>30 units</td>
                                <td><span class="badge bg-info">Normal</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Tomorrow, 10:00 AM</td>
                                <td>Croissants</td>
                                <td>100 units</td>
                                <td><span class="badge bg-info">Normal</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Tomorrow, 2:00 PM</td>
                                <td>Chocolate Frosting</td>
                                <td>25 kg</td>
                                <td><span class="badge bg-success">Low</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Resource Allocation -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Resource Allocation</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Resource</th>
                                <th>Today</th>
                                <th>Tomorrow</th>
                                <th>Capacity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Mixing Station 1</td>
                                <td>75%</td>
                                <td>90%</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Available</span></td>
                            </tr>
                            <tr>
                                <td>Mixing Station 2</td>
                                <td>100%</td>
                                <td>80%</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td><span class="badge bg-danger">Fully Booked</span></td>
                            </tr>
                            <tr>
                                <td>Oven 1</td>
                                <td>60%</td>
                                <td>85%</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Available</span></td>
                            </tr>
                            <tr>
                                <td>Oven 2</td>
                                <td>90%</td>
                                <td>40%</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td><span class="badge bg-warning">Limited</span></td>
                            </tr>
                            <tr>
                                <td>Decoration Station</td>
                                <td>50%</td>
                                <td>65%</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Available</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Required Materials -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Required Materials</h5>
                <p class="card-text">Forecasted raw material requirements for scheduled production</p>
                
                <ul class="nav nav-tabs nav-tabs-bordered" id="materialsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="today-tab" data-bs-toggle="tab" data-bs-target="#today-materials" type="button" role="tab" aria-controls="today-materials" aria-selected="true">Today</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tomorrow-tab" data-bs-toggle="tab" data-bs-target="#tomorrow-materials" type="button" role="tab" aria-controls="tomorrow-materials" aria-selected="false">Tomorrow</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="week-tab" data-bs-toggle="tab" data-bs-target="#week-materials" type="button" role="tab" aria-controls="week-materials" aria-selected="false">This Week</button>
                    </li>
                </ul>
                <div class="tab-content pt-2" id="materialsTabContent">
                    <div class="tab-pane fade show active" id="today-materials" role="tabpanel" aria-labelledby="today-tab">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Required</th>
                                        <th>In Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Flour</td>
                                        <td>50 kg</td>
                                        <td>120 kg</td>
                                        <td><span class="badge bg-success">Sufficient</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">View Details</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sugar</td>
                                        <td>25 kg</td>
                                        <td>40 kg</td>
                                        <td><span class="badge bg-success">Sufficient</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">View Details</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Eggs</td>
                                        <td>120 units</td>
                                        <td>90 units</td>
                                        <td><span class="badge bg-danger">Insufficient</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger">Order Now</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Vanilla Extract</td>
                                        <td>2 L</td>
                                        <td>3.5 L</td>
                                        <td><span class="badge bg-success">Sufficient</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">View Details</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Baking Powder</td>
                                        <td>1 kg</td>
                                        <td>1.2 kg</td>
                                        <td><span class="badge bg-warning">Low Stock</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-warning">Order Soon</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tomorrow-materials" role="tabpanel" aria-labelledby="tomorrow-tab">
                        <!-- Similar table for tomorrow's materials -->
                        <div class="alert alert-info mt-3">
                            Material requirements for tomorrow's production shown here.
                        </div>
                    </div>
                    <div class="tab-pane fade" id="week-materials" role="tabpanel" aria-labelledby="week-tab">
                        <!-- Similar table for week's materials -->
                        <div class="alert alert-info mt-3">
                            Aggregated material requirements for this week's production shown here.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Production Modal -->
<div class="modal fade" id="scheduleProductionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="scheduleProductionForm" action="{{ route('bakery.manufacturing.planning.store') }}" method="POST">
                @csrf
                <input type="hidden" name="business_id" value="{{ session('business_id') }}">
                <input type="hidden" name="company_id" value="{{ session('company_id') }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Production</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="assembled_item_id" class="form-label">Product</label>
                            <select class="form-select searchable" id="assembled_item_id" name="assembled_item_id" required placeholder="Select product">
                                <option value="">Select Product</option>
                                @foreach($assembledItems ?? [] as $item)
                                    <option value="{{ $item->id }}" data-unit="{{ $item->unit }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="high">High</option>
                                <option value="normal" selected>Normal</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="scheduled_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="scheduled_time" class="form-label">Time</label>
                            <input type="time" class="form-control" id="scheduled_time" name="scheduled_time">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="resource_allocation" class="form-label">Resource Allocation</label>
                            <div class="row">
                                @foreach(['Mixing Station 1', 'Mixing Station 2', 'Oven 1', 'Oven 2', 'Decoration Station'] as $index => $resource)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="resource_allocation[]" value="{{ $resource }}" id="resource{{ $index }}">
                                        <label class="form-check-label" for="resource{{ $index }}">
                                            {{ $resource }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule Production</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize FullCalendar
        const calendarEl = document.getElementById('productionCalendar');
        if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: '',
                    center: 'title',
                    right: ''
                },
                events: [
                    @foreach($productionPlans ?? [] as $plan)
                    {
                        title: '{{ $plan->title }}',
                        start: '{{ $plan->scheduled_date->format("Y-m-d") }}{{ $plan->scheduled_time ? "T".$plan->scheduled_time : "" }}',
                        classNames: [
                            'bg-{{ $plan->priority === "high" ? "danger" : ($plan->priority === "normal" ? "primary" : "success") }}',
                            '{{ $plan->status === "completed" ? "opacity-50" : "" }}'
                        ],
                        extendedProps: {
                            status: '{{ $plan->status }}',
                            priority: '{{ $plan->priority }}',
                            product: '{{ $plan->assembledItem->name ?? "Unknown" }}',
                            quantity: '{{ $plan->quantity }} {{ $plan->unit }}'
                        }
                    },
                    @endforeach
                ],
                eventClick: function(info) {
                    alert(`${info.event.title}\nProduct: ${info.event.extendedProps.product}\nQuantity: ${info.event.extendedProps.quantity}\nStatus: ${info.event.extendedProps.status}\nPriority: ${info.event.extendedProps.priority}`);
                }
            });
            calendar.render();
            
            // Handle month navigation
            document.getElementById('prevMonth').addEventListener('click', function() {
                calendar.prev();
            });
            
            document.getElementById('nextMonth').addEventListener('click', function() {
                calendar.next();
            });
            
            document.getElementById('todayBtn').addEventListener('click', function() {
                calendar.today();
            });
            
            // Handle view changes
            document.querySelectorAll('[data-view]').forEach(button => {
                button.addEventListener('click', function() {
                    const view = this.getAttribute('data-view');
                    calendar.changeView(view === 'month' ? 'dayGridMonth' : 
                                        view === 'week' ? 'timeGridWeek' : 
                                        view === 'day' ? 'timeGridDay' : 'listWeek');
                    
                    // Update active button
                    document.querySelectorAll('[data-view]').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });
            
            // Handle month picker change
            document.getElementById('monthPicker').addEventListener('change', function() {
                const date = this.value.split('-');
                calendar.gotoDate(date[0] + '-' + date[1] + '-01');
            });
        }
        
        // Handle product selection in the form
        document.getElementById('assembled_item_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.getAttribute('data-unit')) {
                document.getElementById('unit').value = selectedOption.getAttribute('data-unit');
                
                // Auto-generate title
                const productName = selectedOption.textContent;
                document.getElementById('title').value = 'Production of ' + productName;
            }
        });
    });
</script>
@endpush 