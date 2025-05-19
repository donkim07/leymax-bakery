<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of bakery orders
     */
    public function bakeryOrders()
    {
        return view('bakery.sales.orders');
    }

    /**
     * Display the point of sale page for bakery
     */
    public function bakeryPos()
    {
        return view('bakery.sales.pos');
    }

    /**
     * Display bakery invoices
     */
    public function bakeryInvoices()
    {
        return view('bakery.sales.invoices');
    }

    /**
     * Display paid invoices for bakery
     */
    public function bakeryPaidInvoices()
    {
        return view('bakery.sales.invoices.paid');
    }

    /**
     * Display bakery delivery
     */
    public function bakeryDelivery()
    {
        return view('bakery.sales.delivery');
    }

    /**
     * Display bakery returns
     */
    public function bakeryReturns()
    {
        return view('bakery.sales.returns');
    }

    /**
     * Display academy orders
     */
    public function academyOrders()
    {
        return view('academy.orders');
    }

    /**
     * Display academy invoices
     */
    public function academyInvoices()
    {
        return view('academy.invoices');
    }

    /**
     * Display tools orders
     */
    public function toolsOrders()
    {
        return view('tools.sales.orders');
    }

    /**
     * Display tools point of sale
     */
    public function toolsPos()
    {
        return view('tools.sales.pos');
    }

    /**
     * Display tools invoices
     */
    public function toolsInvoices()
    {
        return view('tools.sales.invoices');
    }

    /**
     * Display paid invoices for tools
     */
    public function toolsPaidInvoices()
    {
        return view('tools.sales.invoices.paid');
    }

    /**
     * Display tools delivery
     */
    public function toolsDelivery()
    {
        return view('tools.sales.delivery');
    }

    /**
     * Display tools returns
     */
    public function toolsReturns()
    {
        return view('tools.sales.returns');
    }
} 