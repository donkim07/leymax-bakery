<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of bakery purchase orders
     */
    public function bakeryOrders()
    {
        return view('bakery.purchases.orders');
    }

    /**
     * Display a listing of bakery bills
     */
    public function bakeryBills()
    {
        return view('bakery.purchases.bills');
    }

    /**
     * Display a listing of bakery credits
     */
    public function bakeryCredits()
    {
        return view('bakery.purchases.credits');
    }

    /**
     * Display bakery receive page
     */
    public function bakeryReceive()
    {
        return view('bakery.purchases.receive');
    }

    /**
     * Display bakery purchase returns
     */
    public function bakeryReturns()
    {
        return view('bakery.purchases.returns');
    }

    /**
     * Display a listing of tools purchase orders
     */
    public function toolsOrders()
    {
        return view('tools.purchases.orders');
    }

    /**
     * Display a listing of tools bills
     */
    public function toolsBills()
    {
        return view('tools.purchases.bills');
    }

    /**
     * Display a listing of tools credits
     */
    public function toolsCredits()
    {
        return view('tools.purchases.credits');
    }

    /**
     * Display tools receive page
     */
    public function toolsReceive()
    {
        return view('tools.purchases.receive');
    }

    /**
     * Display tools purchase returns
     */
    public function toolsReturns()
    {
        return view('tools.purchases.returns');
    }
} 