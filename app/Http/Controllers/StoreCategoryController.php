<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreCategory;


class StoreCategoryController extends Controller
{
     public function index()
    {
        $categories = StoreCategory::with('store')->get();
        return response()->json($categories);
    }


    public function show($id)
    {
        $category = StoreCategory::with(['store', 'products'])->find($id);
        if (!$category) {
            return response()->json(['message' => 'Store category not found'], 404);
        }
        return response()->json($category);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'store_id' => 'required|exists:users,id'
        ]);

        $category = StoreCategory::create($data);
        return response()->json($category, 201);
    }


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

   
    public function destroy($id)
    {
        $category = StoreCategory::find($id);
        if (!$category) {
            return response()->json(['message' => 'Store category not found'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Store category deleted successfully']);
    }
}
