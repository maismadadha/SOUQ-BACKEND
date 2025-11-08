<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    // GET /api/users/{userId}/addresses
    public function indexByUser($userId)
    {
        $addresses = Address::where('user_id', (int)$userId)
            ->orderByDesc('id')
            ->get();

        return response()->json($addresses);
    }

    // POST /api/users/{userId}/addresses
    public function storeForUser(Request $request, $userId)
    {
        $data = $request->validate([
            'city_name'       => 'required|string',
            'street'          => 'required|string',
            'building_number' => 'required|integer',
            'address_note'    => 'nullable|string',
            'latitude'        => 'required|numeric',
            'longitude'       => 'required|numeric',
            'address_name'    => 'required|string',
        ]);

        $data['user_id'] = (int) $userId;

        $address = Address::create($data);

        return response()->json([
            'message' => 'Address created successfully',
            'data'    => $address
        ], 201);
    }

    // PATCH /api/users/{userId}/addresses/{id}
 public function update(Request $request, $user_id, $id)
{
    $address = Address::where('id', $id)
                      ->where('user_id', $user_id)
                      ->first();

    if (!$address) {
        return response()->json(['message' => 'Address not found'], 404);
    }

    $validated = $request->validate([
        'city_name' => 'sometimes|string',
        'street' => 'sometimes|string',
        'building_number' => 'sometimes|integer',
        'address_note' => 'nullable|string',
        'latitude' => 'sometimes|numeric',
        'longitude' => 'sometimes|numeric',
        'address_name' => 'sometimes|string',
    ]);

    $address->update($validated);

    return response()->json([
        'message' => 'Address updated successfully',
        'data' => $address
    ]);
}

    // DELETE /api/users/{userId}/addresses/{id}
    public function destroyForUser($userId, $id)
    {
        $address = Address::where('id', $id)
            ->where('user_id', (int)$userId)
            ->first();

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
