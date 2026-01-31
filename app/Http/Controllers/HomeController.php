<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        if (Auth::check()) {
            // Redirect berdasarkan role
            if (Auth::user()->role === 'admin') { 
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('store');
            }
        }
        
        // Jika belum login, redirect ke store
        return redirect()->route('store');
    }
}