<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductVariant;


class ProductVariantController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::with(['product', 'optionValues'])->get();
        return response()->json($variants);
    }

    public function show($id)
    {
        $variant = ProductVariant::with(['product', 'optionValues'])->find($id);
        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }
        return response()->json($variant);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'sku' => 'required|string|max:255|unique:product_variants,sku',
            'quantity' => 'required|integer|min:0',
            'variant_key' => 'nullable|string'
        ]);

        $variant = ProductVariant::create($data);
        return response()->json($variant, 201);
    }

    public function update(Request $request, $id)
    {
        $variant = ProductVariant::find($id);
        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        $data = $request->validate([
            'sku' => 'sometimes|string|max:255|unique:product_variants,sku,' . $id,
            'quantity' => 'sometimes|integer|min:0',
            'variant_key' => 'nullable|string'
        ]);

        $variant->update($data);
        return response()->json($variant);
    }

    public function destroy($id)
    {
        $variant = ProductVariant::find($id);
        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        $variant->delete();
        return response()->json(['message' => 'Variant deleted successfully']);
    }
}
