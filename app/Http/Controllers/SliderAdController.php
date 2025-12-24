<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SliderAd;
use Illuminate\Support\Facades\Storage;


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
    $request->validate([
        'store_id'    => 'required|exists:users,id',
        'title'       => 'nullable|string|max:255',
        'description' => 'nullable|string',

        // 👇 واحد منهم لازم يكون موجود
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        'image_url'   => 'nullable|url',

        'start_date'  => 'required|date',
        'end_date'    => 'required|date|after_or_equal:start_date',
    ]);

    // 🔴 تحقق منطقي
    if (!$request->hasFile('image') && !$request->filled('image_url')) {
        return response()->json([
            'message' => 'You must provide either image file or image_url'
        ], 422);
    }

    if ($request->hasFile('image') && $request->filled('image_url')) {
        return response()->json([
            'message' => 'Provide only one: image OR image_url'
        ], 422);
    }

    // 🟢 تحديد الصورة
    if ($request->hasFile('image')) {
        // صورة من التلفون
        $path = $request->file('image')->store('slider_ads', 'public');
        $imagePath = $path;
        $fullUrl   = asset('storage/' . $path);
    } else {
        // رابط من النت (Seeder / Postman)
        $imagePath = $request->image_url;
        $fullUrl   = $request->image_url;
    }

    // 🟢 إنشاء الإعلان
    $ad = SliderAd::create([
        'store_id'    => $request->store_id,
        'title'       => $request->title,
        'description' => $request->description,
        'image_url'   => $imagePath, // path أو URL
        'start_date'  => $request->start_date,
        'end_date'    => $request->end_date,
    ]);

    return response()->json([
        'message'   => 'Slider ad created successfully',
        'ad'        => $ad,
        'image_url'=> $fullUrl,
    ], 201);
}



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
