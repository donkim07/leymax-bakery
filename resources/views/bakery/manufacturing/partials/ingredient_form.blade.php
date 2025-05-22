<!-- resources/views/manufacturing/partials/ingredient_form.blade.php -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Add Ingredient</h5>
        <form id="addIngredientForm">
            <input type="hidden" id="assembledItemId" name="assembledItemId">
            <div class="mb-3">
                <label for="sourceType" class="form-label">Source Type</label>
                <select class="form-select searchable" id="sourceType" name="sourceType" required placeholder="Select source type">
                    <option value="">Select Source Type</option>
                    <option value="product">Product</option>
                    <option value="assembled_item">Assembled Item</option>
                </select>
            </div>
            
            <div class="mb-3" id="productSelectDiv" style="display: none;">
                <label for="productId" class="form-label">Product</label>
                <select class="form-select searchable" id="productId" name="productId" placeholder="Search for a product">
                    <option value="">Select Product</option>
                    @foreach($products ?? [] as $product)
                        <option value="{{ $product->id }}" data-unit="{{ $product->unit ?? 'unit' }}" data-cost="{{ $product->cost_price }}">
                            {{ $product->name }} ({{ $product->unit ?? 'unit' }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-3" id="assembledItemSelectDiv" style="display: none;">
                <label for="assembledItemIdRef" class="form-label">Assembled Item</label>
                <select class="form-select searchable" id="assembledItemIdRef" name="assembledItemIdRef" placeholder="Search for an assembled item">
                    <option value="">Select Assembled Item</option>
                    @foreach($existingAssembledItems ?? [] as $existingItem)
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make all .searchable selects searchable with proper configuration
        if (window.Choices) {
            document.querySelectorAll('.searchable').forEach(function(select) {
                if (!select.classList.contains('choices-initialized')) {
                    new Choices(select, { 
                        searchEnabled: true, 
                        shouldSort: false, 
                        removeItemButton: true,
                        placeholder: true, 
                        placeholderValue: select.getAttribute('placeholder') || 'Type to search...',
                        classNames: {
                            containerOuter: 'choices search-enabled'
                        }
                    });
                    select.classList.add('choices-initialized');
                }
            });
        }
        
        // Source type change handler with better show/hide logic
        document.getElementById('sourceType').addEventListener('change', function() {
            const sourceType = this.value;
            document.getElementById('productSelectDiv').style.display = 'none';
            document.getElementById('assembledItemSelectDiv').style.display = 'none';
            
            if (sourceType === 'product') {
                document.getElementById('productSelectDiv').style.display = 'block';
                setTimeout(() => {
                    const select = document.getElementById('productId');
                    if (select && window.Choices && select._choices) {
                        select._choices.showDropdown();
                    }
                }, 100);
            } else if (sourceType === 'assembled_item') {
                document.getElementById('assembledItemSelectDiv').style.display = 'block';
                setTimeout(() => {
                    const select = document.getElementById('assembledItemIdRef');
                    if (select && window.Choices && select._choices) {
                        select._choices.showDropdown();
                    }
                }, 100);
            }
        });
    });
</script>
@endpush 