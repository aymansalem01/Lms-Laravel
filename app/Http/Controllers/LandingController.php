<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('landing.index');
    }
}
