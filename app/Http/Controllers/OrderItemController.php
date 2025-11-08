<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    // ==============================
    // 1️⃣ عرض كل الـ order items
    // ==============================
    public function index()
    {
        $orderItems = OrderItem::with('order', 'product')->get();
        return response()->json($orderItems);
    }

    // ==============================
    // 2️⃣ عرض OrderItem معين
    // ==============================
    public function show($id)
    {
        $orderItem = OrderItem::with('order', 'product')->find($id);

        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        return response()->json($orderItem);
    }

    // ==============================
    // 3️⃣ إنشاء OrderItem جديد
    // ==============================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $orderItem = OrderItem::create($validated);

        $this->recalculateOrder($orderItem->order);

        return response()->json([
            'message' => 'Order item created successfully',
            'data' => $orderItem
        ], 201);
    }

    // ==============================
    // 4️⃣ تحديث OrderItem
    // ==============================
    public function update(Request $request, $id)
    {
        $orderItem = OrderItem::find($id);

        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        $validated = $request->validate([
            'order_id' => 'sometimes|required|exists:orders,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $orderItem->update($validated);

        $this->recalculateOrder($orderItem->order);

        return response()->json([
            'message' => 'Order item updated successfully',
            'data' => $orderItem
        ]);
    }

    // ==============================
    // 5️⃣ حذف OrderItem
    // ==============================
    public function destroy($id)
    {
        $orderItem = OrderItem::find($id);

        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        $order = $orderItem->order;
        $orderItem->delete();

        $this->recalculateOrder($order);

        return response()->json(['message' => 'Order item deleted successfully']);
    }

    // ==============================
    // 🔹 دالة مساعدة لإعادة حساب المجموع
    // ==============================
    private function recalculateOrder(Order $order)
    {
        $subtotal = $order->items()->sum(DB::raw('price * quantity'));
        $itemsCount = $order->items()->sum('quantity');

        $order->subtotal = $subtotal;
        $order->items_count = $itemsCount;
        $order->total_price = $subtotal + $order->delivery_fee - $order->discount_total;
        $order->save();
    }
}
