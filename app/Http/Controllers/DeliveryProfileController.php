<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\DeliveryProfile;

class DeliveryProfileController extends Controller
{
    // GET /deliveries
    public function index()
    {
        $deliveries = DeliveryProfile::with('user')->get();
        return response()->json($deliveries);
    }

    // GET /deliveries/{user_id}
    public function show($user_id)
    {
        $delivery = DeliveryProfile::with('user')
            ->where('user_id', $user_id)->first();

        if (!$delivery) {
            return response()->json(['message' => 'DeliveryProfile not found'], 404);
        }

        return response()->json($delivery);
    }

    // PATCH/PUT /deliveries/{user_id}
    public function update(Request $request, $user_id)
    {
        $delivery = DeliveryProfile::where('user_id', $user_id)->first();
        if (!$delivery) {
            return response()->json(['message' => 'DeliveryProfile not found'], 404);
        }

        $data = $request->validate([
            'first_name'     => 'sometimes|nullable|string',
            'last_name'      => 'sometimes|nullable|string',
            'password'       => 'sometimes|string|min:6',
            'profile_pic_url'=> 'sometimes|nullable|string',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $delivery->update($data);
        return response()->json($delivery);
    }

    // DELETE /deliveries/{user_id}
    public function destroy($user_id)
    {
        $delivery = DeliveryProfile::where('user_id', $user_id)->first();
        if (!$delivery) {
            return response()->json(['message' => 'DeliveryProfile not found'], 404);
        }

        $delivery->delete();
        return response()->json(['message' => 'DeliveryProfile deleted successfully']);
    }
}
