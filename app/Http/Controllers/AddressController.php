<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
public function indexByUser($userId)
{
    $addresses = Address::where('user_id', (int)$userId)
        ->orderBy('id') // أو orderByDesc('id') إذا بدك الأحدث فوق
        ->get();

    return response()->json($addresses);
}


    /**
     * POST /api/users/{userId}/addresses
     * إضافة عنوان (أول عنوان يصير Default تلقائي)
     */
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

        // ⭐ هل عنده عنوان ديفولت؟
        $hasDefault = Address::where('user_id', $userId)
            ->where('is_default', true)
            ->exists();

        // ⭐ إذا لا → هذا أول عنوان يصير Default
        if (!$hasDefault) {
            $data['is_default'] = true;
        }

        $address = Address::create($data);

        return response()->json([
            'message' => 'Address created successfully',
            'data'    => $address
        ], 201);
    }

    /**
     * PATCH /api/users/{userId}/addresses/{id}
     * تعديل عنوان
     */
    public function update(Request $request, $userId, $id)
    {
        $address = Address::where('id', $id)
            ->where('user_id', (int)$userId)
            ->first();

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $validated = $request->validate([
            'city_name'       => 'sometimes|string',
            'street'          => 'sometimes|string',
            'building_number' => 'sometimes|integer',
            'address_note'    => 'nullable|string',
            'latitude'        => 'sometimes|numeric',
            'longitude'       => 'sometimes|numeric',
            'address_name'    => 'sometimes|string',
        ]);

        $address->update($validated);

        return response()->json([
            'message' => 'Address updated successfully',
            'data'    => $address
        ]);
    }

    /**
     * DELETE /api/users/{userId}/addresses/{id}
     * حذف عنوان
     */
    public function destroyForUser($userId, $id)
    {
        $address = Address::where('id', $id)
            ->where('user_id', (int)$userId)
            ->first();

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $wasDefault = $address->is_default;

        $address->delete();

        // ⭐ إذا انحذف الديفولت → عيّن أول عنوان ديفولت
        if ($wasDefault) {
            $nextAddress = Address::where('user_id', $userId)
                ->orderBy('id')
                ->first();

            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        return response()->json(['message' => 'Address deleted successfully']);
    }

    /**
     * PATCH /api/users/{userId}/addresses/{addressId}/default
     * تعيين عنوان كـ Default
     */
    public function setDefault($userId, $addressId)
    {
        // شيل الديفولت عن الكل
        Address::where('user_id', $userId)
            ->update(['is_default' => false]);

        $address = Address::where('id', $addressId)
            ->where('user_id', (int)$userId)
            ->first();

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $address->update(['is_default' => true]);

        return response()->json([
            'message' => 'Default address updated',
            'data'    => $address
        ]);
    }
}
