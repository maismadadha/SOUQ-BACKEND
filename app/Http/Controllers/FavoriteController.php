<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    // GET /api/users/{user_id}/favorites
    public function indexByUser($user_id)
    {
        $favorites = Favorite::with(['store.sellerProfile.mainCategory'])
            ->where('user_id', (int)$user_id)
            ->get()
            ->map(function ($fav) {
                $store   = $fav->store;
                $profile = $store?->sellerProfile;

                return [
                    'user_id'          => $fav->user_id,
                    'store_id'         => $fav->store_id,

                    // من جدول users (seller)
                    'store_email'      => $store->email ?? null,
                    'store_phone'      => $store->phone ?? null,

                    // من seller_profiles
                    'store_name'       => $profile->name ?? null,
                    'store_description'=> $profile->store_description ?? null,
                    'store_logo_url'   => $profile->store_logo_url ?? null,
                    'store_cover_url'  => $profile->store_cover_url ?? null,
                    'main_category'    => $profile?->mainCategory?->name,
                ];
            });

        return response()->json($favorites->values());
    }

    // POST /api/users/{user_id}/favorites  (body: { "store_id": X })
    public function storeForUser(Request $request, $user_id)
    {
        $data = $request->validate([
            'store_id' => 'required|exists:users,id',
        ]);

        $payload = [
            'user_id'  => (int)$user_id,
            'store_id' => (int)$data['store_id'],
        ];

        $exists = Favorite::where($payload)->exists();
        if ($exists) {
            return response()->json(['message' => 'Already in favorites'], 200);
        }

        Favorite::create($payload);

        return response()->json(['message' => 'Added to favorites successfully'], 201);
    }

    // DELETE /api/users/{user_id}/favorites/{store_id}
    public function destroyForUser($user_id, $store_id)
    {
        $deleted = Favorite::where('user_id', (int)$user_id)
            ->where('store_id', (int)$store_id)
            ->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'Favorite not found'], 404);
        }

        return response()->json(['message' => 'Removed from favorites successfully']);
    }
}
