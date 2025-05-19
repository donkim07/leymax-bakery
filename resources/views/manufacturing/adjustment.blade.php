@extends('layouts.app')

@section('title', 'Manufacturing Stock Adjustment')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('bakery.dashboard') }}">Bakery</a></li>
<li class="breadcrumb-item"><a href="{{ route('bakery.manufacturing.process') }}">Manufacturing</a></li>
<li class="breadcrumb-item active">Stock Adjustment</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Manufacturing Stock Adjustment</h5>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAdjustmentModal">
                            <i class="bi bi-plus-circle me-1"></i> New Adjustment
                        </button>
                    </div>
                </div>
                <p class="card-text">Adjust inventory quantities after manufacturing processes to account for produced items and used materials.</p>
                
                <ul class="nav nav-tabs nav-tabs-bordered" id="adjustmentTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent-adjustments" type="button" role="tab" aria-controls="recent-adjustments" aria-selected="true">Recent Adjustments</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-adjustments" type="button" role="tab" aria-controls="pending-adjustments" aria-selected="false">Pending</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="output-tab" data-bs-toggle="tab" data-bs-target="#output-adjustments" type="button" role="tab" aria-controls="output-adjustments" aria-selected="false">Output Adjustments</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="input-tab" data-bs-toggle="tab" data-bs-target="#input-adjustments" type="button" role="tab" aria-controls="input-adjustments" aria-selected="false">Input Adjustments</button>
                    </li>
                </ul>
                <div class="tab-content pt-2" id="adjustmentTabContent">
                    <div class="tab-pane fade show active" id="recent-adjustments" role="tabpanel" aria-labelledby="recent-tab">
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">Adjustment ID</th>
                                        <th scope="col">Process Ref</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Items</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>ADJ-20240045</td>
                                        <td>BP-20240001</td>
                                        <td>Output</td>
                                        <td>Today 10:30 AM</td>
                                        <td>3 items</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Details</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>ADJ-20240044</td>
                                        <td>BP-20240001</td>
                                        <td>Input</td>
                                        <td>Today 08:35 AM</td>
                                        <td>5 items</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Details</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pending-adjustments" role="tabpanel" aria-labelledby="pending-tab">
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">Adjustment ID</th>
                                        <th scope="col">Process Ref</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Created</th>
                                        <th scope="col">Items</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>ADJ-20240046</td>
                                        <td>BP-20240002</td>
                                        <td>Output</td>
                                        <td>15 minutes ago</td>
                                        <td>2 items</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Details</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-check-circle"></i> Complete</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil"></i> Edit</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="output-adjustments" role="tabpanel" aria-labelledby="output-tab">
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">Adjustment ID</th>
                                        <th scope="col">Process Ref</th>
                                        <th scope="col">Product</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>ADJ-20240045</td>
                                        <td>BP-20240001</td>
                                        <td>Vanilla Cake Base</td>
                                        <td>+50 units</td>
                                        <td>Today 10:30 AM</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Details</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>ADJ-20240046</td>
                                        <td>BP-20240002</td>
                                        <td>Chocolate Frosting</td>
                                        <td>+25 kg</td>
                                        <td>15 minutes ago</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Details</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-check-circle"></i> Complete</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil"></i> Edit</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="input-adjustments" role="tabpanel" aria-labelledby="input-tab">
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">Adjustment ID</th>
                                        <th scope="col">Process Ref</th>
                                        <th scope="col">Items Used</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Created By</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>ADJ-20240044</td>
                                        <td>BP-20240001</td>
                                        <td>
                                            <ul class="mb-0 ps-3 small">
                                                <li>Flour: -25kg</li>
                                                <li>Sugar: -10kg</li>
                                                <li>Eggs: -60 units</li>
                                                <li>Vanilla Extract: -1L</li>
                                                <li>Baking Powder: -500g</li>
                                            </ul>
                                        </td>
                                        <td>Today 08:35 AM</td>
                                        <td>John Baker</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Details</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Adjustment Modal -->
