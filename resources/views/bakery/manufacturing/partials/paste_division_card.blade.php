<!-- resources/views/manufacturing/partials/paste_division_card.blade.php -->
<div class="card mt-3" id="pasteDivisionCard" style="display: none;">
    <div class="card-body">
        <h5 class="card-title">Paste Division</h5>
        <div class="alert alert-info">
            Divide this paste into smaller portions with different flavors.
        </div>
        <button class="btn btn-outline-primary" id="dividePasteBtn" data-bs-toggle="modal" data-bs-target="#pasteDivisionModal">
            <i class="bi bi-diagram-3"></i> Divide Paste
        </button>
    </div>
</div>

<!-- Paste Division Modal -->
<div class="modal fade" id="pasteDivisionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="pasteDivisionForm" method="POST">
                @csrf
                <input type="hidden" id="paste_id" name="paste_id">
                <input type="hidden" id="paste_name" name="paste_name">
                
                <div class="modal-header">
                    <h5 class="modal-title">Divide Paste into Portions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Source Paste: <span id="pasteName" class="fw-bold text-primary"></span></h6>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">Available quantity: <span id="availablePasteQty">0.00</span> kg</small>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>
                                <p class="mb-0">Divide the paste into portions by adding flavors and specifying output items.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="flavor" class="form-label">Flavor</label>
                            <select class="form-select searchable" id="flavor" name="flavor" placeholder="Search for flavors">
                                <option value="">Select Flavor</option>
                                @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->name }}" data-cost="{{ $ingredient->cost_price }}">{{ $ingredient->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="flavor_quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="flavor_quantity" name="flavor_quantity" step="0.01" min="0.01">
                        </div>
                        <div class="col-md-4">
                            <label for="flavor_cost" class="form-label">Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="flavor_cost" name="flavor_cost" step="0.01" min="0" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="use_existing_item" class="form-label">Output Item</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="use_existing_item" name="use_existing_item">
                                <label class="form-check-label" for="use_existing_item">Use existing item</label>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Output Item Section -->
                    <div class="row mb-3" id="existingOutputItemSection" style="display: none;">
                        <div class="col-md-12">
                            <select class="form-select searchable" id="output_assembled_item_id" name="output_assembled_item_id" placeholder="Type to search or select existing item">
                                <option value="">Select Existing Item</option>
                                @foreach($assembledItems->where('type', '!=', 'paste') as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ ucfirst($item->type) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Custom Output Item Section -->
                    <div class="row mb-3" id="customOutputItemSection">
                        <div class="col-md-6">
                            <label for="output_name" class="form-label">Output Name</label>
                            <input type="text" class="form-control" id="output_name" name="output_name">
                        </div>
                        <div class="col-md-6">
                            <label for="output_type" class="form-label">Output Type</label>
                            <select class="form-select searchable" id="output_type" name="output_type">
                                <option value="single">Single Item</option>
                                <option value="box">Box</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="paste_quantity" class="form-label">Paste Quantity (kg)</label>
                            <input type="number" class="form-control" id="paste_quantity" name="paste_quantity" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label for="output_quantity" class="form-label">Output Quantity</label>
                            <input type="number" class="form-control" id="output_quantity" name="output_quantity" step="1" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label for="output_unit" class="form-label">Output Unit</label>
                            <input type="text" class="form-control" id="output_unit" name="output_unit" value="piece" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="division_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="division_notes" name="division_notes" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Divide Paste</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize searchable selects for the paste division modal
        if (window.appData && window.appData.initChoices) {
            window.appData.initChoices();
        }
        
        // Toggle between existing and custom output item
        document.getElementById('use_existing_item').addEventListener('change', function() {
            document.getElementById('existingOutputItemSection').style.display = this.checked ? 'block' : 'none';
            document.getElementById('customOutputItemSection').style.display = this.checked ? 'none' : 'block';
        });
        
        // Update flavor cost when flavor or quantity changes
        document.getElementById('flavor').addEventListener('change', updateFlavorCost);
        document.getElementById('flavor_quantity').addEventListener('input', updateFlavorCost);
        
        function updateFlavorCost() {
            const flavorSelect = document.getElementById('flavor');
            const quantity = parseFloat(document.getElementById('flavor_quantity').value) || 0;
            
            if (flavorSelect.selectedIndex > 0) {
                const selectedOption = flavorSelect.options[flavorSelect.selectedIndex];
                const costPerUnit = parseFloat(selectedOption.dataset.cost) || 0;
                const totalCost = costPerUnit * quantity;
                document.getElementById('flavor_cost').value = totalCost.toFixed(2);
            } else {
                document.getElementById('flavor_cost').value = '0.00';
            }
        }
        
        // Set up the divide paste modal
        const pasteDivisionModal = document.getElementById('pasteDivisionModal');
        pasteDivisionModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const pasteId = button.getAttribute('data-id');
            const pasteName = button.getAttribute('data-name');
            
            document.getElementById('paste_id').value = pasteId;
            document.getElementById('paste_name').value = pasteName;
            document.getElementById('pasteName').textContent = pasteName;
            
            // Reset form
            document.getElementById('flavor').selectedIndex = 0;
            document.getElementById('flavor_quantity').value = '';
            document.getElementById('flavor_cost').value = '0.00';
            document.getElementById('paste_quantity').value = '';
            document.getElementById('output_quantity').value = '';
            document.getElementById('output_name').value = '';
            document.getElementById('output_unit').value = 'piece';
            document.getElementById('division_notes').value = '';
            document.getElementById('use_existing_item').checked = false;
            document.getElementById('existingOutputItemSection').style.display = 'none';
            document.getElementById('customOutputItemSection').style.display = 'block';
            
            // Fetch available paste quantity
            fetch(`/manufacturing/paste/${pasteId}/available`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('availablePasteQty').textContent = data.available_qty.toFixed(2);
                })
                .catch(error => {
                    console.error('Error fetching paste data:', error);
                });
            
            setTimeout(() => {
                if (window.appData && window.appData.initChoices) {
                    window.appData.initChoices();
                }
            }, 100);
        });
        
        // Handle form submission
        document.getElementById('pasteDivisionForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const form = this;
            const useExistingItem = document.getElementById('use_existing_item').checked;
            const outputItemId = useExistingItem ? document.getElementById('output_assembled_item_id').value : null;
            const outputName = !useExistingItem ? document.getElementById('output_name').value : null;
            
            // Validation
            if (useExistingItem && !outputItemId) {
                alert('Please select an existing output item');
                return;
            }
            
            if (!useExistingItem && !outputName) {
                alert('Please enter a name for the new output item');
                return;
            }
            
            const pasteQuantity = parseFloat(document.getElementById('paste_quantity').value);
            const availableQuantity = parseFloat(document.getElementById('availablePasteQty').textContent);
            
            if (pasteQuantity > availableQuantity) {
                alert(`You cannot use more than the available paste quantity (${availableQuantity.toFixed(2)} kg)`);
                return;
            }
            
            // Submit form via AJAX
            const formData = new FormData(form);
            formData.append('is_existing_item', useExistingItem);
            
            fetch('/manufacturing/paste/divide', {
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
                    bootstrap.Modal.getInstance(pasteDivisionModal).hide();
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error dividing paste:', error);
                alert('An error occurred while dividing the paste. Please try again.');
            });
        });
    });
</script>
@endpush