<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of bakery suppliers
     */
    public function bakerySuppliers()
    {
        return view('bakery.suppliers.index');
    }

    /**
     * Display a listing of tools suppliers
     */
    public function toolsSuppliers()
    {
        return view('tools.suppliers.index');
    }
} 