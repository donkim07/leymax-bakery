<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillController extends Controller
{
    /**
     * Display a specific bill
     */
    public function show($bill)
    {
        return view('bakery.purchases.bill', compact('bill'));
    }

    /**
     * Mark a bill as paid
     */
    public function markAsPaid($bill)
    {
        // Logic to mark bill as paid
        return redirect()->back()->with('success', 'Bill marked as paid successfully');
    }
} 