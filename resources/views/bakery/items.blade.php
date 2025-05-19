@extends('layouts.app')

@section('title', 'Bakery Inventory')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('bakery.dashboard') }}">Bakery</a></li>
<li class="breadcrumb-item active">Inventory Items</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title m-0">Bakery Items</h5>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createItemModal">
                            <i class="bi bi-plus-circle me-1"></i> Add New Item
                        </button>
                    </div>
                </div>

                <!-- Filter Row -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search items...">
                    </div>
                    <div class="col-md-2">
                        <select id="categoryFilter" class="form-select searchable" placeholder="Filter by Category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="statusFilter" class="form-select searchable" placeholder="Filter by Status">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Item Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="itemsTable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        @if($product->image)
                                            <img src="{{ asset('storage/app/public/'.$product->image) }}" width="50" height="50" class="rounded">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                                                <i class="bi bi-image text-secondary"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>{{ $product->current_stock ?? '0' }} {{ $product->unit ?? 'unit' }}</td>
                                    <td>${{ number_format($product->cost_price, 2) }}</td>
                                    <td>${{ number_format($product->selling_price, 2) }}</td>
                                    <td>
                                        @if($product->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary viewItemBtn"
                                                data-id="{{ $product->id }}"
                                                data-bs-toggle="tooltip" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info editItemBtn"
                                                data-id="{{ $product->id }}"
                                                data-bs-toggle="modal" data-bs-target="#editItemModal"
                                                title="Edit Item">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger deleteItemBtn"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-bs-toggle="tooltip" title="Delete Item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No items found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Item Modal -->
<div class="modal fade" id="createItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createItemForm" action="{{ route('bakery.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="name" name="name" required data-unique-check="product">
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select searchable" id="category_id" name="category_id" placeholder="Search or select category">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                                <option value="new">+ Add New Category</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="cost_price" class="form-label">Cost Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="cost_price" name="cost_price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="selling_price" class="form-label">Selling Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="selling_price" name="selling_price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" value="piece">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control" id="sku" name="sku">
                        </div>
                        <div class="col-md-6">
                            <label for="barcode" class="form-label">Barcode</label>
                            <input type="text" class="form-control" id="barcode" name="barcode">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                <label class="form-check-label" for="is_featured">Featured Item</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="track_inventory" name="track_inventory" checked>
                                <label class="form-check-label" for="track_inventory">Track Inventory</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
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

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editItemForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required data-unique-check="product" data-id="">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_category_id" class="form-label">Category</label>
                            <select class="form-select searchable" id="edit_category_id" name="category_id" placeholder="Search or select category">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Same structure as create modal for other fields... -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="edit_cost_price" class="form-label">Cost Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="edit_cost_price" name="cost_price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_selling_price" class="form-label">Selling Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="edit_selling_price" name="selling_price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="edit_unit" name="unit">
                        </div>
                    </div>
                    
                    <!-- Additional fields follow same pattern -->
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

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createCategoryForm" action="{{ route('bakery.categories.store') }}" method="POST">
                @csrf
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

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        if (tooltipTriggerList.length > 0) {
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }
        
        // Make searchable selects
        if (window.appData && window.appData.initChoices) {
            window.appData.initChoices();
        }
        
        // Filter functionality
        document.getElementById('searchInput').addEventListener('keyup', filterItems);
        document.getElementById('categoryFilter').addEventListener('change', filterItems);
        document.getElementById('statusFilter').addEventListener('change', filterItems);
        
        function filterItems() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const categoryValue = document.getElementById('categoryFilter').value.toLowerCase();
            const statusValue = document.getElementById('statusFilter').value.toLowerCase();
            
            const rows = document.querySelectorAll('#itemsTable tbody tr');
            rows.forEach(row => {
                const nameCell = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const categoryCell = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const statusEl = row.querySelector('td:nth-child(7) .badge');
                const status = statusEl ? statusEl.textContent.toLowerCase() : '';
                
                const matchesSearch = searchValue === '' || nameCell.includes(searchValue);
                const matchesCategory = categoryValue === '' || categoryCell === categoryValue;
                const matchesStatus = statusValue === '' || (statusValue === 'active' && status === 'active') || (statusValue === 'inactive' && status === 'inactive');
                
                row.style.display = (matchesSearch && matchesCategory && matchesStatus) ? '' : 'none';
            });
        }
        
        // Edit Item Button Click
        document.querySelectorAll('.editItemBtn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.id;
                
                fetch(`/bakery/items/${itemId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('editItemForm').action = `/bakery/items/${itemId}`;
                        document.getElementById('edit_name').value = data.name;
                        document.getElementById('edit_name').dataset.id = data.id;
                        document.getElementById('edit_category_id').value = data.category_id;
                        document.getElementById('edit_cost_price').value = data.cost_price;
                        document.getElementById('edit_selling_price').value = data.selling_price;
                        document.getElementById('edit_unit').value = data.unit;
                        // Set other fields...
                        
                        // Re-initialize Choice.js
                        if (window.appData && window.appData.initChoices) {
                            window.appData.initChoices();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching item details:', error);
                        showToast('error', 'Failed to load item details');
                    });
            });
        });
        
        // Delete Item Button Click
        document.querySelectorAll('.deleteItemBtn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.id;
                const itemName = this.dataset.name;
                
                document.getElementById('deleteItemName').textContent = itemName;
                document.getElementById('deleteItemForm').action = `/bakery/items/${itemId}`;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteItemModal'));
                deleteModal.show();
            });
        });
        
        // Category creation from dropdown
        document.getElementById('category_id').addEventListener('change', function() {
            if (this.value === 'new') {
                const categoryModal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
                categoryModal.show();
                this.value = ''; // Reset the select value
            }
        });
        
        // Handle creating category via modal
        document.getElementById('createCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            
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
                    
                    // Add the new category to the dropdowns
                    const options = [
                        { select: document.getElementById('category_id'), beforeValue: 'new' },
                        { select: document.getElementById('edit_category_id'), beforeValue: null }
                    ];
                    
                    options.forEach(({ select, beforeValue }) => {
                        if (select) {
                            const option = new Option(data.category.name, data.category.id);
                            if (beforeValue) {
                                // Find the "Add New" option and insert before it
                                const beforeOption = Array.from(select.options).find(o => o.value === beforeValue);
                                select.insertBefore(option, beforeOption);
                            } else {
                                // Append at the end
                                select.appendChild(option);
                            }
                            
                            // Set as selected
                            select.value = data.category.id;
                        }
                    });
                    
                    // Clear the form
                    document.getElementById('category_name').value = '';
                    
                    // Re-initialize Choices.js
                    if (window.appData && window.appData.initChoices) {
                        window.appData.initChoices();
                    }
                    
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
    });
    
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
</script>
@endpush 