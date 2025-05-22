<?php

namespace App\Http\Controllers\Bakery;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ManufacturingController as BaseManufacturingController;
use App\Models\AssembledItem;
use App\Models\ManufacturingProcess;
use App\Models\ProductionPlan;
use App\Models\ManufacturingWaste;
use App\Models\ManufacturingMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManufacturingController extends BaseManufacturingController
{
    /**
     * Constructor to ensure business_id is set
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Display the assembly page for the bakery business unit
     */
    public function assembly()
    {
        $data = parent::assembly();
        return $data->setPath('bakery.manufacturing.assembly');
    }
    
    /**
     * Display the manufacturing process page for the bakery business unit
     */
    public function process()
    {
        $businessId = session('business_id');
        
        // Get stores for selection
        $stores = \App\Models\Store::where('business_id', $businessId)
            ->where('is_active', true)
            ->get();
        
        $data = parent::process();
        
        // Add stores to the view data
        if ($data instanceof \Illuminate\View\View) {
            $data->with('stores', $stores);
        }
        
        return $data;
    }
    
    /**
     * Display the stock adjustment page for the bakery business unit
     */
    public function adjustment()
    {
        $data = parent::adjustment();
        return $data->setPath('bakery.manufacturing.adjustment');
    }
    
    /**
     * Display the production planning page for the bakery business unit
     */
    public function planning()
    {
        $data = parent::planning();
        return $data->setPath('bakery.manufacturing.planning');
    }
    
    /**
     * Display the waste management page for the bakery business unit
     */
    public function waste()
    {
        $data = parent::waste();
        return $data->setPath('bakery.manufacturing.waste');
    }
    
    /**
     * Display the efficiency metrics page for the bakery business unit
     */
    public function metrics()
    {
        $data = parent::metrics();
        return $data->setPath('bakery.manufacturing.metrics');
    }
    
    /**
     * Store a new production plan for the bakery business unit
     */
    public function storePlan(Request $request)
    {
        return parent::storePlan($request);
    }
    
    /**
     * Update a production plan status for the bakery business unit
     */
    public function updatePlanStatus(Request $request, ProductionPlan $productionPlan)
    {
        return parent::updatePlanStatus($request, $productionPlan);
    }
    
    /**
     * Store a new manufacturing process with store selection
     */
    public function storeProcess(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'store_id' => 'required|exists:stores,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create manufacturing process
            $process = ManufacturingProcess::create([
                'business_id' => session('business_id'),
                'name' => $request->name,
                'store_id' => $request->store_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);
            
            // Add items to process
            foreach ($request->items as $item) {
                $process->items()->create([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);
                
                // Reduce inventory
                $inventory = \App\Models\Inventory::firstOrCreate([
                    'business_id' => session('business_id'),
                    'store_id' => $request->store_id,
                    'product_id' => $item['id'],
                ], [
                    'quantity' => 0,
                    'available_quantity' => 0,
                ]);
                
                if ($inventory->available_quantity < $item['quantity']) {
                    throw new \Exception('Insufficient quantity for ' . \App\Models\Product::find($item['id'])->name);
                }
                
                $inventory->available_quantity -= $item['quantity'];
                $inventory->quantity -= $item['quantity'];
                $inventory->save();
                
                // Record movement
                \App\Models\InventoryMovement::create([
                    'business_id' => session('business_id'),
                    'store_id' => $request->store_id,
                    'product_id' => $item['id'],
                    'type' => 'out',
                    'quantity' => -$item['quantity'],
                    'reference_type' => 'manufacturing_process',
                    'reference_id' => $process->id,
                    'notes' => 'Used in manufacturing process: ' . $request->name,
                    'created_by' => Auth::id(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('bakery.manufacturing.process')
                ->with('success', 'Manufacturing process created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating manufacturing process: ' . $e->getMessage());
        }
    }
}
