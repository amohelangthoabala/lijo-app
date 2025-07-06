<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:pickup,delivery',
            'delivery_address' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.variant' => 'nullable|string',
        ]);

        // Get all product IDs
        $productIds = collect($validated['items'])->pluck('product_id');

        // Load products with their restaurant IDs
        $products = \App\Models\Product::whereIn('id', $productIds)->with('restaurant')->get();

        // Ensure all products belong to the same restaurant
        $restaurantIds = $products->pluck('restaurant_id')->unique();

        if ($restaurantIds->count() > 1) {
            return response()->json([
                'message' => 'Your cart contains items from multiple restaurants. Please order separately.',
            ], 422);
        }

        $restaurantId = $restaurantIds->first();

        $deliveryCost = $validated['type'] === 'delivery' ? 5.00 : 0.00;
        $subtotal = collect($validated['items'])->sum(fn($item) => $item['price'] * $item['quantity']);
        $total = $subtotal + $deliveryCost;

        $order = Order::create([
            'user_id' => auth()->id(),
            'restaurant_id' => $restaurantId, // ✅ NEW FIELD
            'type' => $validated['type'],
            'delivery_address' => $validated['delivery_address'] ?? null,
            'delivery_cost' => $deliveryCost,
            'total' => $total,
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'variant' => $item['variant'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order->load('items.product'),
        ]);
    }

}
