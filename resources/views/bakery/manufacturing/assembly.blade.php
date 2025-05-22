@extends('layouts.app')

@section('title', 'Inventory Assembly')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('bakery.dashboard') }}">Bakery</a></li>
<li class="breadcrumb-item"><a href="{{ route('bakery.manufacturing.assembly') }}">Manufacturing</a></li>
<li class="breadcrumb-item active">Inventory Assembly</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title m-0">Recipe Builder</h5>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAssembledItemModal">
                            <i class="bi bi-plus-circle me-1"></i> Add New Item
                        </button>
                    </div>
                </div>
                
                <!-- Assembled Items Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Group</th>
                                <th>Ingredients</th>
                                <th>Total Cost</th>
                                <th>Selling Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assembledItems ?? [] as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        @if($item->type === 'paste')
                                            <span class="badge bg-warning">Paste</span>
                                        @else
                                            <span class="badge bg-primary">Single</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->assemblyCategory->name ?? 'N/A' }}</td>
                                    <td>{{ $item->assemblyGroup->name ?? 'N/A' }}</td>
                                    <td>{{ $item->ingredients->count() }}</td>
                                    <td>{{ number_format($item->total_cost, 2) }}</td>
                                    <td>{{ number_format($item->selling_price, 2) }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary viewIngredientsBtn" 
                                                data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-type="{{ $item->type }}"
                                                data-bs-toggle="tooltip" title="View & Assemble">
                                                <i class="bi bi-eye"></i> View & Assemble
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info editItemBtn" 
                                                data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#editAssembledItemModal"
                                                data-item="{{ json_encode($item) }}" title="Edit Item">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger deleteItemBtn"
                                                data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                data-bs-toggle="tooltip" title="Delete Item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No assembled items found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="itemDetailsSection" style="display: none;">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title m-0"><span id="selectedItemName"></span> - Ingredients</h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary closeDetailsBtn">
                        <i class="bi bi-x"></i> Close
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm" id="ingredientsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Source Type</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Cost</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Total Ingredients Cost:</th>
                                        <th id="totalIngredientsCost">0.00</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Other Costs:</th>
                                        <th id="otherCosts">0.00</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Total Cost:</th>
                                        <th id="totalCost">0.00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Include ingredient form partial -->
                        @include('manufacturing.partials.ingredient_form')
                        
                        <!-- Paste Divisions List (Only displayed for paste items) -->
                        <div class="card mt-3" id="pasteDivisionsListCard" style="display: none;">
                            <div class="card-body">
                                <h5 class="card-title">Paste Divisions</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm" id="pasteDivisionsTable">
                                        <thead>
                                            <tr>
                                                <th>Output</th>
                                                <th>Quantity</th>
                                                <th>Flavor</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-warning mt-2 mb-0" id="wasteSummary" style="display: none;">
                                    <strong>Total Waste:</strong> <span id="totalWasteQuantity">0</span> <span id="wasteUnit"></span> 
                                    (<span id="wastePercentage">0</span>%)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Assembled Item Modal -->
<div class="modal fade" id="createAssembledItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createAssembledItemForm" action="{{ route('bakery.manufacturing.assembly.store') }}" method="POST">
                @csrf
                <pre>
  business_id: {{ session('business_id') }}
  company_id: {{ session('company_id') }}
  user: {{ json_encode(Auth::user()) }}
  </pre>
                <input type="hidden" name="business_id" value="{{ session('business_id') }}">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Assembled Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="name" name="name" required data-unique-check="assembled-item">
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">Item Type</label>
                            <select class="form-select searchable" id="type" name="type" required placeholder="Select Type">
                                <option value="single">Single Item</option>
                                <option value="paste">Paste (For Division)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="selling_price" class="form-label">Selling Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="selling_price" name="selling_price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="other_costs" class="form-label">Other Costs</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="other_costs" name="other_costs" step="0.01" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" value="item" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="assembly_category_id" class="form-label">Category</label>
                            <div class="input-group">
                                <select class="form-select searchable" id="assembly_category_id" name="assembly_category_id" placeholder="Search or select category">
                                <option value="">Select Category</option>
                                    @foreach($assemblyCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Category</option>
                            </select>
                                <button class="btn btn-outline-secondary" type="button" id="openNewCategoryBtn" onclick="openNewCategoryModal(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="assembly_group_id" class="form-label">Group</label>
                            <div class="input-group">
                                <select class="form-select searchable" id="assembly_group_id" name="assembly_group_id" placeholder="Search or select group">
                                <option value="">Select Group</option>
                                    @foreach($assemblyGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Group</option>
                            </select>
                                <button class="btn btn-outline-secondary" type="button" id="openNewGroupBtn" onclick="openNewGroupModal(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="assembly_size_id" class="form-label">Size</label>
                            <div class="input-group">
                                <select class="form-select searchable" id="assembly_size_id" name="assembly_size_id" placeholder="Search or select size">
                                    <option value="">Select Size</option>
                                    @foreach($assemblySizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Size</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" id="openNewSizeBtn" onclick="openNewSizeModal(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Assembled Item Modal -->
<div class="modal fade" id="editAssembledItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editAssembledItemForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Assembled Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required data-unique-check="assembled-item" data-id="">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_type" class="form-label">Item Type</label>
                            <select class="form-select searchable" id="edit_type" name="type" required placeholder="Select Type">
                                <option value="single">Single Item</option>
                                <option value="paste">Paste (For Division)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="edit_selling_price" class="form-label">Selling Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="edit_selling_price" name="selling_price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_other_costs" class="form-label">Other Costs</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="edit_other_costs" name="other_costs" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="edit_unit" name="unit" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="edit_assembly_category_id" class="form-label">Category</label>
                            <div class="input-group">
                                <select class="form-select searchable" id="edit_assembly_category_id" name="assembly_category_id" placeholder="Search or select category">
                                <option value="">Select Category</option>
                                    @foreach($assemblyCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Category</option>
                            </select>
                                <button class="btn btn-outline-secondary" type="button" id="openEditNewCategoryBtn" onclick="openNewCategoryModal(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_assembly_group_id" class="form-label">Group</label>
                            <div class="input-group">
                                <select class="form-select searchable" id="edit_assembly_group_id" name="assembly_group_id" placeholder="Search or select group">
                                <option value="">Select Group</option>
                                    @foreach($assemblyGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Group</option>
                            </select>
                                <button class="btn btn-outline-secondary" type="button" id="openEditNewGroupBtn" onclick="openNewGroupModal(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_assembly_size_id" class="form-label">Size</label>
                            <div class="input-group">
                                <select class="form-select searchable" id="edit_assembly_size_id" name="assembly_size_id" placeholder="Search or select size">
                                    <option value="">Select Size</option>
                                    @foreach($assemblySizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Size</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="button" id="openEditNewSizeBtn" onclick="openNewSizeModal(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteItemName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteItemForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Item</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Ingredient Confirmation Modal -->
<div class="modal fade" id="deleteIngredientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete Ingredient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove this ingredient?</p>
                <p class="text-danger">This will affect the total cost calculation.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteIngredientForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remove Ingredient</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createCategoryForm" action="{{ route('bakery.manufacturing.assembly.category.store') }}" method="POST">
                @csrf
                <input type="hidden" name="business_id" value="{{ session('business_id') }}">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Group Modal -->
<div class="modal fade" id="createGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createGroupForm" action="{{ route('bakery.manufacturing.assembly.group.store') }}" method="POST">
                @csrf
                <input type="hidden" name="business_id" value="{{ session('business_id') }}">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="group_name" class="form-label">Group Name</label>
                        <input type="text" class="form-control" id="group_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Size Modal -->
<div class="modal fade" id="createSizeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createSizeForm" action="{{ route('bakery.manufacturing.assembly.size.store') }}" method="POST">
                @csrf
                <input type="hidden" name="business_id" value="{{ session('business_id') }}">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Size</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="size_name" class="form-label">Size Name</label>
                        <input type="text" class="form-control" id="size_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Size</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notifications Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="mainToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mainToastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">Are you sure?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmModalYesBtn">Yes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Functions to open the modal windows
    function openNewCategoryModal() {
        const categoryModal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
        categoryModal.show();
    }
    
    function openNewGroupModal() {
        const groupModal = new bootstrap.Modal(document.getElementById('createGroupModal'));
        groupModal.show();
    }
    
    function openNewSizeModal() {
        const sizeModal = new bootstrap.Modal(document.getElementById('createSizeModal'));
        sizeModal.show();
    }
    
    // Toast notification function
    function showToast(type, message) {
        const toast = new bootstrap.Toast(document.getElementById('mainToast'));
        const toastEl = document.getElementById('mainToast');
        const msgEl = document.getElementById('mainToastMessage');
        msgEl.textContent = message;
        toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');
        if (type === 'error') {
            toastEl.classList.add('bg-danger');
        } else if (type === 'warning') {
            toastEl.classList.add('bg-warning');
        } else {
            toastEl.classList.add('bg-success');
        }
        toast.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        let selectedItemId = null;
        let selectedItemType = null;
        
        console.log('Assembly script loaded');
        
        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        if (tooltipTriggerList.length > 0) {
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }
        
        // Updated logic for category, group and size selection
        document.getElementById('assembly_category_id').addEventListener('change', function() {
            console.log('Category changed:', this.value);
            if (this.value === 'new') {
                // Show the category creation modal instead
                const categoryModal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
                categoryModal.show();
                this.value = ''; // Reset the select value
            }
        });
        
        document.getElementById('assembly_group_id').addEventListener('change', function() {
            console.log('Group changed:', this.value);
            if (this.value === 'new') {
                // Show the group creation modal instead
                const groupModal = new bootstrap.Modal(document.getElementById('createGroupModal'));
                groupModal.show();
                this.value = ''; // Reset the select value
            }
        });
        
        document.getElementById('assembly_size_id').addEventListener('change', function() {
            console.log('Size changed:', this.value);
            if (this.value === 'new') {
                // Show the size creation modal instead
                const sizeModal = new bootstrap.Modal(document.getElementById('createSizeModal'));
                sizeModal.show();
                this.value = ''; // Reset the select value
            }
        });
        
        // Handle creating category via modal
        document.getElementById('createCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            
            console.log('Submitting category form');
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the modal
                    bootstrap.Modal.getInstance(document.getElementById('createCategoryModal')).hide();
                    
                    // Add the new category to the dropdown
                    const select = document.getElementById('assembly_category_id');
                    const option = new Option(data.category.name, data.category.id);
                    // Insert before the "Add New Category" option
                    select.insertBefore(option, select.lastChild);
                    select.value = data.category.id;
                    
                    // Also add to the edit form dropdown
                    const editSelect = document.getElementById('edit_assembly_category_id');
                    const editOption = new Option(data.category.name, data.category.id);
                    editSelect.insertBefore(editOption, editSelect.lastChild);
                    
                    // Clear the form
                    document.getElementById('category_name').value = '';
                    
                    // Show success message
                    showToast('success', data.message);
            } else {
                    showToast('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Failed to create category. Please try again.');
            });
        });
        
        // Handle creating group via modal
        document.getElementById('createGroupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            
            console.log('Submitting group form');
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the modal
                    bootstrap.Modal.getInstance(document.getElementById('createGroupModal')).hide();
                    
                    // Add the new group to the dropdown
                    const select = document.getElementById('assembly_group_id');
                    const option = new Option(data.group.name, data.group.id);
                    // Insert before the "Add New Group" option
                    select.insertBefore(option, select.lastChild);
                    select.value = data.group.id;
                    
                    // Also add to the edit form dropdown
                    const editSelect = document.getElementById('edit_assembly_group_id');
                    const editOption = new Option(data.group.name, data.group.id);
                    editSelect.insertBefore(editOption, editSelect.lastChild);
                    
                    // Clear the form
                    document.getElementById('group_name').value = '';
                    
                    // Show success message
                    showToast('success', data.message);
            } else {
                    showToast('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Failed to create group. Please try again.');
            });
        });
        
        // Handle creating size via modal
        document.getElementById('createSizeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            
            console.log('Submitting size form');
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the modal
                    bootstrap.Modal.getInstance(document.getElementById('createSizeModal')).hide();
                    
                    // Add the new size to the dropdown
                    const select = document.getElementById('assembly_size_id');
                    const option = new Option(data.size.name, data.size.id);
                    // Insert before the "Add New Size" option
                    select.insertBefore(option, select.lastChild);
                    select.value = data.size.id;
                    
                    // Also add to the edit form dropdown
                    const editSelect = document.getElementById('edit_assembly_size_id');
                    const editOption = new Option(data.size.name, data.size.id);
                    editSelect.insertBefore(editOption, editSelect.lastChild);
                    
                    // Clear the form
                    document.getElementById('size_name').value = '';
                    
                    // Show success message
                    showToast('success', data.message);
            } else {
                    showToast('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Failed to create size. Please try again.');
            });
        });
        
        // Handle form submission for create assembled item
        document.getElementById('createAssembledItemForm').addEventListener('submit', function(event) {
            // Debug form submission
            console.log('Form submission started');
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
            
            const form = this;
            const formData = new FormData(form);
            
                event.preventDefault();
            
            // Submit form via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('createAssembledItemModal')).hide();
                    
                    // Show success message
                    showToast('success', 'Item created successfully!');
                    
                    // Reload page to show new data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast('error', data.message || 'Failed to create item.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Failed to create item. Please try again.');
            });
        });
        
        // Check for assembled_item_id in session to open ingredients view automatically
        @if(session('assembled_item_id'))
            // Automatically click the view ingredients button for the newly created item
            setTimeout(function() {
                const viewBtn = document.querySelector('.viewIngredientsBtn[data-id="{{ session('assembled_item_id') }}"]');
                if (viewBtn) {
                    viewBtn.click();
                }
            }, 500);
        @endif
        
        // View ingredients button
        document.querySelectorAll('.viewIngredientsBtn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.id;
                const itemName = this.dataset.name;
                const itemType = this.dataset.type;
                
                selectedItemId = itemId;
                selectedItemType = itemType;
                
                document.getElementById('selectedItemName').textContent = itemName;
                document.getElementById('assembledItemId').value = itemId;
                
                // Fetch ingredients for this assembled item
                fetch(`/bakery/manufacturing/assembly/${itemId}/ingredients`)
                    .then(response => response.json())
                    .then(ingredients => {
                        const tbody = document.querySelector('#ingredientsTable tbody');
                        tbody.innerHTML = '';
                        
                        let totalCost = 0;
                        
                        ingredients.forEach(ingredient => {
                            const row = document.createElement('tr');
                            
                            row.innerHTML = `
                                <td>${ingredient.source_name}</td>
                                <td>${ingredient.source_type}</td>
                                <td>${ingredient.quantity}</td>
                                <td>${ingredient.unit}</td>
                                <td>$${parseFloat(ingredient.cost).toFixed(2)}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger deleteIngredientBtn" 
                                        data-id="${ingredient.id}" data-bs-toggle="modal" data-bs-target="#deleteIngredientModal">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            `;
                            
                            tbody.appendChild(row);
                            totalCost += parseFloat(ingredient.cost);
                        });
                        
                        // Update totals
                        document.getElementById('totalIngredientsCost').textContent = totalCost.toFixed(2);
                        
                        // Get assembled item details to show other costs
                        fetch(`/bakery/manufacturing/assembly/${itemId}`)
                            .then(response => response.json())
                            .then(item => {
                                const otherCosts = parseFloat(item.other_costs || 0);
                                const totalItemCost = parseFloat(item.total_cost || 0);
                                
                                document.getElementById('otherCosts').textContent = otherCosts.toFixed(2);
                                document.getElementById('totalCost').textContent = totalItemCost.toFixed(2);
                                
                                // Show/hide paste division card based on item type
                                if (item.type === 'paste') {
                                    document.getElementById('pasteDivisionCard').style.display = 'block';
                                    document.getElementById('pasteName').textContent = item.name;
                                    document.getElementById('pasteTotalCost').textContent = totalItemCost.toFixed(2);
                                    document.getElementById('pasteDivisionForm').action = `/bakery/manufacturing/assembly/${itemId}/paste-divisions`;
                                    
                                    // Load paste divisions if it's a paste
                                    loadPasteDivisions(itemId);
                                } else {
                                    document.getElementById('pasteDivisionCard').style.display = 'none';
                                    document.getElementById('pasteDivisionsListCard').style.display = 'none';
                                }
                            });
                        
                        // Show the details section
                        document.getElementById('itemDetailsSection').style.display = 'block';
                        document.getElementById('itemDetailsSection').scrollIntoView({
                            behavior: 'smooth'
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Failed to load ingredients');
                    });
            });
        });
        
        // Close Details Button Click
        document.querySelector('.closeDetailsBtn').addEventListener('click', function() {
            document.getElementById('itemDetailsSection').style.display = 'none';
        });
        
        // Source type change handler
        document.getElementById('sourceType').addEventListener('change', function() {
            const sourceType = this.value;
            document.getElementById('ingredientSelectDiv').style.display = 'none';
            document.getElementById('productSelectDiv').style.display = 'none';
            document.getElementById('assembledItemSelectDiv').style.display = 'none';
            
            if (sourceType === 'ingredient') {
                document.getElementById('ingredientSelectDiv').style.display = 'block';
            } else if (sourceType === 'product') {
                document.getElementById('productSelectDiv').style.display = 'block';
            } else if (sourceType === 'assembled_item') {
                document.getElementById('assembledItemSelectDiv').style.display = 'block';
            }
        });
        
        // Selection of existing output item vs. creating new
        document.getElementById('createNewOutputItem').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('newOutputItemSection').style.display = 'block';
                document.getElementById('existingOutputItemSection').style.display = 'none';
                document.getElementById('output_assembled_item_id').value = '';
            } else {
                document.getElementById('newOutputItemSection').style.display = 'none';
                document.getElementById('existingOutputItemSection').style.display = 'block';
                document.getElementById('new_output_name').value = '';
            }
        });
        
        // Add ingredient form
        document.getElementById('addIngredientForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const sourceType = document.getElementById('sourceType').value;
            let sourceId = null;
            
            if (sourceType === 'ingredient') {
                sourceId = document.getElementById('ingredientId').value;
            } else if (sourceType === 'product') {
                sourceId = document.getElementById('productId').value;
            } else if (sourceType === 'assembled_item') {
                sourceId = document.getElementById('assembledItemIdRef').value;
            }
            
            if (!sourceId) {
                showToast('error', 'Please select a valid source');
                return;
            }
            
            const quantity = document.getElementById('quantity').value;
            const unit = document.getElementById('unit').value;
            
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('source_type', sourceType);
            formData.append('source_id', sourceId);
            formData.append('quantity', quantity);
            formData.append('unit', unit);
            
            fetch(`/bakery/manufacturing/assembly/${selectedItemId}/ingredients`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Reset form and reload ingredients
                this.reset();
                document.getElementById('sourceType').value = '';
                document.getElementById('ingredientSelectDiv').style.display = 'none';
                document.getElementById('productSelectDiv').style.display = 'none';
                document.getElementById('assembledItemSelectDiv').style.display = 'none';
                
                // Refresh the ingredients table
                document.querySelector('.viewIngredientsBtn[data-id="' + selectedItemId + '"]').click();
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Failed to add ingredient');
            });
        });

        // Paste Division Form
        document.getElementById('pasteDivisionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Close modal and reload paste divisions
                const modal = bootstrap.Modal.getInstance(document.getElementById('pasteDivisionModal'));
                modal.hide();
                loadPasteDivisions(selectedItemId);
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Failed to create paste division');
            });
        });
        
        // Edit button
        document.querySelectorAll('.editItemBtn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.id;
                const itemData = JSON.parse(this.dataset.item);
                
                document.getElementById('editAssembledItemForm').action = `/bakery/manufacturing/assembly/${itemId}`;
                document.getElementById('edit_name').value = itemData.name;
                document.getElementById('edit_type').value = itemData.type;
                document.getElementById('edit_description').value = itemData.description || '';
                document.getElementById('edit_selling_price').value = itemData.selling_price;
                document.getElementById('edit_other_costs').value = itemData.other_costs;
                document.getElementById('edit_unit').value = itemData.unit;
                
                // Set category, group, and size values
                if (itemData.assembly_category_id) {
                    document.getElementById('edit_assembly_category_id').value = itemData.assembly_category_id;
                } else {
                    document.getElementById('edit_assembly_category_id').value = '';
                }
                
                if (itemData.assembly_group_id) {
                    document.getElementById('edit_assembly_group_id').value = itemData.assembly_group_id;
                } else {
                    document.getElementById('edit_assembly_group_id').value = '';
                }
                
                if (itemData.assembly_size_id) {
                    document.getElementById('edit_assembly_size_id').value = itemData.assembly_size_id;
                } else {
                    document.getElementById('edit_assembly_size_id').value = '';
                }
            });
        });
        
        // Delete button
        document.querySelectorAll('.deleteItemBtn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.id;
                const itemName = this.dataset.name;
                
                document.getElementById('deleteItemName').textContent = itemName;
                document.getElementById('deleteItemForm').action = `/bakery/manufacturing/assembly/${itemId}`;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteItemModal'));
                deleteModal.show();
            });
        });
        
        // Source selection handlers to auto-populate unit
        document.getElementById('ingredientId').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected) {
                document.getElementById('unit').value = selected.dataset.unit || '';
            }
        });
        
        document.getElementById('productId').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected) {
                document.getElementById('unit').value = selected.dataset.unit || '';
            }
        });
        
        document.getElementById('assembledItemIdRef').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected) {
                document.getElementById('unit').value = selected.dataset.unit || '';
            }
        });
        
        // Event delegation for delete buttons (since they're added dynamically)
        document.addEventListener('click', function(e) {
            // Delete ingredient button
            if (e.target.closest('.deleteIngredientBtn')) {
                const button = e.target.closest('.deleteIngredientBtn');
                const ingredientId = button.dataset.id;
                
                showConfirmModal('Are you sure you want to remove this ingredient?', function() {
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    formData.append('_method', 'DELETE');
                    
                    fetch(`/bakery/manufacturing/assembly/ingredients/${ingredientId}`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Refresh ingredients
                        document.querySelector('.viewIngredientsBtn[data-id="' + selectedItemId + '"]').click();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Failed to remove ingredient');
                    });
                });
            }
            
            // Delete paste division button
            if (e.target.closest('.deleteDivisionBtn')) {
                const button = e.target.closest('.deleteDivisionBtn');
                const divisionId = button.dataset.id;
                
                showConfirmModal('Are you sure you want to remove this paste division?', function() {
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    formData.append('_method', 'DELETE');
                    
                    fetch(`/bakery/manufacturing/assembly/paste-divisions/${divisionId}`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Refresh paste divisions
                        loadPasteDivisions(selectedItemId);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Failed to remove paste division');
                    });
                });
            }
        });

        if (window.Choices) {
            document.querySelectorAll('.searchable').forEach(function(select) {
                new Choices(select, { searchEnabled: true, placeholder: true, placeholderValue: select.getAttribute('placeholder') || 'Type to search...' });
            });
        }

        // Real-time uniqueness check for assembled item name
        function checkAssembledItemNameUnique(input, submitBtn) {
            const name = input.value.trim();
            const id = input.dataset.id || '';
            if (!name) return;
            fetch(`/api/check-assembled-item-name-unique?name=${encodeURIComponent(name)}&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    let feedback = input.parentElement.querySelector('.name-unique-feedback');
                    if (!feedback) {
                        feedback = document.createElement('div');
                        feedback.className = 'name-unique-feedback text-danger small mt-1';
                        input.parentElement.appendChild(feedback);
                    }
                    if (!data.unique) {
                        feedback.textContent = 'This name is already taken.';
                        submitBtn.disabled = true;
                    } else {
                        feedback.textContent = '';
                        submitBtn.disabled = false;
                    }
                });
        }
        // Create modal
        const createNameInput = document.getElementById('name');
        const createSubmitBtn = document.querySelector('#createAssembledItemForm button[type="submit"]');
        if (createNameInput && createSubmitBtn) {
            createNameInput.addEventListener('input', function() {
                checkAssembledItemNameUnique(createNameInput, createSubmitBtn);
            });
        }
        // Edit modal
        const editNameInput = document.getElementById('edit_name');
        const editSubmitBtn = document.querySelector('#editAssembledItemForm button[type="submit"]');
        if (editNameInput && editSubmitBtn) {
            editNameInput.addEventListener('input', function() {
                checkAssembledItemNameUnique(editNameInput, editSubmitBtn);
            });
        }
    });

    // Toggle input functions
    function toggleNewCategoryInput() {
        const newCategoryInput = document.getElementById('new_category');
        const categorySelect = document.getElementById('assembly_category_id');
        
        if (newCategoryInput.style.display === 'none') {
            newCategoryInput.style.display = 'block';
            categorySelect.value = 'new';
            newCategoryInput.focus();
        } else {
            newCategoryInput.style.display = 'none';
            categorySelect.value = '';
        }
    }
    
    function toggleNewGroupInput() {
        const newGroupInput = document.getElementById('new_group');
        const groupSelect = document.getElementById('assembly_group_id');
        
        if (newGroupInput.style.display === 'none') {
            newGroupInput.style.display = 'block';
            groupSelect.value = 'new';
            newGroupInput.focus();
        } else {
            newGroupInput.style.display = 'none';
            groupSelect.value = '';
        }
    }
    
    function toggleNewSizeInput() {
        const newSizeInput = document.getElementById('new_size');
        const sizeSelect = document.getElementById('assembly_size_id');
        
        if (newSizeInput.style.display === 'none') {
            newSizeInput.style.display = 'block';
            sizeSelect.value = 'new';
            newSizeInput.focus();
        } else {
            newSizeInput.style.display = 'none';
            sizeSelect.value = '';
        }
    }
    
    function toggleEditNewCategoryInput() {
        const newCategoryInput = document.getElementById('edit_new_category');
        const categorySelect = document.getElementById('edit_assembly_category_id');
        
        if (newCategoryInput.style.display === 'none') {
            newCategoryInput.style.display = 'block';
            categorySelect.value = 'new';
            newCategoryInput.focus();
        } else {
            newCategoryInput.style.display = 'none';
            categorySelect.value = '';
        }
    }
    
    function toggleEditNewGroupInput() {
        const newGroupInput = document.getElementById('edit_new_group');
        const groupSelect = document.getElementById('edit_assembly_group_id');
        
        if (newGroupInput.style.display === 'none') {
            newGroupInput.style.display = 'block';
            groupSelect.value = 'new';
            newGroupInput.focus();
        } else {
            newGroupInput.style.display = 'none';
            groupSelect.value = '';
        }
    }
    
    function toggleEditNewSizeInput() {
        const newSizeInput = document.getElementById('edit_new_size');
        const sizeSelect = document.getElementById('edit_assembly_size_id');
        
        if (newSizeInput.style.display === 'none') {
            newSizeInput.style.display = 'block';
            sizeSelect.value = 'new';
            newSizeInput.focus();
        } else {
            newSizeInput.style.display = 'none';
            sizeSelect.value = '';
        }
    }

    // Confirmation modal logic
    function showConfirmModal(message, yesCallback) {
        document.getElementById('confirmModalBody').textContent = message;
        const yesBtn = document.getElementById('confirmModalYesBtn');
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        yesBtn.onclick = function() {
            modal.hide();
            yesCallback();
        };
        modal.show();
    }
</script>
@endpush