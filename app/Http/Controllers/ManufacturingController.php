<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManufacturingController extends Controller
{
    /**
     * Inventory Assembly page
     * Used to create recipes and assemble products from inventory items
     */
    public function assembly()
    {
        return view('manufacturing.assembly');
    }
    
    /**
     * Manufacturing Process page
     * Used for the actual manufacturing of products (cakes, pastes)
     */
    public function process()
    {
        return view('manufacturing.process');
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
} 