<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssembledItem;
use App\Models\AssembledItemIngredient;
use App\Models\PasteDivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssembledItemController extends Controller
{
    /**
     * Get an assembled item
     */
    public function show($id)
    {
        $item = AssembledItem::findOrFail($id);
        
        // Authorize that the user can access this business's data
        if ($item->business_id !== session('business_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($item);
    }
    
    /**
     * Get all ingredients for an assembled item
     */
    public function ingredients($id)
    {
        $item = AssembledItem::findOrFail($id);
        
        // Authorize that the user can access this business's data
        if ($item->business_id !== session('business_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $ingredients = AssembledItemIngredient::with(['ingredient', 'product', 'referencedAssembledItem'])
            ->where('assembled_item_id', $id)
            ->get()
            ->map(function ($ingredient) {
                return [
                    'id' => $ingredient->id,
                    'source_type' => $ingredient->source_type,
                    'source_name' => $ingredient->source_name,
                    'quantity' => $ingredient->quantity,
                    'unit' => $ingredient->unit,
                    'cost' => $ingredient->cost
                ];
            });
        
        return response()->json($ingredients);
    }
    
    /**
     * Get all paste divisions for an assembled item
     */
    public function pasteDivisions($id)
    {
        $item = AssembledItem::findOrFail($id);
        
        // Authorize that the user can access this business's data
        if ($item->business_id !== session('business_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $divisions = PasteDivision::with('outputItem')
            ->where('assembled_item_id', $id)
            ->get();
        
        return response()->json($divisions);
    }
}