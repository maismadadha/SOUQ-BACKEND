<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VariantOptionValue;

class VariantOptionValueController extends Controller
{
    public function index()
    {
        $values = VariantOptionValue::with(['variant', 'option', 'value'])->get();
        return response()->json($values);
    }

    
    public function show($id)
    {
        $value = VariantOptionValue::with(['variant', 'option', 'value'])->find($id);

        if (!$value) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($value);
    }


    public function store(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'product_option_id' => 'required|exists:product_options,id',
            'product_option_value_id' => 'required|exists:product_option_values,id',
        ]);

        $value = VariantOptionValue::create($request->all());

        return response()->json($value, 201);
    }


    public function update(Request $request, $id)
    {
        $value = VariantOptionValue::find($id);

        if (!$value) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $request->validate([
            'variant_id' => 'sometimes|exists:product_variants,id',
            'product_option_id' => 'sometimes|exists:product_options,id',
            'product_option_value_id' => 'sometimes|exists:product_option_values,id',
        ]);

        $value->update($request->all());

        return response()->json($value);
    }


    public function destroy($id)
    {
        $value = VariantOptionValue::find($id);

        if (!$value) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $value->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
