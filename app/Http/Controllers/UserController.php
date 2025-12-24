<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\SellerProfile;
use App\Models\DeliveryProfile;

class UserController extends Controller
{
    // GET /users?role=customer|seller|delivery
    public function index(Request $request)
    {
        $role = $request->query('role');

        $query = User::query()->with([
            'customerProfile',
            'sellerProfile',
            'deliveryProfile',
        ]);

        if ($role === 'customer') {
            $query->where('role', 'customer');
        } elseif ($role === 'seller') {
            $query->where('role', 'seller');
        } elseif ($role === 'delivery') {
            $query->where('role', 'delivery');
        }

        return response()->json($query->get());
    }

    // GET /users/{id}
    public function show($id)
    {
        $user = User::with(['customerProfile','sellerProfile','deliveryProfile'])->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    // POST /users  (ينشئ user + profile حسب الدور)
    public function store(Request $request)
    {
        if (User::where('phone', $request->phone)->exists()) {
            return response()->json([
                'message' => 'Phone already exists',
                'field'   => 'phone'
            ], 409);
        }

        if ($request->filled('email') && User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email already exists',
                'field'   => 'email'
            ], 409);
        }

        $data = $request->validate([
            'phone' => 'required|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'role'  => 'required|in:customer,seller,delivery',

            // customer & delivery
            'first_name' => 'required_if:role,customer,delivery',
            'last_name'  => 'required_if:role,customer,delivery',

            // seller & delivery
            'password'   => 'required_if:role,seller,delivery|min:6',

            // seller only
            'name'              => 'required_if:role,seller|string|max:255',
            'store_description' => 'required_if:role,seller|string',
            'main_category_id'  => 'required_if:role,seller|exists:categories,id',
            'store_logo_url'    => 'nullable|string',
            'store_cover_url'   => 'nullable|string',

            // delivery only
            'profile_pic_url' => 'nullable|string',

            // 🚀 الأعمدة التي أضفناها
            'vehicle_type'   => 'required_if:role,delivery|string',
            'vehicle_number' => 'required_if:role,delivery|string',
        ]);

        $user = null;

        DB::transaction(function () use (&$user, $data) {

            // 1) إنشاء المستخدم
            $user = User::create([
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'role'  => $data['role'],
            ]);

            // 2) إنشاء البروفايل حسب الدور
            switch ($user->role) {

                case 'customer':
                    CustomerProfile::create([
                        'user_id'    => $user->id,
                        'first_name' => $data['first_name'] ?? null,
                        'last_name'  => $data['last_name'] ?? null,
                    ]);
                    break;

                case 'seller':
                    SellerProfile::create([
                        'user_id'           => $user->id,
                        'name'              => $data['name'],
                        'password'          => Hash::make($data['password']),
                        'store_description' => $data['store_description'] ?? null,
                        'main_category_id'  => $data['main_category_id'],
                        'store_logo_url'    => $data['store_logo_url'] ?? '',
                        'store_cover_url'   => $data['store_cover_url'] ?? '',
                    ]);
                    break;

                case 'delivery':
                    DeliveryProfile::create([
                        'user_id'         => $user->id,
                        'first_name'      => $data['first_name'] ?? null,
                        'last_name'       => $data['last_name'] ?? null,
                        'password'        => Hash::make($data['password']),
                        'profile_pic_url' => $data['profile_pic_url'] ?? '',

                        // 🚀 الأعمدة الجديدة
                        'vehicle_type'    => $data['vehicle_type'],
                        'vehicle_number'  => $data['vehicle_number'],
                    ]);
                    break;
            }
        });

        $user->load(['customerProfile','sellerProfile','deliveryProfile']);

