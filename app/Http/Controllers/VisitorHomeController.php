<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class VisitorHomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();
            
        $categories = Category::withCount('products')->get();
        
        return view('store', compact('products', 'categories'));
    }
}