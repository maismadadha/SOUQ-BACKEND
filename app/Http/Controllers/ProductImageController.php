<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ProductImageController extends Controller
{
    // GET /api/products/{productId}/images
    public function index($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $images = ProductImage::where('product_id', $productId)
            ->get()
            ->map(function ($img) {

                $value = $img->image_url;

                // صورة من النت (Seeder)
                if (Str::startsWith($value, ['http://', 'https://'])) {
                    return [
                        'product_id' => $img->product_id,
                        'image_url'  => $value,
                    ];
                }

                // صورة مرفوعة من النظام
                return [
                    'product_id' => $img->product_id,
                    'image_url'  => asset('storage/' . $value),
                ];
            });

        return response()->json($images, 200);
    }

    // POST /api/products/{productId}/images
    public function store(Request $request, $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $data = $request->validate([
            'image_url' => 'required|string|max:255',
        ]);

        $exists = ProductImage::where('product_id', $productId)
            ->where('image_url', $data['image_url'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Image already exists'], 409);
        }

        $image = ProductImage::create([
            'product_id' => $productId,
            'image_url'  => $data['image_url'], // URL أو path
        ]);

        $value = $image->image_url;

        return response()->json([
            'product_id' => $image->product_id,
            'image_url'  => Str::startsWith($value, ['http://', 'https://'])
                ? $value
                : asset('storage/' . $value),
        ], 201);
    }

    // DELETE /api/products/{productId}/images
    public function destroy(Request $request, $productId)
    {
        $imageUrl = $request->input('image_url');

        if (!$imageUrl) {
            return response()->json(['message' => 'image_url is required'], 422);
        }

        // إذا URL كامل، نحوله path
        if (Str::startsWith($imageUrl, ['http://', 'https://'])) {
            $imageUrl = str_replace(asset('storage/') . '/', '', $imageUrl);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $deleted = ProductImage::where('product_id', $productId)
            ->where('image_url', $imageUrl)
            ->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        return response()->json(['message' => 'Image deleted successfully'], 200);
    }
}
