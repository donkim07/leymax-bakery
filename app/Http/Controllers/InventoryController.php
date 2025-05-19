<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Store;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::whereHas('product.business', function($query) {
            $query->whereHas('users', function($q) {
                $q->where('user_id', auth()->id());
            });
        })->paginate(10);

        return view('inventory.index', compact('inventory'));
    }

    public function movements()
    {
        // Placeholder for inventory movements page
        return view('inventory.movements');
    }

    public function adjust(Request $request)
    {
        // Validate request
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'type' => 'required|in:in,out,adjustment',
            'notes' => 'nullable|string',
            'batch_number' => 'nullable|string',
            'expiry_date' => 'nullable|date',
                ]);

        // Process adjustment
        // This would typically update inventory records and create movement logs
        
        return redirect()->back()->with('success', 'Inventory adjusted successfully');
    }

    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function() use ($request) {
                // Source inventory
                $sourceInventory = Inventory::where([
                    'store_id' => $request->from_store_id,
                    'product_id' => $request->product_id,
                ])->firstOrFail();

                if ($sourceInventory->available_quantity < $request->quantity) {
                    throw new \Exception('Insufficient quantity in source store');
                }

                // Destination inventory
                $destInventory = Inventory::firstOrCreate([
                    'store_id' => $request->to_store_id,
                    'product_id' => $request->product_id,
                ]);

                // Update quantities
                $sourceInventory->available_quantity -= $request->quantity;
                $destInventory->available_quantity += $request->quantity;

                $sourceInventory->save();
                $destInventory->save();

                // Record movements
                InventoryMovement::create([
                    'inventory_id' => $sourceInventory->id,
                    'quantity' => -$request->quantity,
                    'type' => 'transfer_out',
                    'reason' => $request->reason,
                    'reference_id' => $destInventory->id,
                    'created_by' => auth()->id(),
                ]);

                InventoryMovement::create([
                    'inventory_id' => $destInventory->id,
                    'quantity' => $request->quantity,
                    'type' => 'transfer_in',
                    'reason' => $request->reason,
                    'reference_id' => $sourceInventory->id,
                    'created_by' => auth()->id(),
                ]);
            });

            return back()->with('success', 'Inventory transferred successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to transfer inventory: ' . $e->getMessage());
        }
    }

    public function adjustStock(Request $request, Business $business, Store $store, Product $product)
    {
        if ($store->business_id !== $business->id || $product->business_id !== $business->id) {
            return $this->errorResponse('Invalid store or product for this business', 403);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'type' => ['required', 'string', Rule::in(['in', 'out', 'adjustment'])],
            'notes' => 'nullable|string',
            'batch_number' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date|after:today',
            'rack_location' => 'nullable|string|max:50',
            'bin_location' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            $inventory = $store->inventory()
                ->where('product_id', $product->id)
                ->where('business_id', $business->id)
                ->first();

            if (!$inventory) {
                $inventory = $store->inventory()->create([
                    'business_id' => $business->id,
                    'product_id' => $product->id,
                    'quantity' => 0,
                    'available_quantity' => 0,
                ]);
            }

            // Update locations if provided
            if ($request->filled('rack_location')) {
                $inventory->rack_location = $request->rack_location;
            }
            if ($request->filled('bin_location')) {
                $inventory->bin_location = $request->bin_location;
            }

            // Calculate new quantities
            $quantity = $request->quantity;
            switch ($request->type) {
                case 'in':
                    $inventory->quantity += $quantity;
                    $inventory->available_quantity += $quantity;
                    break;
                case 'out':
                    if ($inventory->available_quantity < $quantity) {
                        throw new \Exception('Insufficient stock available');
                    }
                    $inventory->quantity -= $quantity;
                    $inventory->available_quantity -= $quantity;
                    break;
                case 'adjustment':
                    $difference = $quantity - $inventory->quantity;
                    $inventory->quantity = $quantity;
                    $inventory->available_quantity += $difference;
                    break;
            }

            // Update batch and expiry if provided
            if ($request->filled('batch_number')) {
                $inventory->batch_number = $request->batch_number;
            }
            if ($request->filled('expiry_date')) {
                $inventory->expiry_date = $request->expiry_date;
            }

            $inventory->save();

            // Create movement record
            InventoryMovement::create([
                'business_id' => $business->id,
                'store_id' => $store->id,
                'product_id' => $product->id,
                'type' => $request->type,
                'quantity' => $request->type === 'out' ? -$quantity : $quantity,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return $this->successResponse($inventory, 'Stock adjusted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function transferBusiness(Request $request, Business $business)
    {
        $validator = Validator::make($request->all(), [
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $fromStore = Store::find($request->from_store_id);
        $toStore = Store::find($request->to_store_id);
        $product = Product::find($request->product_id);

        // Validate ownership
        if ($fromStore->business_id !== $business->id || 
            $toStore->business_id !== $business->id || 
            $product->business_id !== $business->id) {
            return $this->errorResponse('Invalid store or product for this business', 403);
        }

        try {
            DB::beginTransaction();

            // Get source inventory
            $sourceInventory = $fromStore->inventory()
                ->where('product_id', $product->id)
                ->first();

            if (!$sourceInventory || $sourceInventory->available_quantity < $request->quantity) {
                throw new \Exception('Insufficient stock in source store');
            }

            // Get or create destination inventory
            $destInventory = $toStore->inventory()
                ->where('product_id', $product->id)
                ->first();

            if (!$destInventory) {
                $destInventory = $toStore->inventory()->create([
                    'business_id' => $business->id,
                    'product_id' => $product->id,
                    'quantity' => 0,
                    'available_quantity' => 0,
                ]);
            }

            // Update quantities
            $sourceInventory->quantity -= $request->quantity;
            $sourceInventory->available_quantity -= $request->quantity;
            $sourceInventory->save();

            $destInventory->quantity += $request->quantity;
            $destInventory->available_quantity += $request->quantity;
            $destInventory->save();

            // Create movement records
            InventoryMovement::create([
                'business_id' => $business->id,
                'store_id' => $fromStore->id,
                'product_id' => $product->id,
                'type' => 'transfer',
                'quantity' => -$request->quantity,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            InventoryMovement::create([
                'business_id' => $business->id,
                'store_id' => $toStore->id,
                'product_id' => $product->id,
                'type' => 'transfer',
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return $this->successResponse([
                'source' => $sourceInventory,
                'destination' => $destInventory,
            ], 'Stock transferred successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function movementsData(Request $request, Business $business)
    {
        $movements = InventoryMovement::query()
            ->where('business_id', $business->id)
            ->when($request->store_id, fn($q) => $q->where('store_id', $request->store_id))
            ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->type, fn($q) => $q->ofType($request->type))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->with([
                'product:id,name,sku',
                'store:id,name',
                'creator:id,name',
            ])
            ->latest()
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($movements);
    }

    public function alerts(Request $request, Business $business)
    {
        $alerts = $business->inventory()
            ->with(['product:id,name,sku,alert_quantity', 'store:id,name'])
            ->when($request->store_id, fn($q) => $q->where('store_id', $request->store_id))
            ->where(function($query) {
                $query->whereRaw('available_quantity <= (SELECT alert_quantity FROM products WHERE products.id = inventory.product_id)')
                    ->orWhere(function($q) {
                        $q->whereNotNull('expiry_date')
                            ->whereDate('expiry_date', '<=', now()->addDays(30));
                    });
            })
            ->paginate($request->per_page ?? 10);

        return $this->paginatedResponse($alerts);
    }
} 