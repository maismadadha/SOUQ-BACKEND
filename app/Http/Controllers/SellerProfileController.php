<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SellerProfile;

class SellerProfileController extends Controller
{
    // GET /sellers
    public function index()
    {
        $sellers = SellerProfile::with(['user','mainCategory'])->get();
        return response()->json($sellers);
    }

    // GET /sellers/{user_id}
    public function show($user_id)
    {
        $seller = SellerProfile::with(['user','mainCategory'])
            ->where('user_id', $user_id)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }
        return response()->json($seller);
    }

    // PATCH/PUT /sellers/{user_id}
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
            'store_logo_url'    => 'sometimes|nullable|string',
            'store_cover_url'   => 'sometimes|nullable|string',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $seller->update($data);
        return response()->json($seller);
    }

    // DELETE /sellers/{user_id}
    public function destroy($user_id)
    {
        $seller = SellerProfile::where('user_id', $user_id)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }

        $seller->delete();
        return response()->json(['message' => 'Seller deleted successfully']);
    }
}
