<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryProfile;

class DeliveryProfileController extends Controller
{
public function show($user_id)
{
    $delivery = DeliveryProfile::where('user_id', $user_id)->firstOrFail();
    return response()->json($delivery);
}

public function update(Request $request, $user_id)
{
    $delivery = DeliveryProfile::where('user_id', $user_id)->firstOrFail();

    $request->validate([
        'first_name' => 'sometimes|string',
        'last_name' => 'sometimes|string',
        'password' => 'sometimes|string',
        'profile_pic_url' => 'nullable|string',
    ]);

    $delivery->update($request->all());

    return response()->json([
        'message' => 'DeliveryProfile updated successfully',
        'data' => $delivery
    ]);
}

public function destroy($user_id)
{
    $delivery = DeliveryProfile::where('user_id', $user_id)->firstOrFail();
    $delivery->delete();

    return response()->json([
        'message' => 'DeliveryProfile deleted successfully'
    ]);
}

}
