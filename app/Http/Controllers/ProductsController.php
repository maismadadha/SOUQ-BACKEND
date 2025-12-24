<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;

class ProductsController extends Controller
{
    // ✅ GET /products?store_id=&store_category_id=
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('store_category_id')) {
            $query->where('store_category_id', $request->store_category_id);
        }

        // نجيب أول صورة لكل منتج فقط
        $products = $query->with([
            'images' => function ($q) {
                $q->limit(1);
            }
        ])->get();

        // نضيف cover_image بدل مصفوفة images
        $products->transform(function ($product) {
            $product->cover_image = $product->images->first()->image_url ?? null;
            unset($product->images); // نخفي الصور من index فقط
            return $product;
        });

        return response()->json($products);
    }


    // ✅ GET /products/{id}
   public function show($id)
{
    $product = Product::with([
        'store',
        'storeCategory',
        'images' => function ($q) {
            $q->limit(1);
        }
    ])->find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    $product->cover_image = $product->images->first()->image_url ?? null;
    unset($product->images);

    return response()->json($product);
}



    // ✅ POST /seller/products
  public function store(Request $request)
{
    // 1) Validation مرن
    $data = $request->validate([
        'store_id'          => 'required|exists:users,id',
        'store_category_id' => 'required|exists:store_categories,id',
        'name'              => 'required|string|max:255',
        'description'       => 'nullable|string',
        'price'             => 'required|numeric|min:0',
        'preparation_time'  => 'nullable|date_format:H:i:s',
        'attributes'        => 'nullable', // نقبل مصفوفة أو نص JSON
    ]);

    // 2) قيَم افتراضية للحُقول الغير nullable بالـDB
    // description عندك ليس nullable في الميجريشن -> حطّيها "" إن ما انبعتت
    if (!array_key_exists('description', $data) || $data['description'] === null || $data['description'] === '') {
        $data['description'] = '';
    }

    // preparation_time عندك time بدون nullable -> حطّي "00:00:00" إن ما انبعتت
    if (!array_key_exists('preparation_time', $data) || $data['preparation_time'] === null || $data['preparation_time'] === '') {
        $data['preparation_time'] = '00:00:00';
    }

    // 3) attributes: نقبل نص JSON أو مصفوفة ونحوّلها
    if (array_key_exists('attributes', $data)) {
        if (is_string($data['attributes'])) {
            $decoded = json_decode($data['attributes'], true);
            $data['attributes'] = $decoded ?: null;
        } elseif (!is_array($data['attributes'])) {
            $data['attributes'] = null;
        }
    }

    try {
        $product = Product::create($data);
        return response()->json($product, 201);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Failed to create product',
            'error'   => $e->getMessage()
        ], 422);
    }
}


    // ✅ PATCH /seller/products/{id}
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $data = $request->validate([
            'store_category_id' => 'sometimes|exists:store_categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'preparation_time' => 'nullable|date_format:H:i:s',
            'attributes' => 'nullable|array',
        ]);

        $product->update($data);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }


    // ✅ DELETE /seller/products/{id}
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function byCategory($id, Request $request)
{
    $query = Product::query()->where('store_category_id', $id);

    // اختياري: تقييد بمتجر
    if ($request->filled('store_id')) {
        $query->where('store_id', $request->store_id);
    }

    $products = $query->with(['images' => fn($q) => $q->limit(1)])->get();

    $products->transform(function ($p) {
        $p->cover_image = $p->images->first()->image_url ?? null;
        unset($p->images);
        return $p;
    });

    return response()->json($products);
}

public function storeFull(Request $request)
{
    $data = $request->validate([
        'store_id' => 'required|integer',
        'store_category_id' => 'required|integer',
        'name' => 'required|string',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'preparation_time' => 'nullable|string',
        'options' => 'nullable|array',
        'options.*.name' => 'required|string',
        'options.*.label' => 'nullable|string',
        'options.*.required' => 'boolean',
        'options.*.selection' => 'in:single,multi',
        'options.*.affects_variant' => 'boolean',
        'options.*.values' => 'nullable|array',
        'options.*.values.*.value' => 'required|string',
        'options.*.values.*.label' => 'nullable|string',
        'options.*.values.*.price_delta' => 'numeric'
    ]);

    return DB::transaction(function () use ($data) {

        // 1️⃣ إنشاء المنتج
        $product = Product::create([
            'store_id' => $data['store_id'],
            'store_category_id' => $data['store_category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'preparation_time' => $data['preparation_time'] ?? '00:00:00'
        ]);

        // 2️⃣ إنشاء الخيارات + القيم
        if (!empty($data['options'])) {
            foreach ($data['options'] as $i => $opt) {

                $option = ProductOption::create([
                    'product_id' => $product->id,
                    'name' => $opt['name'],
                    'label' => $opt['label'] ?? $opt['name'],
                    'required' => $opt['required'] ?? true,
                    'selection' => $opt['selection'] ?? 'single',
                    'affects_variant' => $opt['affects_variant'] ?? true,
                    'sort_order' => $i
                ]);

                if (!empty($opt['values'])) {
                    foreach ($opt['values'] as $j => $val) {
                        ProductOptionValue::create([
                            'option_id' => $option->id,
                            'value' => $val['value'],
                            'label' => $val['label'] ?? $val['value'],
                            'price_delta' => $val['price_delta'] ?? 0,
                            'sort_order' => $j
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load('options.values')
        ], 201);
    });
}

}
