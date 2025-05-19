<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $products = $business->products()
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('sku', 'like', "%{$request->search}%")
                      ->orWhere('barcode', 'like', "%{$request->search}%");
                });
            })
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->type, fn($q) => $q->ofType($request->type))
            ->when($request->active, fn($q) => $q->active())
            ->when($request->featured, fn($q) => $q->featured())
            ->when($request->stock_status, function($query) use ($request) {
                match($request->stock_status) {
                    'in_stock' => $query->inStock(),
                    'low_stock' => $query->lowStock(),
                    'out_of_stock' => $query->whereDoesntHave('inventory', function($q) {
                        $q->where('available_quantity', '>', 0);
                    }),
                    default => null
                };
            })
            ->with(['category:id,name', 'inventory'])
            ->latest()
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($products);
    }

    public function store(Request $request, Business $business)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => [
                'nullable',
                'exists:categories,id',
                function($attribute, $value, $fail) use ($business) {
                    if ($value) {
                        $category = \App\Models\Category::find($value);
                        if ($category->business_id !== $business->id) {
                            $fail('The selected category does not belong to this business.');
                        }
                    }
                },
            ],
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'discount_start_date' => 'nullable|date|required_with:discount_price',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
            'unit' => 'required|string|max:20',
            'is_featured' => 'boolean',
            'is_digital' => 'boolean',
            'track_inventory' => 'boolean',
            'alert_quantity' => 'required|integer|min:0',
            'type' => ['required', Rule::in(['bakery', 'cake_tool'])],
            'attributes' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $product = $business->products()->create($validator->validated());

        // Create initial inventory records for all stores
        $business->stores()->each(function($store) use ($product) {
            $store->inventory()->create([
                'business_id' => $product->business_id,
                'product_id' => $product->id,
                'quantity' => 0,
                'available_quantity' => 0,
            ]);
        });

        return $this->successResponse($product, 'Product created successfully', 201);
    }

    public function show(Business $business, Product $product)
    {
        if ($product->business_id !== $business->id) {
            return $this->errorResponse('Product does not belong to this business', 403);
        }

        $product->load([
            'category',
            'inventory.store',
        ]);

        return $this->successResponse($product);
    }

    public function update(Request $request, Business $business, Product $product)
    {
        if ($product->business_id !== $business->id) {
            return $this->errorResponse('Product does not belong to this business', 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => [
                'nullable',
                'exists:categories,id',
                function($attribute, $value, $fail) use ($business) {
                    if ($value) {
                        $category = \App\Models\Category::find($value);
                        if ($category->business_id !== $business->id) {
                            $fail('The selected category does not belong to this business.');
                        }
                    }
                },
            ],
            'description' => 'nullable|string',
            'barcode' => ['nullable', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'discount_start_date' => 'nullable|date|required_with:discount_price',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
            'unit' => 'required|string|max:20',
            'is_featured' => 'boolean',
            'is_digital' => 'boolean',
            'track_inventory' => 'boolean',
            'alert_quantity' => 'required|integer|min:0',
            'type' => ['required', Rule::in(['bakery', 'cake_tool'])],
            'attributes' => 'nullable|array',
            'metadata' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $product->update($validator->validated());

        return $this->successResponse($product, 'Product updated successfully');
    }

    public function destroy(Business $business, Product $product)
    {
        if ($product->business_id !== $business->id) {
            return $this->errorResponse('Product does not belong to this business', 403);
        }

        // Check if product has any inventory movements
        if ($product->inventoryMovements()->exists()) {
            return $this->errorResponse('Cannot delete product with inventory history', 400);
        }

        $product->delete();

        return $this->successResponse(null, 'Product deleted successfully');
    }

    public function toggleStatus(Business $business, Product $product)
    {
        if ($product->business_id !== $business->id) {
            return $this->errorResponse('Product does not belong to this business', 403);
        }

        $product->update(['is_active' => !$product->is_active]);

        return $this->successResponse([
            'is_active' => $product->is_active
        ], 'Product status updated successfully');
    }

    public function toggleFeatured(Business $business, Product $product)
    {
        if ($product->business_id !== $business->id) {
            return $this->errorResponse('Product does not belong to this business', 403);
        }

        $product->update(['is_featured' => !$product->is_featured]);

        return $this->successResponse([
            'is_featured' => $product->is_featured
        ], 'Product featured status updated successfully');
    }

    public function updatePrices(Request $request, Business $business, Product $product)
    {
        if ($product->business_id !== $business->id) {
            return $this->errorResponse('Product does not belong to this business', 403);
        }

        $validator = Validator::make($request->all(), [
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'discount_start_date' => 'nullable|date|required_with:discount_price',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $product->update($validator->validated());

        return $this->successResponse($product, 'Product prices updated successfully');
    }

    /**
     * Display bakery items
     */
    public function bakeryItems()
    {
        $businessId = session('business_id');
        $products = Product::where('business_id', $businessId)
            ->where('type', config('constants.product_types.bakery'))
            ->with('category')
            ->paginate(20);
        
        $categories = Category::where('business_id', $businessId)
            ->where('slug', 'like', 'bakery-%')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('bakery.items', compact('products', 'categories'));
    }

    /**
     * Display bakery categories
     */
    public function bakeryCategories()
    {
        return view('bakery.products.categories');
    }

    /**
     * Display tools items
     */
    public function toolsItems()
    {
        return view('tools.products.items');
    }

    /**
     * Display tools categories
     */
    public function toolsCategories()
    {
        return view('tools.products.categories');
    }

    /**
     * AJAX: Check if product name is unique
     */
    public function checkNameUnique(Request $request)
    {
        $name = $request->input('name');
        $id = $request->input('id');
        $businessId = session('business_id');
        $query = Product::where('business_id', $businessId)
            ->where('name', $name);
        if ($id) {
            $query->where('id', '!=', $id);
        }
        $exists = $query->exists();
        return response()->json(['unique' => !$exists]);
    }

    /**
     * Store a new bakery item
     */
    public function storeBakeryItem(Request $request)
    {
        $businessId = session('business_id');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'track_inventory' => 'nullable|boolean',
        ]);
        
        $validated['business_id'] = $businessId;
        $validated['type'] = config('constants.product_types.bakery');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');
        $validated['track_inventory'] = $request->has('track_inventory');
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/products', $imageName);
            $validated['image'] = 'products/' . $imageName;
        }
        
        $product = Product::create($validated);
        
        // Create initial stock entry if needed
        if ($product && $request->filled('initial_stock')) {
            // Handle initial stock logic here
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item created successfully',
                'product' => $product
            ]);
        }
        
        return redirect()->route('bakery.items')
            ->with('success', 'Item created successfully');
    }

    /**
     * Update a bakery item
     */
    public function updateBakeryItem(Request $request, Product $product)
    {
        // Check if this product belongs to the current business
        if ($product->business_id != session('business_id')) {
            return redirect()->route('bakery.items')
                ->with('error', 'You do not have permission to edit this item');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'track_inventory' => 'nullable|boolean',
        ]);
        
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');
        $validated['track_inventory'] = $request->has('track_inventory');
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::delete('public/' . $product->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/products', $imageName);
            $validated['image'] = 'products/' . $imageName;
        }
        
        $product->update($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'product' => $product
            ]);
        }
        
        return redirect()->route('bakery.items')
            ->with('success', 'Item updated successfully');
    }

    /**
     * Delete a bakery item
     */
    public function destroyBakeryItem(Product $product)
    {
        // Check if this product belongs to the current business
        if ($product->business_id != session('business_id')) {
            return redirect()->route('bakery.items')
                ->with('error', 'You do not have permission to delete this item');
        }
        
        // Check if the product is being used in any assembly or process
        $isInUse = false; // Implement logic to check if the product is in use
        
        if ($isInUse) {
            return redirect()->route('bakery.items')
                ->with('error', 'This item cannot be deleted as it is being used in assemblies or processes');
        }
        
        // Delete the product image if it exists
        if ($product->image) {
            Storage::delete('public/' . $product->image);
        }
        
        $product->delete();
        
        return redirect()->route('bakery.items')
            ->with('success', 'Item deleted successfully');
    }

    /**
     * Store a new product category via AJAX
     */
    public function storeCategory(Request $request)
    {
        $businessId = session('business_id');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        // Check for duplicate name
        $exists = Category::where('business_id', $businessId)
            ->where('name', $validated['name'])
            ->exists();
        
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A category with this name already exists'
            ], 422);
        }
        
        // Create the new category
        $category = new Category([
            'business_id' => $businessId,
            'name' => $validated['name'],
            'slug' => 'bakery-' . $businessId . '-' . \Illuminate\Support\Str::slug($validated['name']),
            'is_active' => true
        ]);
        
        $category->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category
        ]);
    }
} 