<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\SellerProfile;

class SellerProfileController extends Controller
{

public function index(Request $request)
{
    $query = SellerProfile::with(['user', 'mainCategory']);

    // فلترة حسب التصنيف (اختياري)
    if ($request->filled('main_category_id')) {
        $query->where('main_category_id', $request->main_category_id);
    }

    return response()->json($query->get());
}

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'image'     => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'seller_id' => 'required|integer|exists:seller_profiles,user_id',
        ]);

        $seller = SellerProfile::where('user_id', $request->seller_id)->firstOrFail();

        // 🔴 حذف القديم إن وجد
        $oldPath = $seller->getRawOriginal('store_logo_url');
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // 🟢 رفع الجديد
        $path = $request->file('image')->store('stores/logos', 'public');

        // 🟢 تحديث DB (نخزن PATH فقط)
        $seller->update([
            'store_logo_url' => $path
        ]);

        return response()->json([
            'message' => 'Logo updated successfully',
            'url'     => asset('storage/' . $path)
        ], 201);
    }

    /* =======================
     *  رفع غلاف المتجر
     * ======================= */
    public function uploadCover(Request $request)
    {
        $request->validate([
            'image'     => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'seller_id' => 'required|integer|exists:seller_profiles,user_id',
        ]);

        $seller = SellerProfile::where('user_id', $request->seller_id)->firstOrFail();

        // 🔴 حذف الغلاف القديم
        $oldPath = $seller->getRawOriginal('store_cover_url');
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // 🟢 رفع الجديد
        $path = $request->file('image')->store('stores/covers', 'public');

        // 🟢 تحديث DB
        $seller->update([
            'store_cover_url' => $path
        ]);

        return response()->json([
            'message' => 'Cover updated successfully',
            'url'     => asset('storage/' . $path)
        ], 201);
    }

    /* =======================
     *  عرض متجر
     * ======================= */
    public function show($user_id)
    {
        $seller = SellerProfile::with(['user','mainCategory'])
            ->where('user_id', $user_id)
            ->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }

        return response()->json($seller);
    }

    /* =======================
     *  تحديث بيانات المتجر
     * ======================= */
    public function update(Request $request, $user_id)
    {
        $seller = SellerProfile::where('user_id', $user_id)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }

        $data = $request->validate([
            'name'              => 'sometimes|string|max:255',
            'password'          => 'sometimes|string|min:6',
            'store_description' => 'sometimes|nullable|string',
            'main_category_id'  => 'sometimes|exists:categories,id',
            'preparation_days'  => 'sometimes|integer|min:0',
            'preparation_hours' => 'sometimes|integer|min:0',
            'delivery_price'    => 'sometimes|numeric|min:0',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $seller->update($data);

        return response()->json($seller);
    }

    /* =======================
 *  البحث عن متجر
 * ======================= */
public function search(Request $request)
{
    $q = $request->query('q');

    if (!$q) {
        return response()->json([]);
    }

    $sellers = SellerProfile::with(['user', 'mainCategory'])
        ->where('name', 'LIKE', "%{$q}%")
        ->get();

    return response()->json($sellers);
}


}
