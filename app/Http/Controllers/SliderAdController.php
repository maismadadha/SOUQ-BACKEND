<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SliderAd;

class SliderAdController extends Controller
{
    /**
     * عرض كل الإعلانات في السلايدر
     */
    public function index(Request $request)
    {
        // إذا activeOnly=true (افتراضي) => اعرض فقط الإعلانات الحالية (اللي وقتها ضمن المدى الزمني)
        $activeOnly = $request->boolean('activeOnly', true);
        $storeId    = $request->input('store_id');

        $query = SliderAd::with('store');

        if ($activeOnly) {
            $now = now();
            $query->where('start_date', '<=', $now)
                  ->where('end_date', '>', $now);
        }

        if (!empty($storeId)) {
            $query->where('store_id', $storeId);
        }

        $ads = $query->orderBy('start_date', 'asc')->get();

        return response()->json($ads);
    }

    /**
     * عرض إعلان واحد بالتفصيل
     */
    public function show($id)
    {
        $ad = SliderAd::with('store')->find($id);
        if (!$ad) {
            return response()->json(['message' => 'SliderAd not found'], 404);
        }
        return response()->json($ad);
    }

    /**
     * إنشاء إعلان جديد
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id'    => 'required|exists:users,id',
            'title'       => 'nullable|string|max:255',   // 👈 جديد
            'description' => 'nullable|string',           // 👈 جديد
            'image_url'   => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        $ad = SliderAd::create($data);
        return response()->json($ad, 201);
    }

    /**
     * تعديل إعلان موجود
     */
    public function update(Request $request, $id)
    {
        $ad = SliderAd::find($id);
        if (!$ad) {
            return response()->json(['message' => 'SliderAd not found'], 404);
        }

        $data = $request->validate([
            'store_id'    => 'sometimes|exists:users,id',
            'title'       => 'sometimes|nullable|string|max:255', // 👈 جديد
            'description' => 'sometimes|nullable|string',         // 👈 جديد
            'image_url'   => 'sometimes|string',
            'start_date'  => 'sometimes|date',
            'end_date'    => 'sometimes|date|after_or_equal:start_date',
        ]);

        $ad->update($data);
        return response()->json($ad);
    }

    /**
     * حذف إعلان
     */
    public function destroy($id)
    {
        $ad = SliderAd::find($id);
        if (!$ad) {
            return response()->json(['message' => 'SliderAd not found'], 404);
        }

        $ad->delete();
        return response()->json(['message' => 'SliderAd deleted successfully']);
    }
}
