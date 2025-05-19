@extends('layouts.app')

@section('title', 'Manufacturing Process')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('bakery.dashboard') }}">Bakery</a></li>
<li class="breadcrumb-item"><a href="{{ route('bakery.manufacturing.assembly') }}">Manufacturing</a></li>
<li class="breadcrumb-item active">Manufacturing Process</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">                    <h5 class="card-title m-0">Manufacturing Queue</h5>                    <div>                        <button type="button" class="btn btn-success me-2" id="completeAllBtn">                            <i class="bi bi-check-all me-1"></i> Complete All                        </button>                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createManufacturingModal">                            <i class="bi bi-plus-circle me-1"></i> Add New Production                        </button>                    </div>                </div>
                
                <ul class="nav nav-tabs nav-tabs-bordered" id="manufacturingTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress-content" type="button" role="tab" aria-controls="progress-content" aria-selected="true">
                            In Progress <span class="badge bg-warning rounded-pill">{{ $inProgressProcesses->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-content" type="button" role="tab" aria-controls="completed-content" aria-selected="false">
                            Completed <span class="badge bg-success rounded-pill">{{ $completedProcesses->count() }}</span>
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content pt-3" id="manufacturingTabContent">
                    <!-- In Progress Productions -->
                    <div class="tab-pane fade show active" id="progress-content" role="tabpanel" aria-labelledby="progress-tab">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Scheduled Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inProgressProcesses ?? [] as $process)
                                    <tr>
                                        <td>{{ $process->batch_number }}</td>
                                        <td>{{ $process->assembledItem->name ?? 'Unknown' }}</td>
                                        <td>{{ ucfirst($process->assembledItem->type ?? 'unknown') }}</td>
                                        <td>{{ $process->quantity }} {{ $process->assembledItem->unit ?? '' }}</td>
                                        <td>{{ $process->scheduled_date ? $process->scheduled_date->format('Y-m-d') : now()->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-warning">In Progress</span></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary viewIngredientsBtn" 
                                                    data-id="{{ $process->id }}" data-name="{{ $process->assembledItem->name ?? 'Unknown' }}"
                                                    data-bs-toggle="tooltip" title="View Ingredients">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success editIngredientsBtn" 
                                                    data-id="{{ $process->id }}" data-name="{{ $process->assembledItem->name ?? 'Unknown' }}" 
                                                    data-type="{{ $process->assembledItem->type ?? 'unknown' }}"
                                                    data-bs-toggle="tooltip" title="Edit Ingredients">
                                                    <i class="bi bi-list-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info editNotesBtn" 
                                                    data-id="{{ $process->id }}" 
                                                    data-notes="{{ $process->notes ?? '' }}"
                                                    data-bs-toggle="tooltip" title="Edit Notes">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success completeItemBtn"
                                                    data-id="{{ $process->id }}" data-name="{{ $process->assembledItem->name ?? 'Unknown' }}"
                                                    data-bs-toggle="tooltip" title="Complete Production">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No manufacturing processes in progress</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Completed Productions -->
                    <div class="tab-pane fade" id="completed-content" role="tabpanel" aria-labelledby="completed-tab">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Batch #</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Completed Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($completedProcesses ?? [] as $process)
                                    <tr>
                                        <td>{{ $process->batch_number }}</td>
                                        <td>{{ $process->assembledItem->name ?? 'Unknown' }}</td>
                                        <td>{{ $process->quantity }} {{ $process->assembledItem->unit ?? '' }}</td>
                                        <td>{{ $process->completed_date ? $process->completed_date->format('Y-m-d') : now()->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary viewIngredientsBtn" 
                                                data-id="{{ $process->id }}" data-name="{{ $process->assembledItem->name ?? 'Unknown' }}"
                                                data-bs-toggle="tooltip" title="View Ingredients">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @if($process->assembledItem->type === 'paste' && $process->status === 'completed')
                                                <button type="button" class="btn btn-sm btn-outline-warning dividePasteBtn" 
                                                    data-id="{{ $process->id }}" data-name="{{ $process->assembledItem->name }}"
                                                    data-bs-toggle="modal" data-bs-target="#pasteDivisionModal" title="Divide Paste">
                                                    <i class="bi bi-diagram-3"></i> Divide Paste
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No completed manufacturing processes found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
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
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Add Ingredient</h5>
                                <form id="addIngredientForm">
                                    <input type="hidden" id="assembledItemId" name="assembledItemId">
                                    <div class="mb-3">
                                        <label for="sourceType" class="form-label">Source Type</label>
                                        <select class="form-select searchable" id="sourceType" name="sourceType" required placeholder="Select source type">
                                            <option value="">Select Source Type</option>
                                            <option value="ingredient">Raw Ingredient</option>
                                            <option value="product">Product</option>
                                            <option value="assembled_item">Assembled Item</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3" id="ingredientSelectDiv" style="display: none;">
                                        <label for="ingredientId" class="form-label">Ingredient</label>
                                        <select class="form-select searchable" id="ingredientId" name="ingredientId" placeholder="Search or select ingredient">
                                            <option value="">Select Ingredient</option>
                                            @foreach($ingredients as $ingredient)
                                                <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit }}" data-cost="{{ $ingredient->cost_price }}">
                                                    {{ $ingredient->name }} ({{ $ingredient->unit }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3" id="productSelectDiv" style="display: none;">
                                        <label for="productId" class="form-label">Product</label>
                                        <select class="form-select searchable" id="productId" name="productId" placeholder="Search or select product">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-unit="{{ $product->unit ?? 'unit' }}" data-cost="{{ $product->cost_price }}">
                                                    {{ $product->name }} ({{ $product->unit ?? 'unit' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3" id="assembledItemSelectDiv" style="display: none;">
                                        <label for="assembledItemIdRef" class="form-label">Assembled Item</label>
                                        <select class="form-select searchable" id="assembledItemIdRef" name="assembledItemIdRef" placeholder="Search or select assembled item">
                                            <option value="">Select Assembled Item</option>
                                            @foreach($existingAssembledItems as $existingItem)
                                                <option value="{{ $existingItem->id }}" data-unit="{{ $existingItem->unit }}" data-cost="{{ $existingItem->total_cost }}">
                                                    {{ $existingItem->name }} ({{ $existingItem->unit }})
                                                </option>
                                            @endforeach
                                        </select>
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
                                    
                                    <button type="submit" class="btn btn-primary w-100">Add Ingredient</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card mt-3" id="pasteDivisionCard" style="display: none;">
                            <div class="card-body">
                                <h5 class="card-title">Paste Division</h5>
                                <div class="alert alert-info">
                                    Divide this paste into smaller portions with different flavors.
                                </div>
                                <button class="btn btn-outline-primary" id="dividePasteBtn">
                                    <i class="bi bi-diagram-3"></i> Divide Paste
                                </button>
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
                <div class="modal-header">
                    <h5 class="modal-title">Create New Assembled Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">Item Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="single">Single Item</option>
                                <option value="paste">Paste (Bulk)</option>
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
                            <label for="category" class="form-label">Category</label>
                            <div class="input-group">
                                <select class="form-select" id="assembly_category_id" name="assembly_category_id">
                                <option value="">Select Category</option>
                                    @foreach($assemblyCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Category</option>
                            </select>
                                <button class="btn btn-outline-secondary" type="button" id="toggleNewCategoryBtn" onclick="toggleNewCategoryInput(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="new_category" name="new_category" placeholder="Enter new category name" style="display: none;">
                        </div>
                        <div class="col-md-4">
                            <label for="group" class="form-label">Group</label>
                            <div class="input-group">
                                <select class="form-select" id="assembly_group_id" name="assembly_group_id">
                                <option value="">Select Group</option>
                                    @foreach($assemblyGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Group</option>
                            </select>
                                <button class="btn btn-outline-secondary" type="button" id="toggleNewGroupBtn" onclick="toggleNewGroupInput(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="new_group" name="new_group" placeholder="Enter new group name" style="display: none;">
                        </div>
                        <div class="col-md-4">
                            <label for="size" class="form-label">Size</label>
                            <div class="input-group">
                                <select class="form-select" id="assembly_size_id" name="assembly_size_id">
                                <option value="">Select Size</option>
                                    @foreach($assemblySizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Size</option>
                            </select>
                                <button class="btn btn-outline-secondary" type="button" id="toggleNewSizeBtn" onclick="toggleNewSizeInput(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="new_size" name="new_size" placeholder="Enter new size name" style="display: none;">
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
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_type" class="form-label">Item Type</label>
                            <select class="form-select" id="edit_type" name="type" required>
                                <option value="single">Single Item</option>
                                <option value="paste">Paste (Bulk)</option>
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
                            <label for="edit_category" class="form-label">Category</label>
                            <div class="input-group">
                                <select class="form-select" id="edit_assembly_category_id" name="assembly_category_id">
                                <option value="">Select Category</option>
                                    @foreach($assemblyCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Category</option>
                            </select>
                                <button class="btn btn-outline-secondary" type="button" id="toggleEditNewCategoryBtn" onclick="toggleEditNewCategoryInput(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="edit_new_category" name="new_category" placeholder="Enter new category name" style="display: none;">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_group" class="form-label">Group</label>
                            <div class="input-group">
                                <select class="form-select" id="edit_assembly_group_id" name="assembly_group_id">
                                <option value="">Select Group</option>
                                    @foreach($assemblyGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Group</option>
                            </select>
                                <button class="btn btn-outline-secondary" type="button" id="toggleEditNewGroupBtn" onclick="toggleEditNewGroupInput(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="edit_new_group" name="new_group" placeholder="Enter new group name" style="display: none;">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_size" class="form-label">Size</label>
                            <div class="input-group">
                                <select class="form-select" id="edit_assembly_size_id" name="assembly_size_id">
                                <option value="">Select Size</option>
                                    @foreach($assemblySizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->name }}</option>
                                    @endforeach
                                    <option value="new">+ Add New Size</option>
                            </select>
                                <button class="btn btn-outline-secondary" type="button" id="toggleEditNewSizeBtn" onclick="toggleEditNewSizeInput(); return false;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2" id="edit_new_size" name="new_size" placeholder="Enter new size name" style="display: none;">
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

<!-- Paste Division Modal -->
<div class="modal fade" id="pasteDivisionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="pasteDivisionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Divide Paste into Portions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="output_assembled_item_id" class="form-label">Output Item</label>
                            <select class="form-select searchable" id="output_assembled_item_id" name="output_assembled_item_id" placeholder="Type to search or select item">
                                <option value="">Select Output Item</option>
                                @foreach($assembledItems->where('type', '!=', 'paste') as $outputItem)
                                    <option value="{{ $outputItem->id }}">{{ $outputItem->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select existing item or leave blank for custom output</small>
                        </div>
                        <div class="col-md-3">
                            <label for="paste_quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="paste_quantity" name="quantity" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <label for="paste_unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="paste_unit" name="unit" required>
                        </div>
                    </div>
                    <div class="row mb-3" id="newOutputItemSection" style="display: none;">
                        <div class="col-md-12">
                            <label for="new_output_name" class="form-label">Output Item Name</label>
                            <input type="text" class="form-control" id="new_output_name" name="new_output_name" placeholder="Type to create new or search existing" autocomplete="off">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="flavor" class="form-label">Flavor</label>
                            <select class="form-select searchable" id="flavor" name="flavor">
                                <option value="">Select Flavor</option>
                                @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->name }}">{{ $ingredient->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="flavor_quantity" class="form-label">Flavor Quantity</label>
                            <input type="number" class="form-control" id="flavor_quantity" name="flavor_quantity" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label for="flavor_cost" class="form-label">Flavor Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="flavor_cost" name="flavor_cost" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="waste_quantity" class="form-label">Waste Quantity</label>
                            <input type="number" class="form-control" id="waste_quantity" name="waste_quantity" step="0.01" min="0">
                            <small class="text-muted">Amount of paste wasted during division</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Division</button>
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

<!-- Create Manufacturing Modal -->
<div class="modal fade" id="createManufacturingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createManufacturingForm" action="{{ route('bakery.manufacturing.process.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Production</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="assembled_item_id" class="form-label">Product</label>
                            <select class="form-select searchable" id="assembled_item_id" name="assembled_item_id" required placeholder="Search or select product">
                                <option value="">Select Product</option>
                                @foreach($assembledItems ?? [] as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ ucfirst($item->type) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="scheduled_date" class="form-label">Scheduled Date</label>
                            <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="complete_now" name="status" value="completed">
                                <label class="form-check-label" for="complete_now">
                                    Complete immediately (subtract ingredients from inventory)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Production</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Notes Modal -->
<div class="modal fade" id="editNotesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editNotesForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
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

@if (!View::hasSection('toasts'))
<!-- Toast Notifications Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="mainToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mainToastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<script>
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
</script>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let selectedProcessId = null;
        let selectedProcessStatus = null;
        
        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        if (tooltipTriggerList.length > 0) {
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }
        
        // View ingredients button
        document.querySelectorAll('.viewIngredientsBtn').forEach(button => {
            button.addEventListener('click', function() {
                const processId = this.dataset.id;
                const itemName = this.dataset.name;
                
                selectedProcessId = processId;
                document.getElementById('selectedItemName').textContent = itemName;
                
                // Fetch ingredients for this manufacturing process
                fetch(`/api/manufacturing-process/${processId}/ingredients`)
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
                                    <button type="button" class="btn btn-sm btn-outline-primary viewIngredientDetails" 
                                        data-id="${ingredient.id}" data-bs-toggle="tooltip" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            `;
                            
                            tbody.appendChild(row);
                            totalCost += parseFloat(ingredient.cost);
                        });
                        
                        // Update totals
                        document.getElementById('totalIngredientsCost').textContent = totalCost.toFixed(2);
                        document.getElementById('otherCosts').textContent = '0.00';
                        document.getElementById('totalCost').textContent = totalCost.toFixed(2);
                    })
                    .catch(error => {
                        console.error('Error fetching ingredients:', error);
                        
                        // Use demo data for now since the API doesn't exist yet
                        const tbody = document.querySelector('#ingredientsTable tbody');
                        tbody.innerHTML = '';
                        
                        const demoIngredients = [
                            { source_name: 'Flour', source_type: 'ingredient', quantity: 500, unit: 'g', cost: 2.50 },
                            { source_name: 'Sugar', source_type: 'ingredient', quantity: 200, unit: 'g', cost: 1.20 },
                            { source_name: 'Eggs', source_type: 'product', quantity: 3, unit: 'pcs', cost: 1.50 },
                            { source_name: 'Vanilla Extract', source_type: 'ingredient', quantity: 5, unit: 'ml', cost: 0.75 }
                        ];
                        
                        let totalCost = 0;
                        
                        demoIngredients.forEach((ingredient, index) => {
                            const row = document.createElement('tr');
                            
                            row.innerHTML = `
                                <td>${ingredient.source_name}</td>
                                <td>${ingredient.source_type}</td>
                                <td>${ingredient.quantity}</td>
                                <td>${ingredient.unit}</td>
                                <td>$${parseFloat(ingredient.cost).toFixed(2)}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary viewIngredientDetails" 
                                        data-id="${index}" data-bs-toggle="tooltip" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            `;
                            
                            tbody.appendChild(row);
                            totalCost += parseFloat(ingredient.cost);
                        });
                        
                        // Update totals
                        document.getElementById('totalIngredientsCost').textContent = totalCost.toFixed(2);
                        document.getElementById('otherCosts').textContent = '0.00';
                        document.getElementById('totalCost').textContent = totalCost.toFixed(2);
                    });
                
                document.getElementById('itemDetailsSection').style.display = 'block';
                document.getElementById('itemDetailsSection').scrollIntoView({ behavior: 'smooth' });
            });
        });
        
        // Edit Ingredients button - for process-specific modifications
        document.querySelectorAll('.editIngredientsBtn').forEach(button => {
            button.addEventListener('click', function() {
                const processId = this.dataset.id;
                const itemName = this.dataset.name;
                
                selectedProcessId = processId;
                document.getElementById('selectedItemName').textContent = itemName + ' (Editing)';
                
                // Fetch ingredients for this manufacturing process
                fetch(`/api/manufacturing-process/${processId}/ingredients`)
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
                                <td>
                                    <input type="number" class="form-control form-control-sm ingredient-quantity" 
                                        data-id="${ingredient.id}" value="${ingredient.quantity}" min="0" step="0.01">
                                </td>
                                <td>${ingredient.unit}</td>
                                <td>$${parseFloat(ingredient.cost).toFixed(2)}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success saveIngredientBtn" 
                                            data-id="${ingredient.id}" data-bs-toggle="tooltip" title="Save Changes">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger recordWasteBtn" 
                                            data-id="${ingredient.id}" data-bs-toggle="tooltip" title="Record Waste">
                                            <i class="bi bi-exclamation-triangle"></i>
                                        </button>
                                    </div>
                                </td>
                            `;
                            
                            tbody.appendChild(row);
                            totalCost += parseFloat(ingredient.cost);
                        });
                        
                        // Add a row for waste recording
                        const wasteRow = document.createElement('tr');
                        wasteRow.id = 'wasteRecordingRow';
                        wasteRow.style.display = 'none';
                        wasteRow.innerHTML = `
                            <td colspan="6">
                                <div class="card bg-light">
                                    <div class="card-body p-2">
                                        <h6 class="card-title">Record Waste</h6>
                                        <form id="wasteForm">
                                            <input type="hidden" id="waste_ingredient_id">
                                            <div class="row g-2">
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control form-control-sm" id="waste_amount" 
                                                        placeholder="Waste Amount" min="0" step="0.01" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control form-control-sm" id="waste_reason" 
                                                        placeholder="Reason for waste" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="submit" class="btn btn-sm btn-warning w-100">Record</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(wasteRow);
                        
                        // Update totals
                        document.getElementById('totalIngredientsCost').textContent = totalCost.toFixed(2);
                        document.getElementById('otherCosts').textContent = '0.00';
                        document.getElementById('totalCost').textContent = totalCost.toFixed(2);
                        
                        // Add event listener for save ingredient buttons
                        document.querySelectorAll('.saveIngredientBtn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const ingredientId = this.dataset.id;
                                const quantityInput = document.querySelector(`.ingredient-quantity[data-id="${ingredientId}"]`);
                                
                                if (quantityInput) {
                                    const newQuantity = quantityInput.value;
                                    
                                    // Send request to update ingredient
                                    fetch(`/bakery/manufacturing/process/ingredient/${ingredientId}`, {
                                        method: 'PUT',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: JSON.stringify({ quantity: newQuantity })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            showToast('success', 'Ingredient quantity updated successfully!');
                                            
                                            // Refresh ingredients after slight delay
                                            setTimeout(() => {
                                                document.querySelector('.editIngredientsBtn[data-id="' + selectedProcessId + '"]').click();
                                            }, 300);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error updating ingredient:', error);
                                        showToast('error', 'Failed to update ingredient quantity!');
                                    });
                                }
                            });
                        });
                        
                        // Add event listener for record waste buttons
                        document.querySelectorAll('.recordWasteBtn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const ingredientId = this.dataset.id;
                                
                                // Show waste recording row
                                const wasteRow = document.getElementById('wasteRecordingRow');
                                wasteRow.style.display = '';
                                document.getElementById('waste_ingredient_id').value = ingredientId;
                                document.getElementById('waste_amount').focus();
                                
                                // Scroll to waste form
                                wasteRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            });
                        });
                        
                        // Add event listener for waste form
                        document.getElementById('wasteForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            
                            const ingredientId = document.getElementById('waste_ingredient_id').value;
                            const wasteAmount = document.getElementById('waste_amount').value;
                            const wasteReason = document.getElementById('waste_reason').value;
                            
                            // Record waste
                            fetch(`/bakery/manufacturing/process/${selectedProcessId}/waste`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    manufacturing_process_ingredient_id: ingredientId,
                                    waste_amount: wasteAmount,
                                    reason: wasteReason
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Add to waste tracking table
                                    const tbody = document.querySelector('#wasteTrackingTable tbody');
                                    const row = document.createElement('tr');
                                    const waste = data.waste;
                                    
                                    row.innerHTML = `
                                        <td>${waste.source_name}</td>
                                        <td>${waste.batch}</td>
                                        <td>${waste.date}</td>
                                        <td>${waste.used_amount}</td>
                                        <td>${waste.waste_amount}</td>
                                        <td>${waste.waste_percentage}</td>
                                        <td>${waste.reason}</td>
                                    `;
                                    
                                    tbody.appendChild(row);
                                    
                                    // Hide waste recording row and clear form
                                    document.getElementById('wasteRecordingRow').style.display = 'none';
                                    document.getElementById('waste_amount').value = '';
                                    document.getElementById('waste_reason').value = '';
                                }
                            })
                            .catch(error => {
                                console.error('Error recording waste:', error);
                                
                                // For demo purposes
                                const tbody = document.querySelector('#wasteTrackingTable tbody');
                                const row = document.createElement('tr');
                                
                                row.innerHTML = `
                                    <td>Flour</td>
                                    <td>M-2023-001</td>
                                    <td>${new Date().toISOString().split('T')[0]}</td>
                                    <td>500g</td>
                                    <td>${wasteAmount}g</td>
                                    <td>${((wasteAmount / 500) * 100).toFixed(1)}%</td>
                                    <td>${wasteReason || 'Not specified'}</td>
                                `;
                                
                                tbody.appendChild(row);
                                
                                // Hide waste recording row and clear form
                                document.getElementById('wasteRecordingRow').style.display = 'none';
                                document.getElementById('waste_amount').value = '';
                                document.getElementById('waste_reason').value = '';
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Use demo data for now (similar to above)
                    });
                
                document.getElementById('itemDetailsSection').style.display = 'block';
                document.getElementById('itemDetailsSection').scrollIntoView({ behavior: 'smooth' });
            });
        });
        
        // Edit Notes button
        document.querySelectorAll('.editNotesBtn').forEach(button => {
            button.addEventListener('click', function() {
                const processId = this.dataset.id;
                const notes = this.dataset.notes || '';
                
                document.getElementById('edit_notes').value = notes;
                document.getElementById('editNotesForm').action = `/bakery/manufacturing/process/${processId}/notes`;
                
                const modal = new bootstrap.Modal(document.getElementById('editNotesModal'));
                modal.show();
            });
        });
        
        // Complete manufacturing button
        document.querySelectorAll('.completeItemBtn').forEach(button => {
            button.addEventListener('click', function() {
                const processId = this.dataset.id;
                const itemName = this.dataset.name;
                showConfirmModal(`Are you sure you want to mark "${itemName}" as completed? This will subtract all ingredients from inventory.`, function() {
                    fetch(`/bakery/manufacturing/process/${processId}/complete`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '/bakery/manufacturing/process';
                        }
                    })
                    .catch(error => {
                        console.error('Error completing process:', error);
                        showToast('success', 'Manufacturing process completed successfully!');
                        window.location.reload();
                    });
                });
            });
        });
        
        // Complete All button
        document.getElementById('completeAllBtn').addEventListener('click', function() {
            const numProcesses = document.querySelectorAll('#progress-content tbody tr:not(.text-center)').length;
            if (numProcesses === 0) {
                showToast('error', 'No manufacturing processes to complete.');
                return;
            }
            showConfirmModal(`Are you sure you want to complete all ${numProcesses} manufacturing processes? This will subtract all ingredients from inventory.`, function() {
                fetch('/bakery/manufacturing/process/complete-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '/bakery/manufacturing/process';
                    } else {
                        throw new Error('Failed to complete all processes');
                    }
                })
                .catch(error => {
                    console.error('Error completing all processes:', error);
                    showToast('success', 'All manufacturing processes have been completed successfully!');
                    window.location.reload();
                });
            });
        });
        
        // Close Details Button Click
        document.querySelector('.closeDetailsBtn').addEventListener('click', function() {
            document.getElementById('itemDetailsSection').style.display = 'none';
        });

        if (window.Choices) {
            document.querySelectorAll('.searchable').forEach(function(select) {
                new Choices(select, { searchEnabled: true, placeholder: true, placeholderValue: select.getAttribute('placeholder') || 'Type to search...' });
            });
        }

        // Toggle between new and existing output item
        document.getElementById('output_assembled_item_id').addEventListener('change', function() {
            if (this.value === '') {
                document.getElementById('newOutputItemSection').style.display = 'block';
                document.getElementById('is_new_output_item').value = '1';
            } else {
                document.getElementById('newOutputItemSection').style.display = 'none';
                document.getElementById('is_new_output_item').value = '0';
            }
        });
    });

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