<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DeliveryOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // 1) إضافة منتج للسلة أو إنشاء سلة جديدة
    public function addToCart(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'store_id'    => 'required|exists:users,id',
            'product_id'  => 'required|exists:products,id',
            'quantity'    => 'nullable|integer|min:1',
            'price'       => 'required|numeric|min:0',
        ]);

        $customerId = $data['customer_id'];
        $storeId    = $data['store_id'];
        $productId  = $data['product_id'];
        $quantity   = $data['quantity'] ?? 1;
        $price      = $data['price'];

        $existingCart = Order::where('customer_id', $customerId)
            ->where('status', 'ON_CART')
            ->first();

        if ($existingCart) {
            if ($existingCart->store_id != $storeId) {
                return response()->json([
                    'message' => 'لديك طلب مفتوح من متجر آخر، هل تريد حذفه وإنشاء طلب جديد؟',
                    'cart_id' => $existingCart->id,
                ], 409);
            }

            $item = $existingCart->items()->where('product_id', $productId)->first();

            if ($item) {
                $item->quantity += $quantity;
                $item->save();
            } else {
                $existingCart->items()->create([
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'price'      => $price,
                ]);
            }

            $this->recalculateOrder($existingCart);

            return response()->json([
                'message' => 'تم تحديث السلة بنجاح',
                'order'   => $existingCart->load('items.product'),
            ]);
        }

        $order = Order::create([
            'customer_id' => $customerId,
            'store_id'    => $storeId,
            'subtotal'    => $price * $quantity,
            'items_count' => $quantity,
            'status'      => 'ON_CART',
        ]);

        $order->items()->create([
            'product_id' => $productId,
            'quantity'   => $quantity,
            'price'      => $price,
        ]);

        return response()->json([
            'message' => 'تم إنشاء السلة بنجاح',
            'order'   => $order->load('items.product'),
        ]);
    }

    // 2) حذف سلة كاملة
    public function deleteCart($cartId)
    {
        $cart = Order::findOrFail($cartId);
        $cart->delete();

        return response()->json([
            'message' => 'تم حذف السلة القديمة بنجاح',
        ]);
    }

    // 3) تعديل كمية منتج داخل السلة
    public function updateCartItem(Request $request, $itemId)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = OrderItem::findOrFail($itemId);
        $item->quantity = $data['quantity'];
        $item->save();

        $this->recalculateOrder($item->order);

        return response()->json([
            'message' => 'تم تعديل كمية المنتج',
            'order'   => $item->order->load('items.product'),
        ]);
    }

    // 4) تعيين عنوان للطلب
    public function setOrderAddress(Request $request, $orderId)
    {
        $data = $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        $order = Order::findOrFail($orderId);
        $order->address_id = $data['address_id'];
        $order->save();

        return response()->json([
            'message' => 'تم تعيين العنوان للطلب',
            'order'   => $order->load('address'),
        ]);
    }

    // 5) تأكيد الطلب
    public function confirmOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->status = 'CONFIRMED';
        $order->save();

        return response()->json([
            'message' => 'تم تأكيد الطلب',
            'order'   => $order->load('items.product', 'address'),]);
    }

    // 6) تحديث حالة الطلب (من المتجر أو المندوب)
    public function updateOrderStatus(Request $request, $orderId)
    {
        $data = $request->validate([
            'status'      => 'required|string',
            'delivery_id' => 'nullable|exists:users,id',
        ]);

        $order  = Order::findOrFail($orderId);
        $status = $data['status'];

        $order->status = $status;
        $order->save();

        if (in_array($status, ['OUT_FOR_DELIVERY', 'DELIVERED'])) {
            $delivery = $order->delivery ?? new DeliveryOrder(['order_id' => $order->id]);

            if ($status === 'OUT_FOR_DELIVERY') {
                $delivery->picked_at = now();
            }

            if ($status === 'DELIVERED') {
                $delivery->delivered_at = now();
            }

            if (!empty($data['delivery_id'])) {
                $delivery->delivery_id = $data['delivery_id'];
            }

            $delivery->save();
        }

        return response()->json([
            'message' => 'تم تحديث حالة الطلب',
            'order'   => $order->load('items.product', 'delivery'),
        ]);
    }

    // 7) عرض طلبات زبون معيّن
    public function getOrdersForCustomer(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:users,id',
        ]);

        $customerId = $data['customer_id'];

        $orders = Order::where('customer_id', $customerId)
            ->with([
                'items.product',
                'address',
                'delivery',
                'store.sellerProfile',
            ])
            ->get();

        // نضيف اسم المتجر للريسبونس
        $orders->each(function ($order) {
            $order->store_name = optional(optional($order->store)->sellerProfile)->name;
        });

        return response()->json($orders);
    }

    // 8) عرض طلبات متجر معيّن
    public function getOrdersForStore(Request $request)
    {
        $storeId = $request->query('store_id');

        $orders = Order::where('store_id', $storeId)
            ->with('items.product', 'address', 'delivery')
            ->get();

        return response()->json($orders);
    }

    // 9) عرض طلبات مندوب توصيل معيّن
    public function getOrdersForDelivery(Request $request)
    {
        $deliveryId = $request->query('delivery_id');

        $orders = Order::whereHas('delivery', function ($q) use ($deliveryId) {
                $q->where('delivery_id', $deliveryId);
            })
            ->with('items.product', 'address', 'delivery')
            ->get();

        return response()->json($orders);
    }

    // دالة مساعدة لحساب المجاميع
    private function recalculateOrder(Order $order)
    {
        $subtotal   = $order->items()->sum(DB::raw('price * quantity'));
        $itemsCount = $order->items()->sum('quantity');

        $order->subtotal    = $subtotal;
        $order->items_count = $itemsCount;
        $order->total_price = $subtotal + $order->delivery_fee - $order->discount_total;
        $order->save();
    }
}
