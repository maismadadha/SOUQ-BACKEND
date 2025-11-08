<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DeliveryOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ==============================
    // 1️⃣ إنشاء أو تحديث Cart
    // ==============================
    public function addToCart(Request $request)
    {
        $customerId = $request->user()->id;
        $storeId = $request->store_id;
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;
        $price = $request->price ?? 0;

        // نتحقق إذا عند الزبون cart موجود
        $existingCart = Order::where('customer_id', $customerId)
            ->where('status', 'ON_CART')
            ->first();

        if ($existingCart) {
            // إذا الـ cart من متجر آخر
            if ($existingCart->store_id != $storeId) {
                return response()->json([
                    'message' => 'لديك طلب مفتوح من متجر آخر، هل تريد حذفه وإنشاء طلب جديد؟',
                    'cart_id' => $existingCart->id
                ], 409); // 409 = Conflict
            }

            // تحديث الـ cart الحالي (إضافة أو تعديل الـ item)
            $item = $existingCart->items()->where('product_id', $productId)->first();
            if ($item) {
                $item->quantity += $quantity;
                $item->save();
            } else {
                $existingCart->items()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price
                ]);
            }

            $this->recalculateOrder($existingCart);

            return response()->json([
                'message' => 'تم تحديث السلة بنجاح',
                'order' => $existingCart->load('items')
            ]);
        }

        // إنشاء cart جديد
        $order = Order::create([
            'customer_id' => $customerId,
            'store_id' => $storeId,
            'subtotal' => $price * $quantity,
            'items_count' => $quantity,
        ]);

        $order->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price
        ]);

        return response()->json([
            'message' => 'تم إنشاء السلة بنجاح',
            'order' => $order->load('items')
        ]);
    }

    // ==============================
    // 2️⃣ حذف Cart إذا الزبون وافق
    // ==============================
    public function deleteCart($cartId)
    {
        $cart = Order::findOrFail($cartId);
        $cart->delete();

        return response()->json([
            'message' => 'تم حذف السلة القديمة بنجاح'
        ]);
    }

    // ==============================
    // 3️⃣ تعديل كمية منتج في الـ cart
    // ==============================
    public function updateCartItem(Request $request, $itemId)
    {
        $item = OrderItem::findOrFail($itemId);
        $item->quantity = $request->quantity;
        $item->save();

        $this->recalculateOrder($item->order);

        return response()->json([
            'message' => 'تم تعديل كمية المنتج',
            'order' => $item->order->load('items')
        ]);
    }

    // ==============================
    // 4️⃣ تعيين عنوان للطلب
    // ==============================
    public function setOrderAddress(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->address_id = $request->address_id;
        $order->save();

        return response()->json([
            'message' => 'تم تعيين العنوان للطلب',
            'order' => $order->load('address')
        ]);
    }

    // ==============================
    // 5️⃣ تأكيد الطلب
    // ==============================
    public function confirmOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->status = 'CONFIRMED';
        $order->save();

        return response()->json([
            'message' => 'تم تأكيد الطلب',
            'order' => $order->load('items', 'address')
        ]);
    }

    // ==============================
    // 6️⃣ تحديث حالة الطلب (متجر / مندوب)
    // ==============================
    public function updateOrderStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $status = $request->status;

        $order->status = $status;
        $order->save();

        // إذا بدأ التوصيل، نحدث أو ننشئ DeliveryOrder
        if (in_array($status, ['OUT_FOR_DELIVERY', 'DELIVERED'])) {
            $delivery = $order->delivery ?? new DeliveryOrder(['order_id' => $order->id]);
            if ($status == 'OUT_FOR_DELIVERY') $delivery->picked_at = now();
            if ($status == 'DELIVERED') $delivery->delivered_at = now();
            $delivery->delivery_id = $request->delivery_id; // id المندوب
            $delivery->save();
        }

        return response()->json([
            'message' => 'تم تحديث حالة الطلب',
            'order' => $order->load('items', 'delivery')
        ]);
    }

    // ==============================
    // 7️⃣ عرض الطلبات حسب الزبون
    // ==============================
    public function getOrdersForCustomer(Request $request)
    {
        $customerId = $request->user()->id;
        $orders = Order::where('customer_id', $customerId)
            ->with('items', 'address', 'delivery')
            ->get();

        return response()->json($orders);
    }

    // ==============================
    // 8️⃣ عرض الطلبات حسب المتجر
    // ==============================
    public function getOrdersForStore(Request $request)
    {
        $storeId = $request->user()->id;
        $orders = Order::where('store_id', $storeId)
            ->with('items', 'address', 'delivery')
            ->get();

        return response()->json($orders);
    }

    // ==============================
    // 9️⃣ عرض الطلبات حسب المندوب
    // ==============================
    public function getOrdersForDelivery(Request $request)
    {
        $deliveryId = $request->user()->id;
        $orders = Order::whereHas('delivery', function ($q) use ($deliveryId) {
            $q->where('delivery_id', $deliveryId);
        })->with('items', 'address', 'delivery')->get();

        return response()->json($orders);
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
