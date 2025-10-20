<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductOption;

class ProductOptionController extends Controller
{
     public function index()
    {
        $options = ProductOption::with(['product', 'values'])->get();
        return response()->json($options);
    }

   
    public function show($id)
    {
        $option = ProductOption::with('values')->find($id);
        if (!$option) {
            return response()->json(['message' => 'Option not found'], 404);
        }
        return response()->json($option);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'label' => 'nullable|string',
            'selection' => 'nullable|string',
            'required' => 'boolean',
            'sort_order' => 'integer',
            'affects_variant' => 'boolean'
        ]);

        $option = ProductOption::create($data);
        return response()->json($option, 201);
    }


    public function update(Request $request, $id)
    {
        $option = ProductOption::find($id);
        if (!$option) {
            return response()->json(['message' => 'Option not found'], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'label' => 'nullable|string',
            'selection' => 'nullable|string',
            'required' => 'boolean',
            'sort_order' => 'integer',
            'affects_variant' => 'boolean'
        ]);

        $option->update($data);
        return response()->json($option);
    }


    public function destroy($id)
    {
        $option = ProductOption::find($id);
        if (!$option) {
            return response()->json(['message' => 'Option not found'], 404);
        }

        $option->delete();
        return response()->json(['message' => 'Option deleted successfully']);
    }
}
