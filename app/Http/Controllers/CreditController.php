<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreditController extends Controller
{
    /**
     * Display a specific credit
     */
    public function show($credit)
    {
        return view('bakery.purchases.credit', compact('credit'));
    }

    /**
     * Mark a credit as used
     */
    public function markAsUsed($credit)
    {
        // Logic to mark credit as used
        return redirect()->back()->with('success', 'Credit marked as used successfully');
    }
} 