<div class="modal fade" id="newAdjustmentModal" tabindex="-1" aria-labelledby="newAdjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newAdjustmentModalLabel">New Manufacturing Stock Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3">
                    <div class="col-md-6">
                        <label for="adjustmentType" class="form-label">Adjustment Type</label>
                        <select id="adjustmentType" class="form-select">
                            <option value="input">Input (Raw Materials Used)</option>
                            <option value="output">Output (Products Produced)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="processRef" class="form-label">Manufacturing Process</label>
                        <select id="processRef" class="form-select">
                            <option value="">Select Manufacturing Process</option>
                            <option value="BP-20240001">BP-20240001 - Vanilla Cake Base</option>
                            <option value="BP-20240002">BP-20240002 - Chocolate Frosting</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <hr>
                        <h6>Items</h6>
                        <div id="itemsContainer">
                            <div class="row mb-3 item-row">
                                <div class="col-md-5">
                                    <label class="form-label">Item</label>
                                    <select class="form-select item-select">
                                        <option value="">Select Item</option>
                                        <option>Vanilla Cake Base</option>
                                        <option>Flour</option>
                                        <option>Sugar</option>
                                        <option>Eggs</option>
                                        <option>Baking Powder</option>
                                        <option>Vanilla Extract</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control item-quantity">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit</label>
                                    <select class="form-select item-unit">
                                        <option>units</option>
                                        <option>kg</option>
                                        <option>g</option>
                                        <option>L</option>
                                        <option>ml</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger remove-item"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addItemBtn">
                                <i class="bi bi-plus-circle me-1"></i> Add Item
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="adjustmentNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="adjustmentNotes" rows="3" placeholder="Enter any additional notes or observations..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Adjustment</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize datatables
        const datatables = document.querySelectorAll('.datatable');
        if (datatables.length > 0) {
            datatables.forEach(function(datatable) {
                new simpleDatatables.DataTable(datatable);
            });
        }
        
        // Add item button functionality
        const addItemBtn = document.getElementById('addItemBtn');
        const itemsContainer = document.getElementById('itemsContainer');
        
        if (addItemBtn && itemsContainer) {
            addItemBtn.addEventListener('click', function() {
                const itemRow = document.querySelector('.item-row');
                const newRow = itemRow.cloneNode(true);
                
                // Clear input values
                newRow.querySelector('.item-select').value = '';
                newRow.querySelector('.item-quantity').value = '';
                
                // Add event listener to remove button
                newRow.querySelector('.remove-item').addEventListener('click', function() {
                    if (document.querySelectorAll('.item-row').length > 1) {
                        this.closest('.item-row').remove();
                    }
                });
                
                itemsContainer.appendChild(newRow);
            });
            
            // Add event listener to existing remove button
            document.querySelector('.remove-item').addEventListener('click', function() {
                if (document.querySelectorAll('.item-row').length > 1) {
                    this.closest('.item-row').remove();
                }
            });
        }
        
        // Toggle form fields based on adjustment type
        const adjustmentType = document.getElementById('adjustmentType');
        if (adjustmentType) {
            adjustmentType.addEventListener('change', function() {
                const itemSelects = document.querySelectorAll('.item-select');
                if (this.value === 'input') {
                    // Show raw materials
                    itemSelects.forEach(select => {
                        // Reset and populate with raw materials
                        select.innerHTML = `
                            <option value="">Select Raw Material</option>
                            <option>Flour</option>
                            <option>Sugar</option>
                            <option>Eggs</option>
                            <option>Baking Powder</option>
                            <option>Vanilla Extract</option>
                            <option>Milk</option>
                            <option>Butter</option>
                        `;
                    });
                } else {
                    // Show finished products
                    itemSelects.forEach(select => {
                        // Reset and populate with finished products
                        select.innerHTML = `
                            <option value="">Select Finished Product</option>
                            <option>Vanilla Cake Base</option>
                            <option>Chocolate Cake Base</option>
                            <option>Strawberry Frosting</option>
                            <option>Whole Wheat Bread</option>
                            <option>Blueberry Muffins</option>
                        `;
                    });
                }
            });
        }
    });
</script>
@endpush 