<!-- resources/views/manufacturing/partials/ingredient_form.blade.php -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Add Ingredient</h5>
        <form id="addIngredientForm">
            <input type="hidden" id="assembledItemId" name="assembledItemId">
            <div class="mb-3">
                <label for="sourceType" class="form-label">Source Type</label>
                <select class="form-select" id="sourceType" name="sourceType" required>
                    <option value="">Select Source Type</option>
                    <option value="ingredient">Raw Ingredient</option>
                    <option value="product">Product</option>
                    <option value="assembled_item">Assembled Item</option>
                </select>
            </div>
            
            <div class="mb-3" id="ingredientSelectDiv" style="display: none;">
                <label for="ingredientId" class="form-label">Ingredient</label>
                <select class="form-select" id="ingredientId" name="ingredientId">
                    <option value="">Select Ingredient</option>
                    @foreach($ingredients ?? [] as $ingredient)
                        <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit }}" data-cost="{{ $ingredient->cost_price }}">
                            {{ $ingredient->name }} ({{ $ingredient->unit }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-3" id="productSelectDiv" style="display: none;">
                <label for="productId" class="form-label">Product</label>
                <select class="form-select" id="productId" name="productId">
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
                <select class="form-select" id="assembledItemIdRef" name="assembledItemIdRef">
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