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
        'customer_id'    => 'required|exists:users,id',
        'store_id'       => 'required|exists:users,id',
        'product_id'     => 'required|exists:products,id',
        'quantity'       => 'nullable|integer|min:1',
        'price'          => 'required|numeric|min:0',
        'customizations' => 'nullable|array', // 👈 جديد
    ]);

    $customerId    = $data['customer_id'];
    $storeId       = $data['store_id'];
    $productId     = $data['product_id'];
    $quantity      = $data['quantity'] ?? 1;
    $price         = $data['price'];
    $customizations = $data['customizations'] ?? null;

    $existingCart = Order::where('customer_id', $customerId)
        ->where('status', 'ON_CART')
        ->first();

    if ($existingCart) {
        if ($existingCart->store_id != $storeId) {
            return response()->json([
                'message' => 'لديك طلب مفتوح من متجر آخر، هل تريد حذفه وإنشاء طلب جديد؟',
                'cart_id' => $existingCart->id
            ], 409);
        }

        // 🔹 نحاول نلاقي item لنفس المنتج ونفس التخصيصات
        $item = $existingCart->items()
            ->where('product_id', $productId)
            ->where(function ($q) use ($customizations) {
                if ($customizations === null) {
                    $q->whereNull('customizations');
                } else {
                    $q->where('customizations', json_encode($customizations));
                }
            })
            ->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->save();
        } else {
            $existingCart->items()->create([
                'product_id'     => $productId,
                'quantity'       => $quantity,
                'price'          => $price,
                'customizations' => $customizations,
            ]);
        }

        $this->recalculateOrder($existingCart);

        return response()->json([
            'message' => 'تم تحديث السلة بنجاح',
            'order'   => $existingCart->load('items.product')
        ]);
    }

    // Cart جديد
    $order = Order::create([
        'customer_id' => $customerId,
        'store_id'    => $storeId,
        'subtotal'    => $price * $quantity,
        'items_count' => $quantity,
    ]);

    $order->items()->create([
        'product_id'     => $productId,
        'quantity'       => $quantity,
        'price'          => $price,
        'customizations' => $customizations,
    ]);

    return response()->json([
        'message' => 'تم إنشاء السلة بنجاح',
        'order'   => $order->load('items.product')
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

        if (in_array($status, ['OUT_FOR_DELIVERY', 'CASH_COLLECTED', 'DELIVERED'])) {
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
    $status  = $request->query('status'); // ← نقرأ الحالة لو مبعوتة

    // نبلش بالـ query
    $query = Order::where('store_id', $storeId)
        ->with([
            'items.product',
            'address',
            'delivery',
            'customer.customerProfile',
        ]);

    // لو مبعوت status نعمل فلترة
    if (!empty($status)) {
        $query->where('status', $status);
    }

    $orders = $query->get();

    // إضافة customer_name لكل طلب
    $orders->each(function ($order) {
        $profile = optional(optional($order->customer)->customerProfile);

        $first = $profile->first_name ?? '';
        $last  = $profile->last_name ?? '';

        $order->customer_name = trim($first . ' ' . $last);
    });

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

    public function setOrderNote(Request $request, $orderId)
{
    $data = $request->validate([
        'note' => 'nullable|string|max:1000',
    ]);

    $order = Order::findOrFail($orderId);
    $order->note = $data['note'] ?? null;
    $order->save();

    return response()->json([
        'message' => 'تم تحديث ملاحظة الطلب',
        'order'   => $order->load('items.product', 'address', 'delivery')
    ]);
}

public function setOrderMeta(Request $request, $orderId)
{
    $data = $request->validate([
        'delivery_fee'    => 'required|numeric|min:0',
        'payment_method'  => 'required|string|in:cash,card',
    ]);

    $order = Order::findOrFail($orderId);
    $order->delivery_fee    = $data['delivery_fee'];
    $order->payment_method  = $data['payment_method'];

    // نحدّث المجموع النهائي
    $order->total_price = $order->subtotal + $order->delivery_fee - $order->discount_total;
    $order->save();

    return response()->json([
        'message' => 'تم تحديث بيانات الطلب',
        'order'   => $order->load('items.product', 'address'),
    ]);
}

// عرض طلب واحد لزبون معيّن باستخدام الـ orderId
public function getOrderById(Request $request)
{
    $data = $request->validate([
        'order_id' => 'required|exists:orders,id',
    ]);

    $orderId = $data['order_id'];

    $order = Order::where('id', $orderId)
        ->with([
            'items.product',
            'address',
            'delivery',
            'store.sellerProfile',
            'customer.customerProfile',
        ])
        ->first();

    if (!$order) {
        return response()->json([
            'message' => 'الطلب غير موجود'
        ], 404);
    }

    // 📌 customer_name
    $profile = optional(optional($order->customer)->customerProfile);
    $first   = $profile->first_name ?? '';
    $last    = $profile->last_name ?? '';
    $order->customer_name = trim($first . ' ' . $last);

    // 📌 store_name
    $order->store_name = $order->store->name ?? '';

    return response()->json($order);
}

public function updateOrderStatusSeller(Request $request, $orderId)
{
    $data = $request->validate([
        'status' => 'required|string',
    ]);

    $order = Order::findOrFail($orderId);
    $status = $data['status'];

    $order->status = $status;
    $order->save();

    return response()->json([
        'message' => 'تم تحديث حالة الطلب',
        'order'   => $order->load('items.product', 'delivery'),
    ]);
}

public function getOrdersReadyForDelivery()
{
    $orders = Order::where('status', 'READY_FOR_PICKUP')
        ->whereNull('delivery_id')
        ->with([
            'address', // عنوان الزبون
            'store.addresses', // عناوين المتجر
            'customer.customerProfile',
        ])
        ->get();

    $orders->each(function ($order) {

        // اسم الزبون
        $customerProfile = optional($order->customer->customerProfile);
        $order->customer_name = trim(
            ($customerProfile->first_name ?? '') . ' ' .
            ($customerProfile->last_name ?? '')
        );

        // اسم المتجر
        $order->store_name = optional($order->store)->name;

        // ⭐ عنوان المتجر الديفولت
        $order->store_address = optional(
            $order->store->addresses->where('is_default', true)->first()
        );

    });

    return response()->json($orders);
}


public function acceptOrderByDelivery(Request $request, $orderId)
{
    $data = $request->validate([
        'delivery_id' => 'required|exists:users,id',
    ]);

    $order = Order::where('id', $orderId)
        ->where('status', 'READY_FOR_PICKUP')
        ->whereNull('delivery_id')
        ->lockForUpdate()
        ->first();

    if (!$order) {
        return response()->json([
            'message' => 'الطلب غير متاح'
        ], 409);
    }

    $order->delivery_id = $data['delivery_id'];
    $order->status = 'OUT_FOR_DELIVERY';
    $order->picked_at = now();
    $order->save();

    return response()->json([
        'message' => 'تم قبول الطلب',
        'order' => $order,
    ]);
}


public function getOrdersForDelivery(Request $request)
{
    $deliveryId = $request->query('delivery_id');

    $orders = Order::where('delivery_id', $deliveryId)
        ->where('status', 'OUT_FOR_DELIVERY')
        ->with([
            'address', // عنوان الزبون
            'store.addresses', // عناوين المتجر
            'customer.customerProfile',
        ])
        ->get();

    $orders->each(function ($order) {
        $order->store_address = optional(
            $order->store->addresses->where('is_default', true)->first()
        );
    });

    return response()->json($orders);
}







}
