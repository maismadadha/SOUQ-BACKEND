<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    public function index()
{
    $products = Product::with(['store', 'storeCategory', 'images', 'variants', 'options.values'])->get();
    return response()->json($products);
}

    public function show($id)
{
    $product = Product::with(['store', 'storeCategory', 'images', 'variants', 'options.values'])->find($id);
    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }
    return response()->json($product);
}

    public function store(Request $request)
{
    $data = $request->validate([
        'store_id' => 'required|exists:users,id',
        'store_categories_id' => 'required|exists:store_categories,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'preparation_time' => 'nullable|date_format:H:i:s',
        'attributes' => 'nullable|json',
    ]);

    $product = Product::create($data);

    return response()->json($product, 201);
}

    public function update(Request $request, $id)
{
    $product = Product::find($id);
    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    $data = $request->validate([
        'store_categories_id' => 'sometimes|exists:store_categories,id',
        'name' => 'sometimes|string|max:255',
        'description' => 'nullable|string',
        'price' => 'sometimes|numeric',
        'preparation_time' => 'nullable|date_format:H:i:s',
        'attributes' => 'nullable|json',
    ]);

    $product->update($data);

    return response()->json($product);
}

    public function destroy($id)
{
    $product = Product::find($id);
    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    $product->delete();

    return response()->json(['message' => 'Product deleted successfully']);
}

}
