<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImageController extends Controller
{
    // GET /api/products/{productId}/images
    public function index($productId)
    {
        // تأكيد المنتج موجود (اختياري بس مفيد)
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $images = ProductImage::where('product_id', $productId)->get();
        return response()->json($images);
    }

    // POST /api/products/{productId}/images
    // Body JSON: { "image_url": "https://..." }
    public function store(Request $request, $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $data = $request->validate([
            'image_url' => 'required|string|max:255',
        ]);

        // إذا كانت موجودة مسبقًا كمفتاح مركّب، رح يعمل SQL error؛
        // فممكن نتحقق ببساطة:
        $exists = ProductImage::where('product_id', $productId)
                              ->where('image_url', $data['image_url'])
                              ->exists();
        if ($exists) {
            return response()->json(['message' => 'Image already exists for this product'], 409);
        }

        $image = ProductImage::create([
            'product_id' => $productId,
            'image_url'  => $data['image_url'],
        ]);

        return response()->json($image, 201);
    }

    // DELETE /api/products/{productId}/images
    // Body JSON: { "image_url": "https://..." }
    public function destroy(Request $request, $productId)
{
    try {
        // نقرأ image_url من أي مكان (query/json/form)
        $imageUrl = $request->input('image_url');
        if (!$imageUrl || !is_string($imageUrl)) {
            return response()->json(['message' => 'image_url is required'], 422);
        }
        $imageUrl = urldecode(trim($imageUrl));

        // تأكد المنتج موجود (اختياري بس منظم)
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // الحذف عبر الاستعلام مباشرة (مش عبر model instance)
        $deleted = ProductImage::where('product_id', $productId)
            ->where('image_url', $imageUrl)
            ->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        return response()->json(['message' => 'Image deleted successfully'], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Failed to delete image',
            'error'   => $e->getMessage()
        ], 422);
    }
}

}
