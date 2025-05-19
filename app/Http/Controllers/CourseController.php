<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display academy courses
     */
    public function index()
    {
        return view('academy.courses.index');
    }

    /**
     * Display academy course categories
     */
    public function categories()
    {
        return view('academy.courses.categories');
    }

    /**
     * Display academy media
     */
    public function media()
    {
        return view('academy.courses.media');
    }

    /**
     * Display academy links
     */
    public function links()
    {
        return view('academy.courses.links');
    }
} 