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
     * Production Planning
     * Used to schedule and plan manufacturing activities
     */
    public function planning()
    {
        return view('manufacturing.planning');
    }
    
    /**
     * Waste Management
     * Used to track and manage waste from manufacturing
     */
    public function waste()
    {
        return view('manufacturing.waste');
    }
    
    /**
     * Efficiency Metrics
     * Used to track and display manufacturing efficiency
     */
    public function metrics()
    {
        return view('manufacturing.metrics');
    }

    public function ingredientForm()
    {
        return view('manufacturing.partials.ingredient_form');
    }
    
    /**
     * Get assembled item details
     */
    public function getAssembledItem(AssembledItem $assembledItem)
    {
        return response()->json($assembledItem);
    }
    
    /**
     * Get ingredients for an assembled item
     */
    public function getIngredients(AssembledItem $assembledItem)
    {
        $ingredients = $assembledItem->ingredients->map(function ($ingredient) {
            $sourceName = '';
            $sourceType = '';
            
            if ($ingredient->ingredient_id) {
                $sourceName = $ingredient->ingredient->name;
                $sourceType = 'Raw Ingredient';
            } elseif ($ingredient->product_id) {
                $sourceName = $ingredient->product->name;
                $sourceType = 'Product';
            } elseif ($ingredient->assembled_item_id_ref) {
                $sourceName = $ingredient->referencedAssembledItem->name;
                $sourceType = 'Assembled Item';
            }
            
            return [
                'id' => $ingredient->id,
                'source_name' => $sourceName,
                'source_type' => $sourceType,
                'quantity' => $ingredient->quantity,
                'unit' => $ingredient->unit,
                'cost' => $ingredient->cost
            ];
        });
        
        return response()->json($ingredients);
    }
    
    /**
     * Get paste divisions for an assembled item
     */
    public function getPasteDivisions(AssembledItem $assembledItem)
    {
        $divisions = $assembledItem->pasteDivisions()->with('outputItem')->get();
        
        return response()->json($divisions);
    }

    /**
     * Store a new category via AJAX
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);
        
        try {
            // Use business_id from request or session
            $businessId = $request->input('business_id') ?? session('business_id');
            \Log::info('storeCategory: using business_id', ['business_id' => $businessId, 'from' => $request->has('business_id') ? 'request' : 'session']);
            if (!$businessId) {
                Log::error('No business ID found in session or request', [
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No active business found. Please contact your administrator.'
                ], 400);
            }
            $category = AssemblyCategory::create([
                'business_id' => $businessId,
                'company_id' => Auth::user()->company->id ?? null,
                'name' => $validated['name'],
                'is_active' => true
            ]);
            Log::info('Category created successfully', [
                'category_id' => $category->id,
                'name' => $category->name,
                'business_id' => $businessId
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create category: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a new group via AJAX
     */
    public function storeGroup(Request $request)
    {   
        \Log::info('storeGroup called', [
            'session' => session()->all(),
            'user_id' => Auth::id(),
            'request' => $request->all()
        ]);
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);
        try {
            // Use business_id from request or session
            $businessId = $request->input('business_id') ?? session('business_id');
            \Log::info('storeGroup: using business_id', ['business_id' => $businessId, 'from' => $request->has('business_id') ? 'request' : 'session']);
            if (!$businessId) {
                Log::error('No business ID found in session or request', [
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No active business found. Please contact your administrator.'
                ], 400);
            }
            $group = AssemblyGroup::create([
                'business_id' => $businessId,
                'company_id' => Auth::user()->company->id ?? null,
                'name' => $validated['name'],
                'is_active' => true
            ]);
            Log::info('Group created successfully', [
                'group_id' => $group->id,
                'name' => $group->name,
                'business_id' => $businessId
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Group created successfully!',
                'group' => $group
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create group: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create group: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a new size via AJAX
     */
    public function storeSize(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);
        try {
            // Use business_id from request or session
            $businessId = $request->input('business_id') ?? session('business_id');
            \Log::info('storeSize: using business_id', ['business_id' => $businessId, 'from' => $request->has('business_id') ? 'request' : 'session']);
            if (!$businessId) {
                Log::error('No business ID found in session or request', [
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No active business found. Please contact your administrator.'
                ], 400);
            }
            $size = AssemblySize::create([
                'business_id' => $businessId,
                'company_id' => Auth::user()->company->id ?? null,
                'name' => $validated['name'],
                'is_active' => true
            ]);
            Log::info('Size created successfully', [
                'size_id' => $size->id,
                'name' => $size->name,
                'business_id' => $businessId
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Size created successfully!',
                'size' => $size
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create size: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create size: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Check if assembled item name is unique
     */
    public function checkNameUnique(Request $request)
    {
        $name = $request->input('name');
        $id = $request->input('id');
        $query = \App\Models\AssembledItem::where('business_id', session('business_id'))
            ->where('name', $name);
        if ($id) {
            $query->where('id', '!=', $id);
        }
        $exists = $query->exists();
        return response()->json(['unique' => !$exists]);
    }

    /**
     * Get the available quantity of paste for division
     */
    public function getPasteAvailability($processId)
    {
        $businessId = session('business_id');
        $process = \App\Models\ManufacturingProcess::where('business_id', $businessId)
            ->where('id', $processId)
            ->where('status', 'completed')
            ->firstOrFail();
        
        $totalPasteQty = $process->output_quantity ?? 0;
        $usedQty = \App\Models\PasteDivision::where('paste_process_id', $processId)
            ->sum('paste_quantity');
        
        $availableQty = $totalPasteQty - $usedQty;
        
        return response()->json([
            'success' => true,
            'total_qty' => $totalPasteQty,
            'used_qty' => $usedQty,
            'available_qty' => $availableQty
        ]);
    }

    /**
     * Divide a paste into portions
     */
    public function dividePaste(Request $request)
    {
        $businessId = session('business_id');
        
        $validated = $request->validate([
            'paste_id' => 'required|exists:manufacturing_processes,id',
            'paste_name' => 'required|string',
            'paste_quantity' => 'required|numeric|min:0.01',
            'output_quantity' => 'required|numeric|min:1',
            'output_unit' => 'required|string',
            'is_existing_item' => 'sometimes|boolean',
            'output_assembled_item_id' => 'nullable|exists:assembled_items,id',
            'output_name' => 'nullable|string|max:255',
            'output_type' => 'nullable|string|in:single,box',
            'flavor' => 'nullable|string',
            'flavor_quantity' => 'nullable|numeric',
            'flavor_cost' => 'nullable|numeric',
            'division_notes' => 'nullable|string'
        ]);
        
        // Get the process
        $process = \App\Models\ManufacturingProcess::where('id', $validated['paste_id'])
            ->where('business_id', $businessId)
            ->where('status', 'completed')
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