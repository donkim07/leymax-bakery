<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display bakery invoices
     */
    public function bakeryInvoices()
    {
        return view('bakery.sales.invoices');
    }

    /**
     * Display bakery paid invoices
     */
    public function bakeryPaidInvoices()
    {
        return view('bakery.sales.invoices.paid');
    }

    /**
     * Display bakery unpaid invoices
     */
    public function bakeryUnpaidInvoices()
    {
        return view('bakery.sales.invoices.unpaid');
    }

    /**
     * Display bakery draft invoices
     */
    public function bakeryDraftInvoices()
    {
        return view('bakery.sales.invoices.draft');
    }

    /**
     * Display tools invoices
     */
    public function toolsInvoices()
    {
        return view('tools.sales.invoices');
    }

    /**
     * Display tools paid invoices
     */
    public function toolsPaidInvoices()
    {
        return view('tools.sales.invoices.paid');
    }

    /**
     * Display tools unpaid invoices
     */
    public function toolsUnpaidInvoices()
    {
        return view('tools.sales.invoices.unpaid');
    }

    /**
     * Display tools draft invoices
     */
    public function toolsDraftInvoices()
    {
        return view('tools.sales.invoices.draft');
    }

    /**
     * Display a specific invoice
     */
    public function show($invoice)
    {
        return view('bakery.sales.invoice', compact('invoice'));
    }

    /**
     * Mark an invoice as paid
     */
    public function markAsPaid($invoice)
    {
        // Logic to mark invoice as paid
        return redirect()->back()->with('success', 'Invoice marked as paid successfully');
    }
} 