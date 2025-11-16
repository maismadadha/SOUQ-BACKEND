<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\SellerProfileController;
use App\Http\Controllers\DeliveryProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductOptionController;
use App\Http\Controllers\ProductOptionValueController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\StoreCategoryController;
use App\Http\Controllers\VariantOptionValueController;
use App\Http\Controllers\SliderAdController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderAddressController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\RoleController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Users (إنشاء المستخدم + البروفايل يتم من هنا)
Route::prefix('users')->group(function () {
    Route::get('/',        [UserController::class, 'index']);                 // ?role=customer|seller|delivery
    Route::get('/{id}',    [UserController::class, 'show']);
    Route::post('/',       [UserController::class, 'store']);                 // <-- الإنشاء الوحيد المسموح
    Route::put('/{id}',    [UserController::class, 'update']);
    Route::patch('/{id}',  [UserController::class, 'update']);                // تحديث جزئي
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

//login
Route::post('app/login-user', [UserController::class, 'loginByPhone']);

// Customer profiles
Route::prefix('customer-profiles')->group(function () {
    Route::get('/',               [CustomerProfileController::class, 'index']);
    Route::get('/{user_id}',      [CustomerProfileController::class, 'show']);
    Route::put('/{user_id}',      [CustomerProfileController::class, 'update']);
    Route::patch('/{user_id}',    [CustomerProfileController::class, 'update']);
    Route::delete('/{user_id}',   [CustomerProfileController::class, 'destroy']);
});

// Seller profiles
Route::prefix('seller-profiles')->group(function () {
    Route::get('/',               [SellerProfileController::class, 'index']);
    Route::get('/{user_id}',      [SellerProfileController::class, 'show']);
    Route::put('/{user_id}',      [SellerProfileController::class, 'update']);
    Route::patch('/{user_id}',    [SellerProfileController::class, 'update']);
    Route::delete('/{user_id}',   [SellerProfileController::class, 'destroy']);
});

// Delivery profiles
Route::prefix('delivery-profiles')->group(function () {
    Route::get('/',               [DeliveryProfileController::class, 'index']);
    Route::get('/{user_id}',      [DeliveryProfileController::class, 'show']);
    Route::put('/{user_id}',      [DeliveryProfileController::class, 'update']);
    Route::patch('/{user_id}',    [DeliveryProfileController::class, 'update']);
    Route::delete('/{user_id}',   [DeliveryProfileController::class, 'destroy']);
});

//CategoryController
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


// Store Categories عامة
Route::get('/store-categories', [StoreCategoryController::class, 'index']);
Route::get('/store-categories/{id}', [StoreCategoryController::class, 'show']);
Route::post('/store-categories', [StoreCategoryController::class, 'store']);
Route::put('/store-categories/{id}', [StoreCategoryController::class, 'update']);
Route::delete('/store-categories/{id}', [StoreCategoryController::class, 'destroy']);
// فئات متجر واحد
Route::get('/stores/{store_id}/categories', [StoreCategoryController::class, 'categoriesByStore']);

//ProductsController
Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products/{id}', [ProductsController::class, 'show']);
Route::post('/seller/products', [ProductsController::class, 'store']);
Route::patch('/seller/products/{id}', [ProductsController::class, 'update']);
Route::delete('/seller/products/{id}', [ProductsController::class, 'destroy']);
Route::get('/store-categories/{id}/products', [ProductsController::class, 'byCategory']);

//ProductImageController
Route::prefix('products/{productId}/images')->group(function () {
    Route::get('/',    [ProductImageController::class, 'index']);   // عرض صور المنتج
    Route::post('/',   [ProductImageController::class, 'store']);   // إضافة صورة عبر image_url
    Route::delete('/', [ProductImageController::class, 'destroy']); // حذف صورة عبر image_url
});

//ProductOptionController
Route::get( '/products/{productId}/options',  [ProductOptionController::class, 'indexByProduct']);
Route::post('/products/{productId}/options',  [ProductOptionController::class, 'storeForProduct']);
Route::delete('/options/{id}',                [ProductOptionController::class, 'destroy']);

//ProductOptionValueController
Route::get('/options/{optionId}/values',  [ProductOptionValueController::class, 'indexByOption']);
Route::post('/options/{optionId}/values', [ProductOptionValueController::class, 'storeForOption']);
Route::delete('/values/{id}',             [ProductOptionValueController::class, 'destroy']);

//SliderAdController
Route::prefix('slider-ads')->group(function () {
    Route::get('/', [SliderAdController::class, 'index']);      // عرض الإعلانات
    Route::get('/{id}', [SliderAdController::class, 'show']);   // عرض إعلان واحد
    Route::post('/', [SliderAdController::class, 'store']);     // إنشاء إعلان جديد
    Route::patch('/{id}', [SliderAdController::class, 'update']); // تعديل جزئي
    Route::delete('/{id}', [SliderAdController::class, 'destroy']); // حذف
});

//AddressController
Route::get(   '/users/{userId}/addresses',            [AddressController::class, 'indexByUser']);
Route::post(  '/users/{userId}/addresses',            [AddressController::class, 'storeForUser']);
Route::patch('/users/{user_id}/addresses/{id}', [AddressController::class, 'update']);
Route::delete('/users/{userId}/addresses/{id}',       [AddressController::class, 'destroyForUser']);
Route::get('/addresses', [AddressController::class, 'getAddressesForUser']);



//FavoriteController
Route::get   ('/users/{user_id}/favorites', [FavoriteController::class, 'indexByUser']);
Route::post  ('/users/{user_id}/favorites', [FavoriteController::class, 'storeForUser']);
Route::delete('/users/{user_id}/favorites/{store_id}', [FavoriteController::class, 'destroyForUser']);


// Cart / Order Actions
// إضافة منتج للسلة أو إنشاء Cart جديد
Route::post('/orders/add-to-cart', [OrderController::class, 'addToCart']);
// حذف Cart موجود (إذا الزبون وافق على حذف cart من متجر آخر)
Route::delete('/orders/cart/{cartId}', [OrderController::class, 'deleteCart']);
// تعديل كمية منتج في الـ cart
Route::put('/orders/cart/item/{itemId}', [OrderController::class, 'updateCartItem']);
// تعيين عنوان للطلب
Route::put('/orders/{orderId}/address', [OrderController::class, 'setOrderAddress']);
// تأكيد الطلب
Route::put('/orders/{orderId}/confirm', [OrderController::class, 'confirmOrder']);
// تحديث حالة الطلب (متجر أو مندوب)
Route::put('/orders/{orderId}/status', [OrderController::class, 'updateOrderStatus']);
// ==============================
// عرض الطلبات
// ==============================
// عرض كل طلبات الزبون
Route::get('/orders/customer', [OrderController::class, 'getOrdersForCustomer']);
// عرض كل طلبات المتجر
Route::get('/orders/store', [OrderController::class, 'getOrdersForStore']);
// عرض كل طلبات المندوب
Route::get('/orders/delivery', [OrderController::class, 'getOrdersForDelivery']);
Route::put('/orders/{orderId}/note', [OrderController::class, 'setOrderNote']);
Route::put('/orders/{orderId}/meta', [OrderController::class, 'setOrderMeta']);



// Order Items Routes
// عرض كل الـ order items (لو حبيت تستخدمها في admin أو debug)
Route::get('/order-items', [OrderItemController::class, 'index']);
// عرض OrderItem معين
Route::get('/order-items/{id}', [OrderItemController::class, 'show']);
// إضافة منتج جديد للـ order (السلة)
Route::post('/order-items', [OrderItemController::class, 'store']);
// تعديل منتج موجود في الـ order
Route::put('/order-items/{id}', [OrderItemController::class, 'update']);
// حذف منتج من الـ order
Route::delete('/order-items/{id}', [OrderItemController::class, 'destroy']);


// Delivery Orders Routes
// عرض كل الـ delivery orders (مثلاً للـ admin أو لوحة المندوب)
Route::get('/delivery-orders', [DeliveryOrderController::class, 'index']);
// عرض DeliveryOrder معين
Route::get('/delivery-orders/{id}', [DeliveryOrderController::class, 'show']);
// إنشاء DeliveryOrder جديد (ربط طلب بالمندوب)
Route::post('/delivery-orders', [DeliveryOrderController::class, 'store']);
// تحديث DeliveryOrder (picked_at / delivered_at)
Route::put('/delivery-orders/{id}', [DeliveryOrderController::class, 'update']);
// حذف DeliveryOrder
Route::delete('/delivery-orders/{id}', [DeliveryOrderController::class, 'destroy']);
