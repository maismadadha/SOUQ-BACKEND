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
            // بنحمّل البروفايل المناسب بس مش شرط كله
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

        $users = $query->get();

        return response()->json($users);
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
        ]);

        $user = null;

        DB::transaction(function () use (&$user, $data) {
            // 1) أنشئ المستخدم
            $user = User::create([
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'role'  => $data['role'], // لازم نضيف عمود role بجدول users
            ]);

            // 2) أنشئ البروفايل حسب الدور
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
                        'password'          => Hash::make($data['password']), // مهم
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
                        'password'        => Hash::make($data['password']), // مهم
                        'profile_pic_url' => $data['profile_pic_url'] ?? '',
                    ]);
                    break;
            }
        });

        // رجّع اليوزر مع بروفايله
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

            // تحديث معلومات البروفايل حسب الدور الحالي
            'first_name' => 'sometimes|string',
            'last_name'  => 'sometimes|string',

            'password'   => 'sometimes|min:6',
            'name'              => 'sometimes|string|max:255',
            'store_description' => 'sometimes|nullable|string',
            'main_category_id'  => 'sometimes|exists:categories,id',
            'store_logo_url'    => 'sometimes|nullable|string',
            'store_cover_url'   => 'sometimes|nullable|string',
            'profile_pic_url'   => 'sometimes|nullable|string',
        ]);

        DB::transaction(function () use ($user, $data) {
            // تحديث user
            $user->update([
                'email' => $data['email'] ?? $user->email,
                'phone' => $data['phone'] ?? $user->phone,
                // ما بنغيّر role هون (إذا بدك تغيير دور نحكيه بخطوة منفصلة)
            ]);

            // تحديث البروفايل حسب دور المستخدم الحالي
            if ($user->role === 'customer' && $user->customerProfile) {
                $user->customerProfile->update([
                    'first_name' => $data['first_name'] ?? $user->customerProfile->first_name,
                    'last_name'  => $data['last_name']  ?? $user->customerProfile->last_name,
                ]);
            }

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

            if ($user->role === 'delivery' && $user->deliveryProfile) {
                $user->deliveryProfile->update([
                    'first_name'      => $data['first_name']      ?? $user->deliveryProfile->first_name,
                    'last_name'       => $data['last_name']       ?? $user->deliveryProfile->last_name,
                    'password'        => isset($data['password']) ? Hash::make($data['password']) : $user->deliveryProfile->password,
                    'profile_pic_url' => array_key_exists('profile_pic_url', $data) ? $data['profile_pic_url'] : $user->deliveryProfile->profile_pic_url,
                ]);
            }
        });

        $user->load(['customerProfile','sellerProfile','deliveryProfile']);

        return response()->json($user);
    }

    // DELETE /users/{id}
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete(); // عندك ON DELETE CASCADE بالبروفايلات، فتمام
        return response()->json(['message' => 'User deleted successfully']);
    }


    public function loginByPhone(Request $request)
{
    // 1) فاليديشن بسيط
    $data = $request->validate([
        'phone' => 'required'
    ]);

    // 2) نجيب اليوزر حسب رقم التلفون والدور customer
    $user = User::with('customerProfile')
        ->where('phone', $data['phone'])
        ->where('role', 'customer') // بما إن تطبيقك تبع customers
        ->first();

    // 3) لو ما لقيناه
    if (!$user) {
        return response()->json([
            'message' => 'User not found',
        ], 404);
    }

    // 4) لو لقيناه
    return response()->json([
        'message' => 'Login success',
        'user'    => $user,
    ], 200);
}


}
