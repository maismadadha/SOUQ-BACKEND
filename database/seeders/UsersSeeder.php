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

class UsersSeeder extends Seeder{
    public function run(): void
    {
        /* =========================
         * 1️⃣ Seller User
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
            'store_description' => 'متجر لمسة إيد يقدّم منتجات يدوية مصنوعة بعناية باستخدام مواد طبيعية ولمسة فنية دافئة تناسب جميع المناسبات.',
            'main_category_id'  => 1, // أعمال يدوية
            'store_logo_url'    => 'https://images.pexels.com/photos/8716181/pexels-photo-8716181.jpeg',
            'store_cover_url'   => 'https://images.pexels.com/photos/4841363/pexels-photo-4841363.jpeg',
            'preparation_days'  => 1,
            'preparation_hours' => 6,
            'delivery_price'    => 3.00,
        ]);

        /* =========================
         * 2️⃣ Store Categories
         * ========================= */
        $best = StoreCategory::create([
            'store_id' => $user->id,
            'name'     => 'الأكثر مبيعًا',
        ]);

        $gifts = StoreCategory::create([
            'store_id' => $user->id,
            'name'     => 'هدايا يدوية',
        ]);

        /* =========================
         * 3️⃣ Product 1: شمعة صويا
         * ========================= */
        $candle = Product::create([
            'store_id'          => $user->id,
            'store_category_id' => $best->id,
            'name'              => 'شمعة صويا معطّرة',
            'description'       => 'شمعة مصنوعة يدويًا من شمع الصويا الطبيعي، تمنح أجواء دافئة ورائحة مهدئة، مناسبة للاسترخاء والاستخدام اليومي.',
            'price'             => 8.00,
            'preparation_time'  => '00:15:00',
        ]);

        foreach ([
            'https://images.pexels.com/photos/6755809/pexels-photo-6755809.jpeg',
            'https://images.pexels.com/photos/6755863/pexels-photo-6755863.jpeg',
        ] as $img) {
            ProductImage::create([
                'product_id' => $candle->id,
                'image_url'  => $img,
            ]);
        }

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
                'option_id'   => $scent->id,
                'value'       => $v,
                'label'       => $v,
                'price_delta' => 0,
                'sort_order'  => $i,
            ]);
        }

        /* =========================
         * 4️⃣ Product 2: حامل شموع
         * ========================= */
        $holder = Product::create([
            'store_id'          => $user->id,
            'store_category_id' => $best->id,
            'name'              => 'حامل شموع خشبي',
            'description'       => 'حامل شموع مصنوع يدويًا من الخشب الطبيعي بتصميم بسيط يضيف لمسة ديكور دافئة للمكان.',
            'price'             => 6.00,
            'preparation_time'  => '00:10:00',
        ]);

        foreach ([
            'https://images.pexels.com/photos/6633441/pexels-photo-6633441.jpeg',
            'https://images.pexels.com/photos/10496215/pexels-photo-10496215.jpeg',
        ] as $img) {
            ProductImage::create([
                'product_id' => $holder->id,
                'image_url'  => $img,
            ]);
        }

        /* =========================
         * 5️⃣ Product 3: صندوق هدايا
         * ========================= */
        $box = Product::create([
            'store_id'          => $user->id,
            'store_category_id' => $gifts->id,
            'name'              => 'صندوق هدايا يدوي',
            'description'       => 'صندوق هدايا أنيق مصنوع يدويًا، مناسب لتقديم الهدايا في المناسبات الخاصة بلمسة طبيعية ودافئة.',
            'price'             => 12.00,
            'preparation_time'  => '00:20:00',
        ]);

        foreach ([
            'https://images.pexels.com/photos/5876688/pexels-photo-5876688.jpeg',
            'https://images.pexels.com/photos/20192214/pexels-photo-20192214.jpeg',
        ] as $img) {
            ProductImage::create([
                'product_id' => $box->id,
                'image_url'  => $img,
            ]);
        }
    }
}
