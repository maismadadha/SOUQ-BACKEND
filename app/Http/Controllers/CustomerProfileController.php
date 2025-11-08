<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerProfile;

class CustomerProfileController extends Controller
{
    // GET /customers
    public function index()
    {
        $profiles = CustomerProfile::with('user')->get();
        return response()->json($profiles);
    }

    // GET /customers/{user_id}
    public function show($user_id)
    {
        $profile = CustomerProfile::with('user')
            ->where('user_id', $user_id)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }

    // PATCH/PUT /customers/{user_id}
    public function update(Request $request, $user_id)
    {
        $profile = CustomerProfile::where('user_id', $user_id)->first();
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $data = $request->validate([
            'first_name' => 'sometimes|nullable|string',
            'last_name'  => 'sometimes|nullable|string',
        ]);

        $profile->update($data);
        return response()->json($profile);
    }

    // DELETE /customers/{user_id}
    public function destroy($user_id)
    {
        $profile = CustomerProfile::where('user_id', $user_id)->first();
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $profile->delete();
        return response()->json(['message' => 'Profile deleted successfully']);
    }
}
