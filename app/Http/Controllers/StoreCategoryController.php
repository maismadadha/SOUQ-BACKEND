<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreCategory;

class StoreCategoryController extends Controller
{
    // GET /store-categories  أو /store-categories?store_id=5
    public function index(Request $request)
    {
        $query = StoreCategory::with('store');

        // فلترة اختياريّة بالـ store_id
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        return response()->json($query->get());
    }

    // GET /store-categories/{id}
    public function show($id)
    {
        $category = StoreCategory::with(['store','products'])->find($id);

        if (!$category) {
            return response()->json(['message' => 'Store category not found'], 404);
        }

        return response()->json($category);
    }

    // POST /store-categories
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'store_id' => 'required|exists:users,id',
        ]);

        $category = StoreCategory::create($data);

        return response()->json($category, 201);
    }

    // PUT/PATCH /store-categories/{id}
    public function update(Request $request, $id)
    {
        $category = StoreCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Store category not found'], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $category->update($data);

        return response()->json($category);
    }

    // DELETE /store-categories/{id}
    public function destroy($id)
    {
        $category = StoreCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Store category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Store category deleted successfully']);
    }

    // ✅ جديد: إرجاع فئات متجر واحد فقط
    // GET /stores/{store_id}/categories
    public function categoriesByStore($storeId)
    {
        $categories = StoreCategory::where('store_id', $storeId)
            ->with('products')
            ->get();

        return response()->json($categories);
    }
}
