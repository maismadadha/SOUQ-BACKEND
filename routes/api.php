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


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//usercontroller
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);


//customercontroller
Route::get('/customer-profiles', [CustomerProfileController::class, 'index']);
Route::get('/customer-profiles/{user_id}', [CustomerProfileController::class, 'show']);
Route::post('/customer-profiles', [CustomerProfileController::class, 'store']);
Route::put('/customer-profiles/{user_id}', [CustomerProfileController::class, 'update']);
Route::delete('/customer-profiles/{user_id}', [CustomerProfileController::class, 'destroy']);

//sellercontroller
Route::get('/seller-profiles', [SellerProfileController::class, 'index']);
Route::get('/seller-profiles/{id}', [SellerProfileController::class, 'show']);
Route::post('/seller-profiles', [SellerProfileController::class, 'store']);
Route::put('/seller-profiles/{id}', [SellerProfileController::class, 'update']);
Route::delete('/seller-profiles/{id}', [SellerProfileController::class, 'destroy']);

//DeliveryController
Route::get('/delivery-profiles', [DeliveryProfileController::class, 'index']);
Route::get('/delivery-profiles/{user_id}', [DeliveryProfileController::class, 'show']);
Route::post('/delivery-profiles', [DeliveryProfileController::class, 'store']);
Route::put('/delivery-profiles/{user_id}', [DeliveryProfileController::class, 'update']);
Route::delete('/delivery-profiles/{user_id}', [DeliveryProfileController::class, 'destroy']);

//CategoryController
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

//ProductsController
Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products/{id}', [ProductsController::class, 'show']);
Route::post('/products', [ProductsController::class, 'store']);
Route::put('/products/{id}', [ProductsController::class, 'update']);
Route::delete('/products/{id}', [ProductsController::class, 'destroy']);

//ProductImageController
Route::get('/product-images', [ProductImageController::class, 'index']);
Route::get('/product-images/{id}', [ProductImageController::class, 'show']);
Route::post('/product-images', [ProductImageController::class, 'store']);
Route::put('/product-images/{id}', [ProductImageController::class, 'update']);
Route::delete('/product-images/{id}', [ProductImageController::class, 'destroy']);

//ProductOptionController
Route::get('/product-options', [ProductOptionController::class, 'index']);
Route::get('/product-options/{id}', [ProductOptionController::class, 'show']);
Route::post('/product-options', [ProductOptionController::class, 'store']);
Route::put('/product-options/{id}', [ProductOptionController::class, 'update']);
Route::delete('/product-options/{id}', [ProductOptionController::class, 'destroy']);

//ProductOptionValueController
Route::get('/product-option-values', [ProductOptionValueController::class, 'index']);
Route::get('/product-option-values/{id}', [ProductOptionValueController::class, 'show']);
Route::post('/product-option-values', [ProductOptionValueController::class, 'store']);
Route::put('/product-option-values/{id}', [ProductOptionValueController::class, 'update']);
Route::delete('/product-option-values/{id}', [ProductOptionValueController::class, 'destroy']);

//ProductVariantController
Route::get('/variants', [ProductVariantController::class, 'index']);
Route::get('/variants/{id}', [ProductVariantController::class, 'show']);
Route::post('/variants', [ProductVariantController::class, 'store']);
Route::put('/variants/{id}', [ProductVariantController::class, 'update']);
Route::delete('/variants/{id}', [ProductVariantController::class, 'destroy']);

//StoreCategoryController
Route::get('/store-categories', [StoreCategoryController::class, 'index']);
Route::get('/store-categories/{id}', [StoreCategoryController::class, 'show']);
Route::post('/store-categories', [StoreCategoryController::class, 'store']);
Route::put('/store-categories/{id}', [StoreCategoryController::class, 'update']);
Route::delete('/store-categories/{id}', [StoreCategoryController::class, 'destroy']);

//VariantOptionValueController
Route::get('/variant-option-values', [VariantOptionValueController::class, 'index']);
Route::get('/variant-option-values/{id}', [VariantOptionValueController::class, 'show']);
Route::post('/variant-option-values', [VariantOptionValueController::class, 'store']);
Route::put('/variant-option-values/{id}', [VariantOptionValueController::class, 'update']);
Route::delete('/variant-option-values/{id}', [VariantOptionValueController::class, 'destroy']);

//SliderAdController
Route::get('/slider-ads', [SliderAdController::class, 'index']);
Route::get('/slider-ads/{id}', [SliderAdController::class, 'show']);
Route::post('/slider-ads', [SliderAdController::class, 'store']);
Route::put('/slider-ads/{id}', [SliderAdController::class, 'update']);
Route::delete('/slider-ads/{id}', [SliderAdController::class, 'destroy']);
