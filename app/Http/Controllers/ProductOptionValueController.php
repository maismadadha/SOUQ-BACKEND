<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;

class ProductOptionValueController extends Controller
{
    // ✅ 1) عرض كل القيم لخيار معيّن
    // GET /api/options/{optionId}/values
    public function indexByOption($optionId)
    {
        $option = ProductOption::find($optionId);
        if (!$option) return response()->json(['message' => 'Option not found'], 404);

        $values = ProductOptionValue::where('option_id', $optionId)
                    ->orderBy('sort_order')
                    ->get();

        return response()->json($values);
    }

    // ✅ 2) إضافة قيمة لخيار (auto sort_order)
    // POST /api/options/{optionId}/values
    public function storeForOption(Request $request, $optionId)
    {
        $option = ProductOption::find($optionId);
        if (!$option) return response()->json(['message' => 'Option not found'], 404);

        $data = $request->validate([
            'value'        => 'required|string|max:100',
            'label'        => 'nullable|string|max:100',
            'price_delta'  => 'nullable|numeric',
        ]);

        // تأكد ما تكرر نفس القيمة لنفس الخيار
        if (ProductOptionValue::where('option_id', $optionId)
                ->where('value', $data['value'])
                ->exists()) {
            return response()->json(['message' => 'Value already exists for this option'], 409);
        }

        // auto sort_order
        $max = ProductOptionValue::where('option_id', $optionId)->max('sort_order');
        $data['sort_order'] = is_null($max) ? 0 : ($max + 1);
        $data['label'] = $data['label'] ?? $data['value'];
        $data['price_delta'] = $data['price_delta'] ?? 0;
        $data['option_id'] = $optionId;

        $value = ProductOptionValue::create($data);

        return response()->json($value, 201);
    }

    // ✅ 3) حذف قيمة
    // DELETE /api/values/{id}
    public function destroy($id)
    {
        $value = ProductOptionValue::find($id);
        if (!$value) return response()->json(['message' => 'Value not found'], 404);

        $value->delete();
        return response()->json(['message' => 'Value deleted successfully']);
    }
}
