<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductOptionValue;

class ProductOptionValueController extends Controller
{
    public function index()
    {
        $values = ProductOptionValue::with('option')->get();
        return response()->json($values);
    }

    public function show($id)
    {
        $value = ProductOptionValue::with('option')->find($id);
        if (!$value) {
            return response()->json(['message' => 'Value not found'], 404);
        }
        return response()->json($value);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'option_id' => 'required|exists:product_options,id',
            'value' => 'required|string',
            'label' => 'nullable|string',
            'price_delta' => 'nullable|numeric',
            'sort_order' => 'nullable|integer'
        ]);

        $value = ProductOptionValue::create($data);
        return response()->json($value, 201);
    }

    public function update(Request $request, $id)
    {
        $value = ProductOptionValue::find($id);
        if (!$value) {
            return response()->json(['message' => 'Value not found'], 404);
        }

        $data = $request->validate([
            'value' => 'sometimes|string',
            'label' => 'nullable|string',
            'price_delta' => 'nullable|numeric',
            'sort_order' => 'nullable|integer'
        ]);

        $value->update($data);
        return response()->json($value);
    }

    public function destroy($id)
    {
        $value = ProductOptionValue::find($id);
        if (!$value) {
            return response()->json(['message' => 'Value not found'], 404);
        }

        $value->delete();
        return response()->json(['message' => 'Value deleted successfully']);
    }
}
