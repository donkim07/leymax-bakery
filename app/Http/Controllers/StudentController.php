<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display academy students
     */
    public function index()
    {
        return view('academy.students.index');
    }

    /**
     * Display academy student progress
     */
    public function progress()
    {
        return view('academy.students.progress');
    }
} 