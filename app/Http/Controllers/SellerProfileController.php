<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SellerProfile;



class SellerProfileController extends Controller
{
     public function index()
{
        $sellers = SellerProfile::with(['user', 'mainCategory'])->get();
        return response()->json($sellers);
}

    public function show($id)
{
        $seller = SellerProfile::with(['user', 'mainCategory'])->find($id);

        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }

        return response()->json($seller);
}

    public function store(Request $request)
{
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'store_description' => 'nullable|string',
            'main_category_id' => 'required|exists:categories,id',
            'store_logo_url' => 'nullable|string',
            'store_cover_url' => 'nullable|string',
        ]);

        $seller = SellerProfile::create($data);

        return response()->json($seller, 201);
}

    public function update(Request $request, $id)
{
        $seller = SellerProfile::find($id);

        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }

        $data = $request->validate([
            'password' => 'sometimes|string|min:6',
            'name' => 'sometimes|string|max:255',
            'store_description' => 'nullable|string',
            'main_category_id' => 'sometimes|exists:categories,id',
            'store_logo_url' => 'nullable|string',
            'store_cover_url' => 'nullable|string',
        ]);

        $seller->update($data);

        return response()->json($seller);
}

    public function destroy($id)
{
        $seller = SellerProfile::find($id);

        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }

        $seller->delete();

        return response()->json(['message' => 'Seller deleted successfully']);
}
}
