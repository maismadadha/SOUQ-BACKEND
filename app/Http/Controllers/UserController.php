<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\SellerProfile;
use App\Models\DeliveryProfile;


class UserController extends Controller
{
    public function index()
{
    $users = User::all();
    return response()->json($users);
}

    public function show($id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }
    return response()->json($user);
}

    public function store(Request $request)
{

    $request->validate([
       'phone' => 'required|unique:users,phone', // رقم الهاتف مطلوب وفريد
       'email' => 'nullable|email',
        'role'  => 'required|in:customer,seller,delivery',

        // حقول الـ profiles حسب الدور
        'first_name' => 'required_if:role,customer,delivery',
        'last_name'  => 'required_if:role,customer,delivery',
        'password'   => 'required_if:role,seller,delivery',
        'store_description' => 'required_if:role,seller',
        'main_category_id' => 'required_if:role,seller|exists:categories,id',
        'store_logo_url' => 'nullable|string',
        'store_cover_url' => 'nullable|string',
        'name' => 'required_if:role,seller|string|max:255',
        'profile_pic_url' => 'nullable|string',

    ]);


    $user = User::create([
        'email' => $request->email,
        'phone' => $request->phone,
          'role'  => $request->role,
    ]);


      // إنشاء الـ profile حسب الدور
        switch ($user->role) {
            case 'customer':
                CustomerProfile::create([
                    'user_id' => $user->id,
                    'first_name' => $request->first_name,
                    'last_name'  => $request->last_name,
                ]);
                break;

            case 'seller':
                SellerProfile::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'password' => $request->password, // لاحقًا تشفير
                'store_description' => $request->store_description,
                'main_category_id' => $request->main_category_id,
                'store_logo_url' => $request->store_logo_url ?? '',
                'store_cover_url' => $request->store_cover_url ?? '',
                ]);
                break;

            case 'delivery':
                DeliveryProfile::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'password'   => $request->password,
                'profile_pic_url' => $request->profile_pic_url ?? '',
                ]);
                break;
        }



    return response()->json([
            'message' => 'User and profile created successfully',
            'user' => $user,
        ], 201);
}

    public function update(Request $request, $id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $request->validate([
        'phone' => 'unique:users,phone,' . $id, // يتأكد من فريدية الهاتف مع استثناء السجل الحالي
        'email' => 'nullable|email'
    ]);

    $user->update($request->only(['email', 'phone']));

    return response()->json($user);
}

    public function destroy($id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->delete();

    return response()->json(['message' => 'User deleted successfully']);
}



}
