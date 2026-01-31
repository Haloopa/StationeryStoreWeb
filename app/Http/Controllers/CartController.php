<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Menampilkan Cart
    public function index()
    {
        $user = Auth::user();
        $carts = Cart::with('product')
                    ->where('user_id', $user->id)
                    ->get();
        
        $total = $carts->sum(function ($cart) {
            return $cart->product->price * $cart->quantity;
        });
        
        return view('cart.index', compact('carts', 'total'));
    }

    // Menambahkan produk ke cart
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1|max:' . $product->stock,
        ]);

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->first();

        $quantity = $request->quantity ?? 1;

        if ($cart) {
            $newQuantity = $cart->quantity + $quantity;
            
            // Cek stok tersedia
            if ($newQuantity > $product->stock) {
                return redirect()->back()
                    ->with('error', 'Jumlah melebihi stok yang tersedia. Stok tersedia: ' . $product->stock);
            }
            
            $cart->update(['quantity' => $newQuantity]);
            $message = 'Jumlah produk diperbarui di keranjang';
        } else {
            // Buat cart item baru
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
            $message = 'Produk berhasil ditambahkan ke keranjang';
        }

        return redirect()->back()
            ->with('success', $message);
    }

    // Update jumlah produk cart
    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cart->product->stock,
        ]);

        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->update(['quantity' => $request->quantity]);

        return redirect()->back()
            ->with('success', 'Jumlah produk diperbarui');
    }

    // Hapus Produk
    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

        return redirect()->back()
            ->with('success', 'Produk dihapus dari keranjang');
    }

    // Clear isi cart
    public function clear()
    {
        $user = Auth::user();
        Cart::where('user_id', $user->id)->delete();

        return redirect()->back()
            ->with('success', 'Keranjang berhasil dikosongkan');
    }
}