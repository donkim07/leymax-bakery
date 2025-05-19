<?php

namespace App\Http\Controllers;

use App\Models\AssembledItem;
use App\Models\AssembledItemIngredient;
use App\Models\PasteDivision;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\AssemblyCategory;
use App\Models\AssemblyGroup;
use App\Models\AssemblySize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssemblyController extends Controller
{
    /**
     * Display a listing of assembled items
     */
    public function index()
    {
        $assembledItems = AssembledItem::with(['ingredients.ingredient', 'ingredients.product', 'ingredients.referencedAssembledItem', 'pasteDivisions.outputAssembledItem'])
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('bakery.manufacturing.assembly');
    }

    /**
     * Store a newly created assembled item
     */
    public function store(Request $request)
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
            'new_category' => 'nullable|string|max:100',
            'new_group' => 'nullable|string|max:100',
            'new_size' => 'nullable|string|max:100',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Handle new category creation if needed
            if ($request->has('new_category') && !empty($request->new_category)) {
                $category = AssemblyCategory::create([
                    'business_id' => session('business_id'),
                    'company_id' => Auth::user()->company->id ?? null,
                    'name' => $request->new_category,
                    'is_active' => true
                ]);
                $validated['assembly_category_id'] = $category->id;
            } else if ($request->assembly_category_id === 'new') {
                $validated['assembly_category_id'] = null;
            }
            
            // Handle new group creation if needed
            if ($request->has('new_group') && !empty($request->new_group)) {
                $group = AssemblyGroup::create([
                    'business_id' => session('business_id'),
                    'company_id' => Auth::user()->company->id ?? null,
                    'name' => $request->new_group,
                    'is_active' => true
                ]);
                $validated['assembly_group_id'] = $group->id;
            } else if ($request->assembly_group_id === 'new') {
                $validated['assembly_group_id'] = null;
            }
            
            // Handle new size creation if needed
            if ($request->has('new_size') && !empty($request->new_size)) {
                $size = AssemblySize::create([
                    'business_id' => session('business_id'),
                    'company_id' => Auth::user()->company->id ?? null,
                    'name' => $request->new_size,
                    'is_active' => true
                ]);
                $validated['assembly_size_id'] = $size->id;
            } else if ($request->assembly_size_id === 'new') {
                $validated['assembly_size_id'] = null;
            }
            
            $assembledItem = AssembledItem::create([
                'business_id' => session('business_id'),
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
            
            return redirect()->route('bakery.manufacturing.assembly')
                ->with('success', 'Assembled item created successfully.')
                ->with('assembled_item_id', $assembledItem->id);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->with('error', 'Failed to create assembled item: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AssembledItem $assembly)
    {
        return redirect()->route('bakery.manufacturing.assembly');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssembledItem $assembly)
    {
        return redirect()->route('bakery.manufacturing.assembly');
    }

    /**
     * Update the specified assembled item
     */
    public function update(Request $request, AssembledItem $assembly)
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
            'new_category' => 'nullable|string|max:100',
            'new_group' => 'nullable|string|max:100',
            'new_size' => 'nullable|string|max:100',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Handle new category creation if needed
            if ($request->has('new_category') && !empty($request->new_category)) {
                $category = AssemblyCategory::create([
                    'business_id' => session('business_id'),
                    'company_id' => Auth::user()->company->id ?? null,
                    'name' => $request->new_category,
                    'is_active' => true
                ]);
                $validated['assembly_category_id'] = $category->id;
            } else if ($request->assembly_category_id === 'new') {
                $validated['assembly_category_id'] = null;
            }
            
            // Handle new group creation if needed
            if ($request->has('new_group') && !empty($request->new_group)) {
                $group = AssemblyGroup::create([
                    'business_id' => session('business_id'),
                    'company_id' => Auth::user()->company->id ?? null,
                    'name' => $request->new_group,
                    'is_active' => true
                ]);
                $validated['assembly_group_id'] = $group->id;
            } else if ($request->assembly_group_id === 'new') {
                $validated['assembly_group_id'] = null;
            }
            
            // Handle new size creation if needed
            if ($request->has('new_size') && !empty($request->new_size)) {
                $size = AssemblySize::create([
                    'business_id' => session('business_id'),
                    'company_id' => Auth::user()->company->id ?? null,
                    'name' => $request->new_size,
                    'is_active' => true
                ]);
                $validated['assembly_size_id'] = $size->id;
            } else if ($request->assembly_size_id === 'new') {
                $validated['assembly_size_id'] = null;
            }
            
            $assembly->update([
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
            
            $assembly->updateTotalCost();
            
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
    public function destroy(AssembledItem $assembly)
    {
        try {
            $assembly->delete();
            
            return redirect()->route('bakery.manufacturing.assembly')
                ->with('success', 'Assembled item deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete assembled item: ' . $e->getMessage());
        }
    }
    
    /**
     * Add an ingredient to an assembled item
     */
    public function addIngredient(Request $request, AssembledItem $assembly)
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
                'assembled_item_id' => $assembly->id,
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'cost' => $cost
            ], $data));
            
            // Update the total cost of the assembled item
            $assembly->updateTotalCost();
            
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
    public function createPasteDivision(Request $request, AssembledItem $assembly)
    {
        // Validate that this is a paste type item
        if ($assembly->type !== 'paste') {
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
            'waste_reason' => 'nullable|string|max:255',
            // Fields for creating a new output item
            'new_output_name' => 'nullable|required_if:output_assembled_item_id,|string|max:255',
            'new_output_category' => 'nullable|string|max:100',
            'new_output_group' => 'nullable|string|max:100',
            'new_output_selling_price' => 'nullable|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create new output item if needed
            $outputItemId = $validated['output_assembled_item_id'];
            
            if (!$outputItemId && $validated['new_output_name']) {
                $outputItem = AssembledItem::create([
                    'business_id' => $assembly->business_id,
                    'name' => $validated['new_output_name'],
                    'description' => "Created from paste division of {$assembly->name}",
                    'type' => 'single',
                    'unit' => $validated['unit'],
                    'selling_price' => $validated['new_output_selling_price'] ?? 0,
                    'other_costs' => 0,
                    'total_cost' => $assembly->calculateUnitCost() * $validated['quantity'],
                    'category' => $validated['new_output_category'],
                    'group' => $validated['new_output_group'],
                    'size' => $validated['quantity'].' '.$validated['unit'],
                    'created_by' => Auth::id()
                ]);
                
                $outputItemId = $outputItem->id;
            }
            
            // Create paste division
            PasteDivision::create([
                'assembled_item_id' => $assembly->id,
                'output_assembled_item_id' => $outputItemId,
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'flavor' => $validated['flavor'],
                'flavor_quantity' => $validated['flavor_quantity'],
                'flavor_unit' => $validated['flavor_unit'],
                'flavor_cost' => $validated['flavor_cost'],
                'waste_quantity' => $validated['waste_quantity'],
                'waste_reason' => $validated['waste_reason'],
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Paste division created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->with('error', 'Failed to create paste division: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Remove a paste division
     */
    public function removePasteDivision(PasteDivision $pasteDivision)
    {
        try {
            $pasteDivision->delete();
            
            return redirect()->back()->with('success', 'Paste division removed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove paste division: ' . $e->getMessage());
        }
    }
    
    /**
     * API: Get a single assembled item
     */
    public function getAssembledItem(AssembledItem $assembledItem)
    {
        return response()->json($assembledItem);
    }
    
    /**
     * API: Get ingredients for an assembled item
     */
    public function getIngredients(AssembledItem $assembledItem)
    {
        $ingredients = $assembledItem->ingredients->map(function ($ingredient) {
            $sourceName = '';
            $sourceType = '';
            
            if ($ingredient->ingredient_id) {
                $sourceName = $ingredient->ingredient->name;
                $sourceType = 'ingredient';
            } elseif ($ingredient->product_id) {
                $sourceName = $ingredient->product->name;
                $sourceType = 'product';
            } elseif ($ingredient->assembled_item_id_ref) {
                $sourceName = $ingredient->referencedAssembledItem->name;
                $sourceType = 'assembled item';
            }
            
            return [
                'id' => $ingredient->id,
                'source_name' => $sourceName,
                'source_type' => $sourceType,
                'quantity' => $ingredient->quantity,
                'unit' => $ingredient->unit,
                'cost' => $ingredient->cost,
            ];
        });
        
        return response()->json($ingredients);
    }
    
    /**
     * API: Get paste divisions for an assembled item
     */
    public function getPasteDivisions(AssembledItem $assembledItem)
    {
        $pasteDivisions = PasteDivision::with('outputAssembledItem')
            ->where('assembled_item_id', $assembledItem->id)
            ->get();
            
        return response()->json($pasteDivisions);
    }
}