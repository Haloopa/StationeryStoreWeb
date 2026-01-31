<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek login user
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Cek admin
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return redirect()->route('store')->with('error', 'Anda tidak memiliki akses ke halaman admin.');
        }
        
        return $next($request);
    }
}