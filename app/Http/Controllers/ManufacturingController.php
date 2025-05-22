<?php

namespace App\Http\Controllers;

use App\Models\AssembledItem;
use App\Models\AssembledItemIngredient;
use App\Models\PasteDivision;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Category;
use App\Models\AssemblyCategory;
use App\Models\AssemblyGroup;
use App\Models\AssemblySize;
use App\Models\ManufacturingProcess;
use App\Models\ProductionPlan;
use App\Models\ProductionPlanMaterial;
use App\Models\ManufacturingWaste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ManufacturingController extends Controller
{
    /**
     * Constructor to ensure business_id is set
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // If business_id is not in session, try to get from user's company
            if (!session('business_id')) {
                $user = Auth::user();
                if ($user && $user->company && $user->company->business_id) {
                    session(['business_id' => $user->company->business_id]);
                    Log::info('Setting business_id in session from user company', [
                        'user_id' => $user->id,
                        'company_id' => $user->company->id,
                        'business_id' => $user->company->business_id
                    ]);
                }
            }
            
            return $next($request);
        });
    }

    /**
     * Inventory Assembly page
     * Used to create recipes and assemble products from inventory items
     */
    public function assembly()
    {
        // Ensure business_id and company_id are set in session for Blade rendering
        if (!session('company_id')) {
            $user = Auth::user();
            if ($user && $user->company) {
                session(['company_id' => $user->company->id]);
            }
        }
        if (!session('business_id')) {
            $user = Auth::user();
            // Try to get business from user's company
            if ($user && $user->company) {
                $business = \App\Models\Business::where('company_id', $user->company->id)->where('is_active', true)->first();
                if (!$business) {
                    $business = \App\Models\Business::where('company_id', $user->company->id)->first();
                }
                if ($business) {
                    session(['business_id' => $business->id]);
                }
            }
            // Fallback: get first business from user's enabled businesses
            if (!session('business_id') && $user && is_array($user->enabled_businesses) && count($user->enabled_businesses) > 0) {
                $business = \App\Models\Business::where('type', $user->enabled_businesses[0])->first();
                if ($business) {
                    session(['business_id' => $business->id]);
                }
            }
        }
        
        $assembledItems = AssembledItem::with(['assemblyCategory', 'assemblyGroup', 'assemblySize', 'ingredients.ingredient', 'ingredients.product', 'ingredients.referencedAssembledItem', 'pasteDivisions.outputAssembledItem'])
            ->where('business_id', session('business_id'))
            ->orderBy('created_at', 'desc')
            ->get();
            
        $ingredients = Ingredient::where('business_id', session('business_id'))
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        $products = Product::where('business_id', session('business_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $existingAssembledItems = AssembledItem::where('business_id', session('business_id'))
            ->orderBy('name')
            ->get();
            
        $assemblyCategories = AssemblyCategory::where('business_id', session('business_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $assemblyGroups = AssemblyGroup::where('business_id', session('business_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $assemblySizes = AssemblySize::where('business_id', session('business_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return view('manufacturing.assembly', compact(
            'assembledItems', 
            'ingredients', 
            'products', 
            'existingAssembledItems',
            'assemblyCategories',
            'assemblyGroups',
            'assemblySizes'
        ));
    }
    
    /**
     * Store a newly created assembled item
     */
    public function storeAssembledItem(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:single,paste',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'selling_price' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'assembly_category_id' => 'nullable',
            'assembly_group_id' => 'nullable',
            'assembly_size_id' => 'nullable',
        ]);
        
        // Use business_id from request or session
        $businessId = $request->input('business_id') ?? session('business_id');
        \Log::info('storeAssembledItem: using business_id', ['business_id' => $businessId, 'from' => $request->has('business_id') ? 'request' : 'session']);
        if (!$businessId) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business ID not found in session or request. Please contact your administrator.'
                ], 400);
            }
            return redirect()->back()
                ->with('error', 'Business ID not found in session or request. Please contact your administrator.')
                ->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            $assembledItem = AssembledItem::create([
                'business_id' => $businessId,
                'company_id' => Auth::user()->company->id ?? null,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'unit' => $validated['unit'],
                'selling_price' => $validated['selling_price'],
                'other_costs' => $validated['other_costs'],
                'total_cost' => $validated['other_costs'], // Will be updated after ingredients are added
                'assembly_category_id' => $validated['assembly_category_id'],
                'assembly_group_id' => $validated['assembly_group_id'],
                'assembly_size_id' => $validated['assembly_size_id'],
                'created_by' => Auth::id()
            ]);
            
            DB::commit();
            
            Log::info('Assembled item created successfully', [
                'item_id' => $assembledItem->id,
                'name' => $assembledItem->name,
                'business_id' => $businessId
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Assembled item created successfully.',
                    'item' => $assembledItem
                ]);
            }
            
            return redirect()->route('bakery.manufacturing.assembly')
                ->with('success', 'Assembled item created successfully.')
                ->with('assembled_item_id', $assembledItem->id);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create assembled item: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create assembled item: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to create assembled item: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update the specified assembled item
     */
    public function updateAssembledItem(Request $request, AssembledItem $assembledItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:single,paste',
            'unit' => 'required|string|max:50',
            'selling_price' => 'required|numeric|min:0',
            'other_costs' => 'required|numeric|min:0',
            'assembly_category_id' => 'nullable',
            'assembly_group_id' => 'nullable',
            'assembly_size_id' => 'nullable',
        ]);
        
        DB::beginTransaction();
        
        try {
            $assembledItem->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'unit' => $validated['unit'],
                'selling_price' => $validated['selling_price'],
                'other_costs' => $validated['other_costs'],
                'assembly_category_id' => $validated['assembly_category_id'],
                'assembly_group_id' => $validated['assembly_group_id'],
                'assembly_size_id' => $validated['assembly_size_id'],
            ]);
            
            $assembledItem->updateTotalCost();
            
            DB::commit();
            
            return redirect()->route('bakery.manufacturing.assembly')
                ->with('success', 'Assembled item updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update assembled item: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Remove the specified assembled item
     */
    public function destroyAssembledItem(AssembledItem $assembledItem)
    {
        try {
            $assembledItem->delete();
            
            return redirect()->route('bakery.manufacturing.assembly')
                ->with('success', 'Assembled item deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete assembled item: ' . $e->getMessage());
        }
    }
    
    /**
     * Add an ingredient to an assembled item
     */
    public function addIngredient(Request $request, AssembledItem $assembledItem)
    {
        $validated = $request->validate([
            'source_type' => 'required|string|in:ingredient,product,assembled_item',
            'source_id' => 'required|integer',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'required|string|max:50',
        ]);
        
        DB::beginTransaction();
        
        try {
            $cost = 0;
            
            // Set the appropriate foreign key based on source type
            switch ($validated['source_type']) {
                case 'ingredient':
                    $ingredient = Ingredient::findOrFail($validated['source_id']);
                    $cost = $ingredient->cost_price * $validated['quantity'];
                    $data = [
                        'ingredient_id' => $ingredient->id,
                        'product_id' => null,
                        'assembled_item_id_ref' => null
                    ];
                    break;
                    
                case 'product':
                    $product = Product::findOrFail($validated['source_id']);
                    $cost = $product->cost_price * $validated['quantity'];
                    $data = [
                        'ingredient_id' => null,
                        'product_id' => $product->id,
                        'assembled_item_id_ref' => null
                    ];
                    break;
                    
                case 'assembled_item':
                    $referencedAssembledItem = AssembledItem::findOrFail($validated['source_id']);
                    $cost = $referencedAssembledItem->total_cost * $validated['quantity'];
                    $data = [
                        'ingredient_id' => null,
                        'product_id' => null,
                        'assembled_item_id_ref' => $referencedAssembledItem->id
                    ];
                    break;
            }
            
            // Create the ingredient record
            $ingredient = AssembledItemIngredient::create(array_merge([
                'assembled_item_id' => $assembledItem->id,
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'cost' => $cost
            ], $data));
            
            // Update the total cost of the assembled item
            $assembledItem->updateTotalCost();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Ingredient added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->with('error', 'Failed to add ingredient: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Remove an ingredient from an assembled item
     */
    public function removeIngredient(AssembledItemIngredient $ingredient)
    {
        DB::beginTransaction();
        
        try {
            $assembledItem = $ingredient->assembledItem;
            $ingredient->delete();
            
            // Update the total cost of the assembled item
            $assembledItem->updateTotalCost();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Ingredient removed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->with('error', 'Failed to remove ingredient: ' . $e->getMessage());
        }
    }
    
    /**
     * Create a paste division
     */
    public function createPasteDivision(Request $request, AssembledItem $assembledItem)
    {
        // Validate that this is a paste type item
        if ($assembledItem->type !== AssembledItem::TYPE_PASTE) {
            return redirect()->back()->with('error', 'Only paste items can be divided.');
        }
        
        $validated = $request->validate([
            'output_assembled_item_id' => 'nullable|exists:assembled_items,id',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'required|string|max:50',
            'flavor' => 'nullable|string|max:100',
            'flavor_quantity' => 'nullable|numeric|min:0',
            'flavor_unit' => 'nullable|string|max:50',
            'flavor_cost' => 'nullable|numeric|min:0',
            'waste_quantity' => 'nullable|numeric|min:0',
        ]);
        
        try {
            PasteDivision::create([
                'assembled_item_id' => $assembledItem->id,
                'output_assembled_item_id' => $validated['output_assembled_item_id'],
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'flavor' => $validated['flavor'],
                'flavor_quantity' => $validated['flavor_quantity'],
                'flavor_unit' => $validated['flavor_unit'],
                'flavor_cost' => $validated['flavor_cost'],
                'waste_quantity' => $validated['waste_quantity'],
            ]);
            
            return redirect()->back()->with('success', 'Paste division created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create paste division: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Manufacture process page
     */
    public function process()
    {
        $assembledItems = AssembledItem::with(['ingredients.ingredient', 'ingredients.product', 'ingredients.referencedAssembledItem'])
            ->where('business_id', session('business_id'))
            ->orderBy('created_at', 'desc')
            ->get();
            
        $ingredients = Ingredient::where('business_id', session('business_id'))
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        $products = Product::where('business_id', session('business_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        // Get assembly categories, groups, and sizes for the current business
        $assemblyCategories = \App\Models\AssemblyCategory::where('business_id', session('business_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $assemblyGroups = \App\Models\AssemblyGroup::where('business_id', session('business_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $assemblySizes = \App\Models\AssemblySize::where('business_id', session('business_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $existingAssembledItems = AssembledItem::where('business_id', session('business_id'))
            ->orderBy('name')
            ->get();
            
        // Get in progress and completed manufacturing processes
        $inProgressProcesses = \App\Models\ManufacturingProcess::with('assembledItem')
            ->where('business_id', session('business_id'))
            ->where('status', 'in_progress')
            ->orderBy('scheduled_date', 'asc')
            ->get();
            
        $completedProcesses = \App\Models\ManufacturingProcess::with('assembledItem')
            ->where('business_id', session('business_id'))
            ->where('status', 'completed')
            ->orderByDesc('completed_date')
            ->limit(20) // Limit to the most recent 20 completed processes
            ->get();
            
        return view('manufacturing.process', compact(
            'assembledItems', 
            'ingredients', 
            'products', 
            'existingAssembledItems',
            'inProgressProcesses',
            'completedProcesses',
            'assemblyCategories',
            'assemblyGroups',
            'assemblySizes'
        ));
    }
    
    /**
     * Store a new manufacturing process
     */
    public function storeProcess(Request $request)
    {
        $validated = $request->validate([
            'assembled_item_id' => 'required|exists:assembled_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'scheduled_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:in_progress,completed',
        ]);
        
        DB::beginTransaction();
        
        try {
            $status = ($request->has('status') && $request->status === 'completed') ? 'completed' : 'in_progress';
            $completedDate = ($status === 'completed') ? now() : null;
            
            // Generate batch number
            $batchPrefix = 'M-' . date('Ymd');
            $lastBatch = \App\Models\ManufacturingProcess::where('batch_number', 'like', $batchPrefix . '%')
                ->orderByDesc('id')
                ->first();
                
            $batchNumber = $lastBatch 
                ? $batchPrefix . '-' . sprintf('%03d', (int)substr($lastBatch->batch_number, -3) + 1)
                : $batchPrefix . '-001';
                
            // Create manufacturing process
            $process = \App\Models\ManufacturingProcess::create([
                'business_id' => session('business_id'),
                'company_id' => session('company_id'),
                'assembled_item_id' => $validated['assembled_item_id'],
                'batch_number' => $batchNumber,
                'quantity' => $validated['quantity'],
                'scheduled_date' => $validated['scheduled_date'],
                'completed_date' => $completedDate,
                'notes' => $validated['notes'],
                'status' => $status,
                'created_by' => Auth::id()
            ]);
            
            // Get assembled item
            $assembledItem = AssembledItem::findOrFail($validated['assembled_item_id']);
            
            // Add ingredients to manufacturing process
            foreach ($assembledItem->ingredients as $ingredient) {
                // Calculate the quantity needed based on manufacturing quantity
                $requiredQuantity = $ingredient->quantity * $validated['quantity'];
                
                \App\Models\ManufacturingProcessIngredient::create([
                    'manufacturing_process_id' => $process->id,
                    'source_type' => $ingredient->source_type,
                    'ingredient_id' => $ingredient->ingredient_id,
                    'product_id' => $ingredient->product_id,
                    'assembled_item_id_ref' => $ingredient->assembled_item_id_ref,
                    'quantity' => $requiredQuantity,
                    'unit' => $ingredient->unit,
                    'cost' => $ingredient->cost * $validated['quantity'],
                    'used_quantity' => $status === 'completed' ? $requiredQuantity : 0,
                ]);
            }
            
            // If completed, subtract ingredients from inventory
            if ($status === 'completed') {
                // Logic for subtracting ingredients from inventory
                // This would involve updating the Inventory model for each ingredient
                // (Implementation depends on your inventory tracking system)
            }
            
            DB::commit();
            
            return redirect()->route('bakery.manufacturing.process')
                ->with('success', 'Manufacturing process created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->with('error', 'Failed to create manufacturing process: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Complete a manufacturing process
     */
    public function completeProcess(Request $request, $processId)
    {
        DB::beginTransaction();
        
        try {
            $process = \App\Models\ManufacturingProcess::findOrFail($processId);
            
            // Check if already completed
            if ($process->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Process already completed.'
                ]);
            }
            
            // Update process status
            $process->status = 'completed';
            $process->completed_date = now();
            $process->save();
            
            // Mark all ingredients as used
            foreach ($process->ingredients as $ingredient) {
                $ingredient->used_quantity = $ingredient->quantity;
                $ingredient->save();
                
                // Subtract from inventory
                // (Implementation depends on your inventory tracking system)
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Manufacturing process completed successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete manufacturing process: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Complete all manufacturing processes
     */
    public function completeAllProcesses(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $processes = \App\Models\ManufacturingProcess::where('business_id', session('business_id'))
                ->where('status', 'in_progress')
                ->get();
                
            foreach ($processes as $process) {
                // Update process status
                $process->status = 'completed';
                $process->completed_date = now();
                $process->save();
                
                // Mark all ingredients as used
                foreach ($process->ingredients as $ingredient) {
                    $ingredient->used_quantity = $ingredient->quantity;
                    $ingredient->save();
                    
                    // Subtract from inventory
                    // (Implementation depends on your inventory tracking system)
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'All manufacturing processes completed successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete manufacturing processes: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update manufacturing process ingredient
     */
    public function updateProcessIngredient(Request $request, $ingredientId)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);
        
        try {
            $ingredient = \App\Models\ManufacturingProcessIngredient::findOrFail($ingredientId);
            $ingredient->quantity = $validated['quantity'];
            $ingredient->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Ingredient quantity updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ingredient quantity: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Record waste for a manufacturing process ingredient
     */
    public function recordWaste(Request $request, $processId)
    {
        $validated = $request->validate([
            'manufacturing_process_ingredient_id' => 'required|exists:manufacturing_process_ingredients,id',
            'waste_amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
        ]);
        
        try {
            $waste = \App\Models\ManufacturingWaste::create([
                'manufacturing_process_id' => $processId,
                'manufacturing_process_ingredient_id' => $validated['manufacturing_process_ingredient_id'],
                'quantity' => $validated['waste_amount'],
                'reason' => $validated['reason'],
                'recorded_by' => Auth::id()
            ]);
            
            $ingredient = \App\Models\ManufacturingProcessIngredient::findOrFail($validated['manufacturing_process_ingredient_id']);
            $sourceName = 'Unknown';
            
            if ($ingredient->ingredient_id) {
                $sourceName = $ingredient->ingredient->name;
            } elseif ($ingredient->product_id) {
                $sourceName = $ingredient->product->name;
            } elseif ($ingredient->assembled_item_id_ref) {
                $sourceName = $ingredient->assembledItem->name;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Waste recorded successfully.',
                'waste' => [
                    'source_name' => $sourceName,
                    'batch' => $ingredient->manufacturingProcess->batch_number,
                    'date' => $waste->created_at->format('Y-m-d'),
                    'used_amount' => $ingredient->quantity . ' ' . $ingredient->unit,
                    'waste_amount' => $validated['waste_amount'] . ' ' . $ingredient->unit,
                    'waste_percentage' => number_format(($validated['waste_amount'] / $ingredient->quantity) * 100, 1),
                    'reason' => $validated['reason']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record waste: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Stock Adjustment for manufacturing
     * Used to adjust stock after manufacturing process
     */
    public function adjustment()
    {
        return view('manufacturing.adjustment');
    }
    
    /**
     * Production Planning page
     */
    public function planning()
    {
        $assembledItems = AssembledItem::where('business_id', session('business_id'))
            ->orderBy('name')
            ->get();
            
        $productionPlans = ProductionPlan::with(['assembledItem', 'creator'])
            ->where('business_id', session('business_id'))
            ->orderBy('scheduled_date')
            ->get();
            
        $upcomingPlans = ProductionPlan::with(['assembledItem'])
            ->where('business_id', session('business_id'))
            ->where('status', 'planned')
            ->where('scheduled_date', '>=', now()->format('Y-m-d'))
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->take(10)
            ->get();
            
        // Get resource allocation data
        $resources = [
            ['name' => 'Mixing Station 1', 'today' => 75, 'tomorrow' => 90, 'capacity' => 75, 'status' => 'Available'],
            ['name' => 'Mixing Station 2', 'today' => 100, 'tomorrow' => 80, 'capacity' => 100, 'status' => 'Fully Booked'],
            ['name' => 'Oven 1', 'today' => 60, 'tomorrow' => 85, 'capacity' => 60, 'status' => 'Available'],
            ['name' => 'Oven 2', 'today' => 90, 'tomorrow' => 40, 'capacity' => 90, 'status' => 'Limited'],
            ['name' => 'Decoration Station', 'today' => 50, 'tomorrow' => 65, 'capacity' => 50, 'status' => 'Available'],
        ];
        
        // Get required materials for today and tomorrow
        $todayMaterials = $this->calculateRequiredMaterials(now()->format('Y-m-d'));
        $tomorrowMaterials = $this->calculateRequiredMaterials(now()->addDay()->format('Y-m-d'));
        $weekMaterials = $this->calculateRequiredMaterials(now()->format('Y-m-d'), now()->addDays(7)->format('Y-m-d'));
        
        return view('manufacturing.planning', compact(
            'assembledItems',
            'productionPlans',
            'upcomingPlans',
            'resources',
            'todayMaterials',
            'tomorrowMaterials',
            'weekMaterials'
        ));
    }
    
    /**
     * Calculate required materials for production plans in a date range
     */
    private function calculateRequiredMaterials($startDate, $endDate = null)
    {
        $query = ProductionPlan::with(['assembledItem.ingredients.ingredient', 'assembledItem.ingredients.product', 'assembledItem.ingredients.referencedAssembledItem'])
            ->where('business_id', session('business_id'))
            ->where('status', 'planned');
        
        if ($endDate) {
            $query->whereBetween('scheduled_date', [$startDate, $endDate]);
        } else {
            $query->where('scheduled_date', $startDate);
        }
        
        $plans = $query->get();
        
        $materials = [];
        
        foreach ($plans as $plan) {
            if (!$plan->assembledItem || !$plan->assembledItem->ingredients) {
                continue;
            }
            
            foreach ($plan->assembledItem->ingredients as $ingredient) {
                $sourceItem = null;
                $sourceType = null;
            
            if ($ingredient->ingredient_id) {
                    $sourceItem = $ingredient->ingredient;
                    $sourceType = 'ingredient';
            } elseif ($ingredient->product_id) {
                    $sourceItem = $ingredient->product;
                    $sourceType = 'product';
            } elseif ($ingredient->assembled_item_id_ref) {
                    $sourceItem = $ingredient->referencedAssembledItem;
                    $sourceType = 'assembled_item';
            }
            
                if (!$sourceItem) {
                    continue;
                }
                
                $requiredQuantity = $ingredient->quantity * $plan->quantity;
                $itemId = $sourceType . '_' . $sourceItem->id;
                
                if (!isset($materials[$itemId])) {
                    $materials[$itemId] = [
                        'id' => $sourceItem->id,
                        'name' => $sourceItem->name,
                        'type' => $sourceType,
                        'required' => $requiredQuantity,
                'unit' => $ingredient->unit,
                        'in_stock' => $sourceItem->quantity ?? 0,
                        'status' => ($sourceItem->quantity ?? 0) >= $requiredQuantity ? 'Available' : 'Insufficient'
                    ];
                } else {
                    $materials[$itemId]['required'] += $requiredQuantity;
                    $materials[$itemId]['status'] = ($sourceItem->quantity ?? 0) >= $materials[$itemId]['required'] ? 'Available' : 'Insufficient';
                }
            }
        }
        
        return array_values($materials);
    }

    /**
     * Store a new production plan
     */
    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'assembled_item_id' => 'required|exists:assembled_items,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'priority' => 'required|in:high,normal,low',
            'resource_allocation' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);
        
        try {
            $plan = ProductionPlan::create([
                'business_id' => session('business_id'),
                'company_id' => session('company_id'),
                'assembled_item_id' => $validated['assembled_item_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'scheduled_date' => $validated['scheduled_date'],
                'scheduled_time' => $validated['scheduled_time'],
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'priority' => $validated['priority'],
                'status' => 'planned',
                'resource_allocation' => $validated['resource_allocation'],
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            
            // Calculate and create material requirements
            $this->createPlanMaterialRequirements($plan);
            
            return redirect()->route('bakery.manufacturing.planning')
                ->with('success', 'Production plan scheduled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to schedule production plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Create material requirements for a production plan
     */
    private function createPlanMaterialRequirements(ProductionPlan $plan)
    {
        $assembledItem = $plan->assembledItem;
        
        if (!$assembledItem || !$assembledItem->ingredients) {
            return;
        }
        
        foreach ($assembledItem->ingredients as $ingredient) {
            $sourceType = null;
            $sourceId = null;
            
            if ($ingredient->ingredient_id) {
                $sourceType = 'ingredient';
                $sourceId = $ingredient->ingredient_id;
            } elseif ($ingredient->product_id) {
                $sourceType = 'product';
                $sourceId = $ingredient->product_id;
            } elseif ($ingredient->assembled_item_id_ref) {
                $sourceType = 'assembled_item';
                $sourceId = $ingredient->assembled_item_id_ref;
            }
            
            if (!$sourceType || !$sourceId) {
                continue;
            }
            
            ProductionPlanMaterial::create([
                'production_plan_id' => $plan->id,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'quantity' => $ingredient->quantity * $plan->quantity,
                'unit' => $ingredient->unit,
                'status' => 'required'
            ]);
        }
    }
    
    /**
     * Update a production plan status
     */
    public function updatePlanStatus(Request $request, ProductionPlan $productionPlan)
    {   
        $validated = $request->validate([
            'status' => 'required|in:planned,in_progress,completed,cancelled',
        ]);
        
        try {
            $productionPlan->update([
                'status' => $validated['status'],
                'updated_by' => Auth::id(),
            ]);
            
            return redirect()->route('bakery.manufacturing.planning')
                ->with('success', 'Production plan status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update production plan status: ' . $e->getMessage());
        }
    }
    
    /**
     * Waste Management page
     */
    public function waste()
    {
        $assembledItems = AssembledItem::where('business_id', session('business_id'))
            ->orderBy('name')
            ->get();
            
        $query = ManufacturingWaste::with(['manufacturingProcess.assembledItem', 'recordedBy'])
            ->where('business_id', session('business_id'));
            
        // Apply filters if they exist
        if (request()->has('date_from') && request('date_from')) {
            $query->where('recorded_at', '>=', request('date_from') . ' 00:00:00');
        }
        
        if (request()->has('date_to') && request('date_to')) {
            $query->where('recorded_at', '<=', request('date_to') . ' 23:59:59');
        }
        
        if (request()->has('assembled_item_id') && request('assembled_item_id')) {
            $query->whereHas('manufacturingProcess', function ($q) {
                $q->where('assembled_item_id', request('assembled_item_id'));
            });
        }
        
        if (request()->has('ingredient_type') && request('ingredient_type')) {
            $query->where('source_type', request('ingredient_type'));
        }
        
        if (request()->has('min_waste_percentage') && request('min_waste_percentage')) {
            $query->where('waste_percentage', '>=', request('min_waste_percentage'));
        }
        
        $wasteRecords = $query->orderBy('recorded_at', 'desc')
            ->paginate(20);
            
        // Calculate waste statistics
        $wasteCount = $query->count();
        $averageWastePercentage = $query->avg('waste_percentage') ?? 0;
        $totalWasteValue = $query->sum('waste_value') ?? 0;
        
        // Find the highest waste ingredient
        $highestWaste = ManufacturingWaste::selectRaw('source_name, SUM(waste_value) as total_waste')
            ->where('business_id', session('business_id'))
            ->groupBy('source_name')
            ->orderByDesc('total_waste')
            ->first();
            
        $highestWasteIngredient = $highestWaste ? $highestWaste->source_name : 'None';
        
        // Prepare chart data for waste trends
        $wasteChartData = $this->getWasteChartData();
        
        return view('manufacturing.waste', compact(
            'wasteRecords',
            'assembledItems',
            'wasteCount',
            'averageWastePercentage',
            'totalWasteValue',
            'highestWasteIngredient',
            'wasteChartData'
        ));
    }

    /**
     * Get waste chart data for the last 30 days
     */
    private function getWasteChartData()
    {
        $startDate = now()->subDays(29)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        $wasteData = ManufacturingWaste::selectRaw('DATE(recorded_at) as date, AVG(waste_percentage) as avg_percentage')
            ->where('business_id', session('business_id'))
            ->whereBetween('recorded_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $labels = [];
        $percentages = [];
        
        // Create a date range for the last 30 days
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            new \DateTime($endDate . ' +1 day')
        );
        
        // Initialize with zeros for all dates
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            $percentages[$dateStr] = 0;
        }
        
        // Fill in actual data where it exists
        foreach ($wasteData as $item) {
            $percentages[$item->date] = round($item->avg_percentage, 1);
        }
        
        return [
            'labels' => $labels,
            'percentages' => array_values($percentages)
        ];
    }

    /**
     * Efficiency Metrics page
     */
    public function metrics()
    {
        $assembledItems = AssembledItem::where('business_id', session('business_id'))
            ->orderBy('name')
            ->get();
            
        // Apply date filters if they exist
        $startDate = request('date_from') ? request('date_from') : now()->subDays(30)->format('Y-m-d');
        $endDate = request('date_to') ? request('date_to') : now()->format('Y-m-d');
        
        // Get metrics from the database or calculate them
        $metrics = $this->getOrCalculateMetrics($startDate, $endDate);
        
        // Get product efficiency data
        $productEfficiency = $this->getProductEfficiencyData($startDate, $endDate);
        
        // Get ingredient efficiency data
        $ingredientEfficiency = $this->getIngredientEfficiencyData($startDate, $endDate);
        
        // Prepare chart data for efficiency trends
        $efficiencyChartData = $this->getEfficiencyChartData($startDate, $endDate);
        
        return view('manufacturing.metrics', compact(
            'assembledItems',
            'metrics',
            'productEfficiency',
            'ingredientEfficiency',
            'efficiencyChartData',
            'totalRuns' => $metrics['totalRuns'] ?? 0,
            'averageWaste' => $metrics['averageWaste'] ?? 0,
            'productionEfficiency' => $metrics['productionEfficiency'] ?? 0,
            'mostEfficientProduct' => $metrics['mostEfficientProduct'] ?? 'None'
        ));
    }

    /**
     * Get or calculate metrics for the given date range
     */
    private function getOrCalculateMetrics($startDate, $endDate)
    {
        // Get completed manufacturing processes in the date range
        $processes = ManufacturingProcess::where('business_id', session('business_id'))
            ->where('status', 'completed')
            ->whereBetween('completed_date', [$startDate, $endDate])
            ->get();
        
        $totalRuns = $processes->count();
        $totalWaste = 0;
        $totalEfficiency = 0;
        $productEfficiency = [];
        
        foreach ($processes as $process) {
            // Calculate waste percentage
            $wastes = ManufacturingWaste::where('manufacturing_process_id', $process->id)->get();
            $processWastePercentage = $wastes->avg('waste_percentage') ?? 0;
            $totalWaste += $processWastePercentage;
            
            // Calculate time efficiency
            $actualTime = $process->actual_time ?? 0;
            $expectedTime = $process->expected_time ?? 0;
            $timeEfficiency = $expectedTime > 0 ? min(100, ($expectedTime / max(1, $actualTime)) * 100) : 0;
            $totalEfficiency += $timeEfficiency;
            
            // Track product efficiency
            if ($process->assembled_item_id) {
                if (!isset($productEfficiency[$process->assembled_item_id])) {
                    $productEfficiency[$process->assembled_item_id] = [
                        'name' => $process->assembledItem->name ?? 'Unknown',
                        'runs' => 1,
                        'efficiency' => $timeEfficiency,
                        'waste' => $processWastePercentage
                    ];
                } else {
                    $productEfficiency[$process->assembled_item_id]['runs']++;
                    $productEfficiency[$process->assembled_item_id]['efficiency'] += $timeEfficiency;
                    $productEfficiency[$process->assembled_item_id]['waste'] += $processWastePercentage;
                }
            }
        }
        
        // Calculate averages
        $averageWaste = $totalRuns > 0 ? $totalWaste / $totalRuns : 0;
        $averageEfficiency = $totalRuns > 0 ? $totalEfficiency / $totalRuns : 0;
        
        // Find most efficient product
        $mostEfficientProduct = 'None';
        $highestEfficiency = 0;
        
        foreach ($productEfficiency as $id => $data) {
            $avgEfficiency = $data['runs'] > 0 ? $data['efficiency'] / $data['runs'] : 0;
            if ($avgEfficiency > $highestEfficiency) {
                $highestEfficiency = $avgEfficiency;
                $mostEfficientProduct = $data['name'];
            }
        }
        
        return [
            'totalRuns' => $totalRuns,
            'averageWaste' => $averageWaste,
            'productionEfficiency' => $averageEfficiency,
            'mostEfficientProduct' => $mostEfficientProduct
        ];
    }

    /**
     * Get product efficiency data for the given date range
     */
    private function getProductEfficiencyData($startDate, $endDate)
    {
        $productData = [];
        
        // Get completed manufacturing processes grouped by product
        $processes = ManufacturingProcess::with('assembledItem')
            ->where('business_id', session('business_id'))
            ->where('status', 'completed')
            ->whereBetween('completed_date', [$startDate, $endDate])
            ->get()
            ->groupBy('assembled_item_id');
            
        foreach ($processes as $itemId => $itemProcesses) {
            if (!$itemId || !$itemProcesses->first()->assembledItem) {
                continue;
            }
            
            $assembledItem = $itemProcesses->first()->assembledItem;
            $runs = $itemProcesses->count();
            $totalTime = $itemProcesses->sum('actual_time');
            $avgTime = $runs > 0 ? $totalTime / $runs : 0;
            
            // Calculate efficiency
            $totalEfficiency = 0;
            foreach ($itemProcesses as $process) {
                $actualTime = $process->actual_time ?? 0;
                $expectedTime = $process->expected_time ?? 0;
                $efficiency = $expectedTime > 0 ? min(100, ($expectedTime / max(1, $actualTime)) * 100) : 0;
                $totalEfficiency += $efficiency;
            }
            $avgEfficiency = $runs > 0 ? $totalEfficiency / $runs : 0;
            
            // Calculate trend (compare with previous period)
            $previousStartDate = date('Y-m-d', strtotime($startDate . ' -' . (strtotime($endDate) - strtotime($startDate)) . ' seconds'));
            $previousEndDate = date('Y-m-d', strtotime($endDate . ' -' . (strtotime($endDate) - strtotime($startDate)) . ' seconds'));
            
            $previousProcesses = ManufacturingProcess::where('business_id', session('business_id'))
                ->where('assembled_item_id', $itemId)
            ->where('status', 'completed')
                ->whereBetween('completed_date', [$previousStartDate, $previousEndDate])
                ->get();
                
            $previousTotalEfficiency = 0;
            foreach ($previousProcesses as $process) {
                $actualTime = $process->actual_time ?? 0;
                $expectedTime = $process->expected_time ?? 0;
                $efficiency = $expectedTime > 0 ? min(100, ($expectedTime / max(1, $actualTime)) * 100) : 0;
                $previousTotalEfficiency += $efficiency;
            }
            $previousAvgEfficiency = $previousProcesses->count() > 0 ? $previousTotalEfficiency / $previousProcesses->count() : 0;
            
            $trend = $previousAvgEfficiency > 0 ? (($avgEfficiency - $previousAvgEfficiency) / $previousAvgEfficiency) * 100 : 0;
            
            $productData[] = (object)[
                'name' => $assembledItem->name,
                'runs' => $runs,
                'avg_time' => round($avgTime, 1),
                'efficiency' => round($avgEfficiency, 1),
                'trend' => round($trend, 1)
            ];
        }
        
        // Sort by efficiency (descending)
        usort($productData, function($a, $b) {
            return $b->efficiency <=> $a->efficiency;
        });
        
        return $productData;
    }

    /**
     * Get ingredient efficiency data for the given date range
     */
    private function getIngredientEfficiencyData($startDate, $endDate)
    {
        $ingredientData = [];
        
        // Get waste records grouped by ingredient
        $wastes = ManufacturingWaste::where('business_id', session('business_id'))
            ->whereBetween('recorded_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get()
            ->groupBy('source_name');
            
        foreach ($wastes as $sourceName => $sourceWastes) {
            $totalUsed = $sourceWastes->sum('used_amount');
            $totalWaste = $sourceWastes->sum('waste_amount');
            $wastePercentage = $totalUsed > 0 ? ($totalWaste / $totalUsed) * 100 : 0;
            $costImpact = $sourceWastes->sum('waste_value');
            $unit = $sourceWastes->first()->unit;
            
            $ingredientData[] = (object)[
                'name' => $sourceName,
                'total_used' => round($totalUsed, 2),
                'unit' => $unit,
                'waste_percentage' => round($wastePercentage, 1),
                'cost_impact' => round($costImpact, 2)
            ];
        }
        
        // Sort by waste percentage (descending)
        usort($ingredientData, function($a, $b) {
            return $b->waste_percentage <=> $a->waste_percentage;
        });
        
        return $ingredientData;
    }

    /**
     * Get efficiency chart data for the given date range
     */
            ->firstOrFail();
        
        // Check if there's enough paste available
        $totalPasteQty = $process->output_quantity ?? 0;
        $usedQty = \App\Models\PasteDivision::where('paste_process_id', $process->id)
            ->sum('paste_quantity');
        $remainingQty = $totalPasteQty - $usedQty;
        
        if ($validated['paste_quantity'] > $remainingQty) {
            return response()->json([
                'success' => false,
                'message' => "Not enough paste available. Available: {$remainingQty} kg"
            ], 422);
        }
        
        // Create or get the output assembled item
        if ($request->has('is_existing_item') && $request->boolean('is_existing_item')) {
            // Use existing assembled item
            $outputItemId = $validated['output_assembled_item_id'];
            $outputItem = \App\Models\AssembledItem::where('id', $outputItemId)
                ->where('business_id', $businessId)
                ->firstOrFail();
        } else {
            // Create new assembled item for the output
            $outputItem = new \App\Models\AssembledItem([
                'business_id' => $businessId,
                'name' => $validated['output_name'],
                'type' => $validated['output_type'],
                'unit' => $validated['output_unit'],
                'total_cost' => 0, // Will be calculated later
                'is_active' => true
            ]);
            $outputItem->save();
        }
        
        // Create the paste division record
        $pasteDivision = new \App\Models\PasteDivision([
            'business_id' => $businessId,
            'paste_process_id' => $process->id,
            'output_item_id' => $outputItem->id,
            'paste_quantity' => $validated['paste_quantity'],
            'output_quantity' => $validated['output_quantity'],
            'output_unit' => $validated['output_unit'],
            'flavor' => $validated['flavor'] ?? null,
            'flavor_quantity' => $validated['flavor_quantity'] ?? null,
            'flavor_cost' => $validated['flavor_cost'] ?? 0,
            'notes' => $validated['division_notes'] ?? null,
            'created_by' => auth()->id()
        ]);
        
        $pasteDivision->save();
        
        // Create inventory entry for the output item
        // This would involve crediting the inventory with the new output item
        
        return response()->json([
            'success' => true,
            'message' => 'Paste has been successfully divided',
            'paste_division' => $pasteDivision
        ]);
    }
} 