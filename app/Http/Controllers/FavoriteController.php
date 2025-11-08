<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    // GET /api/users/{user_id}/favorites
    public function indexByUser($user_id)
    {
        // نرجّع قائمة المتاجر المفضلة لليوزر (مع بيانات المتجر من users)
        $favorites = Favorite::where('user_id', (int)$user_id)
            ->with('store')   // عندك علاقة store() بالموديل
            ->get();

        return response()->json($favorites);
    }

    // POST /api/users/{user_id}/favorites  (body: { "store_id": X })
    public function storeForUser(Request $request, $user_id)
    {
        $data = $request->validate([
            'store_id' => 'required|exists:users,id|different:user_id', // ما بيفضّل حاله
            // نمرّر user_id من الراوت
        ]);

        $payload = [
            'user_id'  => (int)$user_id,
            'store_id' => (int)$data['store_id'],
        ];

        // Idempotent: لو موجود مسبقًا نرجع 200، غير هيك نضيف ونعيد 201
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
    // نحذف مباشرة عبر الشرطين (أفضل مع المفاتيح المركّبة)
    $deleted = \App\Models\Favorite::where('user_id', (int)$user_id)
        ->where('store_id', (int)$store_id)
        ->delete();

    if ($deleted === 0) {
        return response()->json(['message' => 'Favorite not found'], 404);
    }

    return response()->json(['message' => 'Removed from favorites successfully']);
}

}
