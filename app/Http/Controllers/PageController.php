<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    public function callForPapers()
    {
        return view('pages.call-for-papers');
    }

    public function committees()
    {
        return view('pages.committees');
    }

    public function register()
    {
        return view('pages.register');
    }
}