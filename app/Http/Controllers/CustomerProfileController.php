<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerProfile;


class CustomerProfileController extends Controller
{
    public function index()
{
        $profiles = CustomerProfile::all();
        return response()->json($profiles);
}

     public function show($user_id)
{
        $profile = CustomerProfile::find($user_id);
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }
        return response()->json($profile);
}

     public function store(Request $request)
{
        $request->validate([
            'user_id' => 'required|unique:customer_profiles,user_id',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string'
        ]);

        $profile = CustomerProfile::create($request->only(['user_id','first_name','last_name']));
        return response()->json($profile, 201);
}

     public function update(Request $request, $user_id)
{
        $profile = CustomerProfile::find($user_id);
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $profile->update($request->only(['first_name','last_name']));
        return response()->json($profile);
}

     public function destroy($user_id)
{
        $profile = CustomerProfile::find($user_id);
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $profile->delete();
        return response()->json(['message' => 'Profile deleted successfully']);
}
}
