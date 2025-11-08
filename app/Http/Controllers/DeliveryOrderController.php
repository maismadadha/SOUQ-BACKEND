<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryOrder;
use App\Models\Order;

class DeliveryOrderController extends Controller
{
    // ==============================
    // عرض كل الـ delivery orders
    // ==============================
    public function index()
    {
        $deliveryOrders = DeliveryOrder::with(['order', 'delivery'])->get();
        return response()->json($deliveryOrders);
    }

    // ==============================
    // عرض delivery order معين
    // ==============================
    public function show($id)
    {
        $deliveryOrder = DeliveryOrder::with(['order', 'delivery'])->find($id);

        if (!$deliveryOrder) {
            return response()->json(['message' => 'Delivery order not found'], 404);
        }

        return response()->json($deliveryOrder);
    }

    // ==============================
    // إنشاء DeliveryOrder
    // ==============================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_id' => 'required|exists:users,id',
            'picked_at' => 'nullable|date',
            'delivered_at' => 'nullable|date',
        ]);

        $deliveryOrder = DeliveryOrder::create($validated);

        return response()->json([
            'message' => 'Delivery order created successfully',
            'data' => $deliveryOrder
        ], 201);
    }

    // ==============================
    // تحديث DeliveryOrder
    // ==============================
    public function update(Request $request, $id)
    {
        $deliveryOrder = DeliveryOrder::find($id);

        if (!$deliveryOrder) {
            return response()->json(['message' => 'Delivery order not found'], 404);
        }

        $validated = $request->validate([
            'order_id' => 'sometimes|required|exists:orders,id',
            'delivery_id' => 'sometimes|required|exists:users,id',
            'picked_at' => 'nullable|date',
            'delivered_at' => 'nullable|date',
        ]);

        $deliveryOrder->update($validated);

        // ==============================
        // تحديث حالة الـ order تلقائيًا حسب picked/delivered
        // ==============================
        $order = $deliveryOrder->order;
        if ($deliveryOrder->delivered_at) {
            $order->status = 'DELIVERED';
        } elseif ($deliveryOrder->picked_at) {
            $order->status = 'OUT_FOR_DELIVERY';
        }
        $order->save();

        return response()->json([
            'message' => 'Delivery order updated successfully',
            'data' => $deliveryOrder
        ]);
    }

    // ==============================
    // حذف DeliveryOrder
    // ==============================
    public function destroy($id)
    {
        $deliveryOrder = DeliveryOrder::find($id);

        if (!$deliveryOrder) {
            return response()->json(['message' => 'Delivery order not found'], 404);
        }

        $deliveryOrder->delete();

        return response()->json(['message' => 'Delivery order deleted successfully']);
    }
}
