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
<div class="modal fade" id="scheduleProductionModal" tabindex="-1" aria-labelledby="scheduleProductionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleProductionModalLabel">Schedule Production</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3">
                    <div class="col-md-6">
                        <label for="productSelect" class="form-label">Product</label>
                        <select id="productSelect" class="form-select">
                            <option selected disabled>Choose...</option>
                            <option>Vanilla Cake Base</option>
                            <option>Chocolate Cake Base</option>
                            <option>Strawberry Frosting</option>
                            <option>Whole Wheat Bread</option>
                            <option>Blueberry Muffins</option>
                            <option>Croissants</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="batchSize" class="form-label">Batch Size</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="batchSize" value="50">
                            <span class="input-group-text">units</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="startTime" class="form-label">Start Time</label>
                        <input type="time" class="form-control" id="startTime" value="08:00">
                    </div>
                    <div class="col-md-6">
                        <label for="estimatedDuration" class="form-label">Estimated Duration</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="estimatedDuration" value="120">
                            <span class="input-group-text">minutes</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="assignedTo" class="form-label">Assigned To</label>
                        <select id="assignedTo" class="form-select">
                            <option selected disabled>Choose...</option>
                            <option>John Baker</option>
                            <option>Maria Pastry</option>
                            <option>Alex Confection</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="priority" class="form-label">Priority</label>
                        <select id="priority" class="form-select">
                            <option>Low</option>
                            <option selected>Normal</option>
                            <option>High</option>
                            <option>Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="resourceAllocation" class="form-label">Resource Allocation</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="mixingStation1">
                            <label class="form-check-label" for="mixingStation1">
                                Mixing Station 1
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="mixingStation2">
                            <label class="form-check-label" for="mixingStation2">
                                Mixing Station 2
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="oven1">
                            <label class="form-check-label" for="oven1">
                                Oven 1
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="oven2">
                            <label class="form-check-label" for="oven2">
                                Oven 2
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="decorationStation">
                            <label class="form-check-label" for="decorationStation">
                                Decoration Station
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" rows="3" placeholder="Add any special instructions or notes here..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success">Check Material Requirements</button>
                <button type="button" class="btn btn-primary">Schedule</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calendar functionality would be implemented here with a library like FullCalendar
        console.log('Production calendar initialized');
        
        // Sample calendar data - in a real implementation this would come from an API
        const calendarEvents = [
            {
                title: 'Vanilla Cake Base (50 units)',
                start: '2024-06-17T08:30:00',
                end: '2024-06-17T11:30:00',
                color: '#4154f1'
            },
            {
                title: 'Chocolate Frosting (25 kg)',
                start: '2024-06-17T13:00:00',
                end: '2024-06-17T15:00:00',
                color: '#2eca6a'
            },
            {
                title: 'Whole Wheat Bread (40 loaves)',
                start: '2024-06-18T06:00:00',
                end: '2024-06-18T09:00:00',
                color: '#ff771d'
            }
        ];
        
        // Calendar navigation handlers
        document.getElementById('prevMonth').addEventListener('click', function() {
            console.log('Previous month clicked');
        });
        
        document.getElementById('nextMonth').addEventListener('click', function() {
            console.log('Next month clicked');
        });
        
        document.getElementById('todayBtn').addEventListener('click', function() {
            console.log('Today button clicked');
        });
        
        // View buttons
        const viewButtons = document.querySelectorAll('[data-view]');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                viewButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                console.log('View changed to:', this.dataset.view);
            });
        });
        
        // Product selector in modal
        const productSelect = document.getElementById('productSelect');
        if (productSelect) {
            productSelect.addEventListener('change', function() {
                // In a real implementation, this would populate duration and resource requirements
                // based on the selected product's recipe
                console.log('Product selected:', this.value);
            });
        }
    });
</script>
@endpush 