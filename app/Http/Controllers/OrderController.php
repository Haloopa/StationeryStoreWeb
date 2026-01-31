<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Menampilkan semua order
    public function index()
    {
        $user = Auth::user();
        $orders = Order::withCount('items')
                    ->where('user_id', $user->id)
                    ->latest()
                    ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    // Menampilkan detail order
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load(['items.product.category']);

        return view('orders.show', compact('order'));
    }

    // Checkout dari cart ke whatsapp
    public function checkout()
    {
        $user = Auth::user();
        $carts = Cart::with('product.category')
                    ->where('user_id', $user->id)
                    ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja kosong.');
        }

        DB::beginTransaction();

        try {
            // Buat order record
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'total_amount' => $carts->sum(function($cart) {
                    return $cart->product->price * $cart->quantity;
                }),
                'status' => 'pending',
            ]);

            // Simpan item order 
            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->product->price,
                    'subtotal' => $cart->product->price * $cart->quantity,
                ]);
            }

            // Generate pesan whatsapp
            $whatsappMessage = $this->generateWhatsAppMessage($order, $carts, $user);
            
            // Simpan transaksi
            DB::commit();
            
            // Redirect ke whatsapp
            return redirect($whatsappMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')
                ->with('error', 'Terjadi kesalahan saat checkout: ' . $e->getMessage());
        }
    }

    // Bikin pesanan via whatsapp
    private function generateWhatsAppMessage($order, $carts, $user)
    {
        $phone = "621235678000";
        
        $message = "Halo, saya ingin memesan:%0A";
        $message .= "📦 *No. Order: {$order->order_number}*%0A";
        $message .= "👤 *Nama: {$user->name}*%0A";
        $message .= "📧 *Email: {$user->email}*%0A%0A";
        
        $message .= "*🛒 Daftar Pesanan:*%0A";
        foreach ($carts as $cart) {
            $message .= "• {$cart->product->name}%0A";
            $message .= "  Jumlah: {$cart->quantity} x Rp " . number_format($cart->product->price, 0, ',', '.') . "%0A";
            $message .= "  Subtotal: Rp " . number_format($cart->product->price * $cart->quantity, 0, ',', '.') . "%0A%0A";
        }
        
        $total = $order->total_amount;
        $message .= "*💰 Total: Rp " . number_format($total, 0, ',', '.') . "*%0A%0A";
        
        $message .= "Mohon konfirmasi ketersediaan stock dan beritahu cara pembayarannya.%0A";
        $message .= "Terima kasih! 😊";
        
        return "https://api.whatsapp.com/send?phone={$phone}&text={$message}";
    }
}