<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\StoreCategory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;

class LamsetEdSeeder extends Seeder
{
    public function run(): void
    {
        /* =========================
         * 1️⃣ إنشاء المستخدم (Seller)
         * ========================= */
        $user = User::create([
            'email' => 'lamseted@suq-app.com',
            'phone' => '0791110001',
            'role'  => 'seller',
        ]);

        SellerProfile::create([
            'user_id'           => $user->id,
            'password'          => Hash::make('123456'),
            'name'              => 'لمسة إيد',
            'store_description' => 'متجر لمسة إيد مختص بتقديم منتجات يدوية مصنوعة بعناية ولمسة فنية مميزة باستخدام مواد طبيعية وبجودة عالية.',
            'main_category_id'  => 1, // أعمال يدوية
            'store_logo_url'    => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30',
            'store_cover_url'   => 'https://images.unsplash.com/photo-1503602642458-232111445657',
            'preparation_days'  => 1,
            'preparation_hours' => 6,
            'delivery_price'    => 3.00,
        ]);

        /* =========================
         * 2️⃣ فئات المتجر
         * ========================= */
        $best = StoreCategory::create([
            'store_id' => $user->id,
            'name'     => 'الأكثر مبيعًا',
        ]);

        $gifts = StoreCategory::create([
            'store_id' => $user->id,
            'name'     => 'هدايا يدوية',
        ]);

        $decor = StoreCategory::create([
            'store_id' => $user->id,
            'name'     => 'ديكور منزلي',
        ]);

        /* =========================
         * 3️⃣ المنتج 1: شمعة صويا
         * ========================= */
        $candle = Product::create([
            'store_id'          => $user->id,
            'store_category_id' => $best->id,
            'name'              => 'شمعة صويا معطّرة',
            'description'       => 'شمعة مصنوعة يدويًا من شمع الصويا الطبيعي، تمنح أجواء دافئة ورائحة مهدئة، مناسبة للاستخدام اليومي.',
            'price'             => 8.00,
            'preparation_time'  => '00:15:00',
        ]);

        ProductImage::insert([
            [
                'product_id' => $candle->id,
                'image_url'  => 'https://images.unsplash.com/photo-1602526219045-b7c8c39b8b02',
            ],
            [
                'product_id' => $candle->id,
                'image_url'  => 'https://images.unsplash.com/photo-1589987607627-616cac2a3a9c',
            ],
        ]);

        $scent = ProductOption::create([
            'product_id' => $candle->id,
            'name' => 'scent',
            'label' => 'الرائحة',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 1,
            'affects_variant' => true,
        ]);

        foreach (['لافندر','فانيليا','ورد'] as $i => $v) {
            ProductOptionValue::create([
                'option_id' => $scent->id,
                'value' => $v,
                'label' => $v,
                'price_delta' => 0,
                'sort_order' => $i,
            ]);
        }

        /* =========================
         * 4️⃣ المنتج 2: حامل شموع
         * ========================= */
        $holder = Product::create([
            'store_id'          => $user->id,
            'store_category_id' => $best->id,
            'name'              => 'حامل شموع خشبي',
            'description'       => 'حامل شموع مصنوع يدويًا من الخشب الطبيعي بتصميم بسيط يضيف لمسة ديكور دافئة.',
            'price'             => 6.00,
            'preparation_time'  => '00:10:00',
        ]);

        ProductImage::insert([
            [
                'product_id' => $holder->id,
                'image_url'  => 'https://images.unsplash.com/photo-1616627452249-8a8b2b0c4a9d',
            ],
            [
                'product_id' => $holder->id,
                'image_url'  => 'https://images.unsplash.com/photo-1540574163026-643ea20ade25',
            ],
        ]);

        /* =========================
         * 5️⃣ المنتج 3: صندوق هدايا
         * ========================= */
        $box = Product::create([
            'store_id'          => $user->id,
            'store_category_id' => $gifts->id,
            'name'              => 'صندوق هدايا يدوي',
            'description'       => 'صندوق هدايا أنيق مصنوع يدويًا مناسب لتقديم الهدايا في المناسبات المختلفة.',
            'price'             => 12.00,
            'preparation_time'  => '00:20:00',
        ]);

        ProductImage::insert([
            [
                'product_id' => $box->id,
                'image_url'  => 'https://images.unsplash.com/photo-1607349913331-1c4eaccc0d37',
            ],
            [
                'product_id' => $box->id,
                'image_url'  => 'https://images.unsplash.com/photo-1512909006721-3d6018887383',
            ],
        ]);
    }
}
