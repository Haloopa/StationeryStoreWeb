<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Menampilkan daftar produk
    public function index(Request $request)
    {
        // Parameter filter
        $search = $request->get('search');
        $categorySlug = $request->get('category');
        $stockFilter = $request->get('stock');
        
        // Query produk
        $query = Product::with('category')
                        ->where('is_active', true)
                        ->latest();

        // Filter pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($categorySlug && $categorySlug !== 'all') {
            $query->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Filter stok
        if ($stockFilter === 'available') {
            $query->where('stock', '>', 0);
        } elseif ($stockFilter === 'low') {
            $query->where('stock', '>', 0)->where('stock', '<=', 10);
        }

        $products = $query->paginate(12);

        $categories = Category::withCount(['products' => function($query) {
            $query->where('is_active', true);
        }])->get();

        $currentCategory = null;
        if ($categorySlug && $categorySlug !== 'all') {
            $currentCategory = Category::where('slug', $categorySlug)->first();
        }

        $totalProducts = Product::where('is_active', true)->count();

        return view('products.index', compact(
            'products', 
            'categories', 
            'currentCategory', 
            'totalProducts',
            'search',
            'categorySlug',
            'stockFilter'
        ));
    }

    // Menampilkan detail satu produk
    public function show($slug)
    {
        $product = Product::with('category')
                          ->where('slug', $slug)
                          ->where('is_active', true)
                          ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->where('is_active', true)
                                  ->inRandomOrder()
                                  ->limit(4)
                                  ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}