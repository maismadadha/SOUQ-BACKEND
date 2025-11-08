<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductOption;

class ProductOptionController extends Controller
{
    // 1) عرض خيارات منتج معيّن (مرتَّبة)
    // GET /api/products/{productId}/options
    public function indexByProduct($productId)
    {
        $product = Product::find($productId);
        if (!$product) return response()->json(['message' => 'Product not found'], 404);

        // نرجّع الخيارات + قيمها (values) مرتبة حسب sort_order
        $options = ProductOption::where('product_id', $productId)
                    ->with('values')
                    ->orderBy('sort_order')
                    ->get();

        return response()->json($options);
    }

    // 2) إضافة خيار لمنتج (مع auto sort_order)
    // POST /api/products/{productId}/options
    public function storeForProduct(Request $request, $productId)
{
    $product = \App\Models\Product::find($productId);
    if (!$product) return response()->json(['message' => 'Product not found'], 404);

    $data = $request->validate([
        'name'            => 'required|string|max:100',
        'label'           => 'nullable|string|max:100',
        'selection'       => 'nullable|in:single,multi',
        'required'        => 'boolean',
        'sort_order'      => 'nullable|integer',
        'affects_variant' => 'boolean',
    ]);

    // منع تكرار الاسم داخل نفس المنتج
    $exists = \App\Models\ProductOption::where('product_id', $productId)
                ->where('name', $data['name'])
                ->exists();
    if ($exists) {
        return response()->json(['message' => 'Option name already exists for this product'], 409);
    }

    // تعبئة افتراضيات
    $data['product_id']      = (int)$productId;
    $data['selection']       = $data['selection']       ?? 'single';
    $data['label']           = $data['label']           ?? $data['name'];
    $data['required']        = $data['required']        ?? true;
    $data['affects_variant'] = $data['affects_variant'] ?? true;

    // Auto sort_order
    if (!array_key_exists('sort_order', $data) || $data['sort_order'] === null) {
        $max = \App\Models\ProductOption::where('product_id', $productId)->max('sort_order');
        $data['sort_order'] = is_null($max) ? 0 : ($max + 1);
    }

    try {
        $option = \App\Models\ProductOption::create($data);
        return response()->json($option->load('values'), 201);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Failed to create option',
            'error'   => $e->getMessage()
        ], 422);
    }
}


    // 3) حذف خيار
    // DELETE /api/options/{id}
    public function destroy($id)
    {
        $option = ProductOption::find($id);
        if (!$option) return response()->json(['message' => 'Option not found'], 404);

        $option->delete(); // قيمه بتنمسح بكاسكيد لو FK مضبوط
        return response()->json(['message' => 'Option deleted successfully']);
    }
}