        return response()->json([
            'message' => 'User and profile created successfully',
            'user'    => $user,
        ], 201);
    }

    // PATCH/PUT /users/{id}
    public function update(Request $request, $id)
    {
        $user = User::with(['customerProfile','sellerProfile','deliveryProfile'])->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = $request->validate([
            'phone' => 'sometimes|unique:users,phone,' . $id,
            'email' => 'sometimes|nullable|email|unique:users,email,' . $id,

            'first_name' => 'sometimes|string',
            'last_name'  => 'sometimes|string',

            'password'   => 'sometimes|min:6',

            'name'              => 'sometimes|string|max:255',
            'store_description' => 'sometimes|nullable|string',
            'main_category_id'  => 'sometimes|exists:categories,id',
            'store_logo_url'    => 'sometimes|nullable|string',
            'store_cover_url'   => 'sometimes|nullable|string',
            'profile_pic_url'   => 'sometimes|nullable|string',

            // ✨ تحديث بيانات المركبة
            'vehicle_type'   => 'sometimes|string',
            'vehicle_number' => 'sometimes|string',
        ]);

        DB::transaction(function () use ($user, $data) {

            // تحديث user
            $user->update([
                'email' => $data['email'] ?? $user->email,
                'phone' => $data['phone'] ?? $user->phone,
            ]);

            // تحديث customer
            if ($user->role === 'customer' && $user->customerProfile) {
                $user->customerProfile->update([
                    'first_name' => $data['first_name'] ?? $user->customerProfile->first_name,
                    'last_name'  => $data['last_name']  ?? $user->customerProfile->last_name,
                ]);
            }

            // تحديث seller
            if ($user->role === 'seller' && $user->sellerProfile) {
                $user->sellerProfile->update([
                    'name'              => $data['name']              ?? $user->sellerProfile->name,
                    'password'          => isset($data['password']) ? Hash::make($data['password']) : $user->sellerProfile->password,
                    'store_description' => array_key_exists('store_description', $data) ? $data['store_description'] : $user->sellerProfile->store_description,
                    'main_category_id'  => $data['main_category_id']  ?? $user->sellerProfile->main_category_id,
                    'store_logo_url'    => array_key_exists('store_logo_url', $data) ? $data['store_logo_url'] : $user->sellerProfile->store_logo_url,
                    'store_cover_url'   => array_key_exists('store_cover_url', $data) ? $data['store_cover_url'] : $user->sellerProfile->store_cover_url,
                ]);
            }

            // تحديث delivery
            if ($user->role === 'delivery' && $user->deliveryProfile) {
                $user->deliveryProfile->update([
                    'first_name'      => $data['first_name']      ?? $user->deliveryProfile->first_name,
                    'last_name'       => $data['last_name']       ?? $user->deliveryProfile->last_name,
                    'password'        => isset($data['password']) ? Hash::make($data['password']) : $user->deliveryProfile->password,
                    'profile_pic_url' => array_key_exists('profile_pic_url', $data) ? $data['profile_pic_url'] : $user->deliveryProfile->profile_pic_url,

                    // 🚀 تحديث بيانات المركبة
                    'vehicle_type'    => $data['vehicle_type']   ?? $user->deliveryProfile->vehicle_type,
                    'vehicle_number'  => $data['vehicle_number'] ?? $user->deliveryProfile->vehicle_number,
                ]);
            }
        });

        return response()->json($user->load(['customerProfile','sellerProfile','deliveryProfile']));
    }

    // DELETE /users/{id}
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }


    public function loginByPhone(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required'
        ]);

        $user = User::with('customerProfile')
            ->where('phone', $data['phone'])
            ->where('role', 'customer')
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 401);
        }

        return response()->json([
            'message' => 'Login success',
            'user'    => $user,
        ], 200);
    }

    public function sellerLogin(Request $request)
    {
        $data = $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);

        $user = User::with('sellerProfile')
            ->where('phone', $data['phone'])
            ->where('role', 'seller')
            ->first();

        if (!$user || !$user->sellerProfile) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!Hash::check($data['password'], $user->sellerProfile->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user->sellerProfile->makeHidden('password');

        return response()->json([
            'message' => 'Login success',
            'user'    => $user,
        ], 200);
    }

    public function deliveryLogin(Request $request)
{
    $data = $request->validate([
        'phone'    => 'required',
        'password' => 'required',
    ]);

    $user = User::with('deliveryProfile')
        ->where('phone', $data['phone'])
        ->where('role', 'delivery')
        ->first();

    if (!$user || !$user->deliveryProfile) {
        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    if (!Hash::check($data['password'], $user->deliveryProfile->password)) {
        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    $user->deliveryProfile->makeHidden('password');

    return response()->json([
        'message' => 'Login success',
        'user'    => $user,
    ], 200);
}


}
