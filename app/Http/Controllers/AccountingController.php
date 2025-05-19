<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountingController extends Controller
{
    /**
     * Display bakery accounts
     */
    public function bakeryAccounts()
    {
        return view('bakery.accounting.accounts');
    }

    /**
     * Display bakery expenses
     */
    public function bakeryExpenses()
    {
        return view('bakery.accounting.expenses');
    }

    /**
     * Display bakery journal
     */
    public function bakeryJournal()
    {
        return view('bakery.accounting.journal');
    }

    /**
     * Display bakery banking
     */
    public function bakeryBanking()
    {
        return view('bakery.accounting.banking');
    }

    /**
     * Display bakery payroll
     */
    public function bakeryPayroll()
    {
        return view('bakery.accounting.payroll');
    }

    /**
     * Display tools accounts
     */
    public function toolsAccounts()
    {
        return view('tools.accounting.accounts');
    }

    /**
     * Display tools expenses
     */
    public function toolsExpenses()
    {
        return view('tools.accounting.expenses');
    }

    /**
     * Display tools journal
     */
    public function toolsJournal()
    {
        return view('tools.accounting.journal');
    }

    /**
     * Display tools banking
     */
    public function toolsBanking()
    {
        return view('tools.accounting.banking');
    }

    /**
     * Display tools payroll
     */
    public function toolsPayroll()
    {
        return view('tools.accounting.payroll');
    }
} 