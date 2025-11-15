<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\StoreCategory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;

class DemoStoreSeeder extends Seeder
{
    public function run(): void
    {
        // ==============================
        // 1) إنشاء صاحب المتجر
        // ==============================
        $user = User::create([
            'email' => 'styl2ehub@example.com',
            'phone' => '0791711111',
            'role'  => 'seller',
        ]);

        // ==============================
        // 2) بروفايل متجر
        // ==============================
        SellerProfile::create([
            'user_id' => $user->id,
            'name' => 'ستايل هب',
            'password' => bcrypt('123456'),
            'store_description' => 'متجر أزياء عصري يقدم تشكيلات متنوعة للرجال والنساء.',
            'main_category_id' => 3,
            'store_logo_url' => 'https://images.pexels.com/photos/163139/mens-fashion.jpg',
            'store_cover_url' => 'https://images.pexels.com/photos/298864/pexels-photo-298864.jpeg',
        ]);

        // ==============================
        // 3) فئات المتجر
        // ==============================
        $categories = [
            'الأكثر مبيعًا',
            'ملابس رجالية',
            'ملابس نسائية',
            'إكسسوارات'
        ];

        foreach ($categories as $name) {
            StoreCategory::create([
                'store_id' => $user->id,
                'name' => $name,
            ]);
        }

        // ========== جلب الفئات ==========
        $best = StoreCategory::where('name', 'الأكثر مبيعًا')->first();
        $men  = StoreCategory::where('name', 'ملابس رجالية')->first();
        $women = StoreCategory::where('name', 'ملابس نسائية')->first();
        $acc = StoreCategory::where('name', 'إكسسوارات')->first();

        // ==============================
        // 4) منتج 1 – Hoodie
        // ==============================
        $hoodie = Product::create([
            'store_id' => $user->id,
            'store_category_id' => $best->id,
            'name' => 'هودي شبابي',
            'description' => 'هودي عالي الجودة مناسب لكل الفصول.',
            'price' => 35,
            'preparation_time' => '00:20:00',
        ]);

        $hoodieImages = [
            'https://images.pexels.com/photos/6311571/pexels-photo-6311571.jpeg',
            'https://images.pexels.com/photos/5886041/pexels-photo-5886041.jpeg',
        ];

        foreach ($hoodieImages as $img) {
            ProductImage::create([
                'product_id' => $hoodie->id,
                'image_url' => $img
            ]);
        }

        // خيارات الهودي
        $hoodieSize = ProductOption::create([
            'product_id' => $hoodie->id,
            'name' => 'size',
            'label' => 'الحجم',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 1,
            'affects_variant' => true,
        ]);

        foreach (['S','M','L','XL'] as $s) {
            ProductOptionValue::create([
                'option_id' => $hoodieSize->id,
                'value' => $s,
                'label' => $s,
                'price_delta' => 0
            ]);
        }

        $hoodieColor = ProductOption::create([
            'product_id' => $hoodie->id,
            'name' => 'color',
            'label' => 'اللون',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 2,
            'affects_variant' => true,
        ]);

        foreach (['أسود','رمادي','أزرق'] as $c) {
            ProductOptionValue::create([
                'option_id' => $hoodieColor->id,
                'value' => $c,
                'label' => $c,
            ]);
        }

        // ==============================
        // 5) منتج 2 – Sneakers
        // ==============================
        $shoes = Product::create([
            'store_id' => $user->id,
            'store_category_id' => $best->id,
            'name' => 'سنيكرز أبيض',
            'description' => 'حذاء شبابي مريح وخفيف.',
            'price' => 50,
            'preparation_time' => '00:25:00',
        ]);

        $shoeImages = [
            'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg',
            'https://images.pexels.com/photos/975674/pexels-photo-975674.jpeg',
        ];

        foreach ($shoeImages as $img) {
            ProductImage::create([
                'product_id' => $shoes->id,
                'image_url' => $img
            ]);
        }

        $shoeSize = ProductOption::create([
            'product_id' => $shoes->id,
            'name' => 'size',
            'label' => 'المقاس',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 1,
            'affects_variant' => true,
        ]);

        foreach (['40','41','42','43','44'] as $s) {
            ProductOptionValue::create([
                'option_id' => $shoeSize->id,
                'value' => $s,
                'label' => $s,
            ]);
        }

        // ==============================
        // 6) ملابس رجالية – قميص
        // ==============================
        $shirt = Product::create([
            'store_id' => $user->id,
            'store_category_id' => $men->id,
            'name' => 'قميص كلاسيكي',
            'description' => 'قميص رسمي أنيق للعمل والمناسبات.',
            'price' => 28,
            'preparation_time' => '00:15:00',
        ]);

        $shirtImages = [
            'https://images.pexels.com/photos/428340/pexels-photo-428340.jpeg',
            'https://images.pexels.com/photos/2983464/pexels-photo-2983464.jpeg',
        ];

        foreach ($shirtImages as $i) {
            ProductImage::create([
                'product_id' => $shirt->id,
                'image_url' => $i
            ]);
        }

        $shirtSize = ProductOption::create([
            'product_id' => $shirt->id,
            'name' => 'size',
            'label' => 'الحجم',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 1,
            'affects_variant' => true,
        ]);

        foreach (['S','M','L','XL'] as $s) {
            ProductOptionValue::create([
                'option_id' => $shirtSize->id,
                'value' => $s,
                'label' => $s,
            ]);
        }

        // ==============================
        // 7) ملابس نسائية – فستان
        // ==============================
        $dress = Product::create([
            'store_id' => $user->id,
            'store_category_id' => $women->id,
            'name' => 'فستان روز',
            'description' => 'فستان ناعم مناسب للخروجات والسهرات.',
            'price' => 45,
            'preparation_time' => '00:20:00',
        ]);

        $dressImages = [
            'https://images.pexels.com/photos/1859413/pexels-photo-1859413.jpeg',
            'https://images.pexels.com/photos/842811/pexels-photo-842811.jpeg',
        ];

        foreach ($dressImages as $i) {
            ProductImage::create([
                'product_id' => $dress->id,
                'image_url' => $i
            ]);
        }

        $dressSize = ProductOption::create([
            'product_id' => $dress->id,
            'name' => 'size',
            'label' => 'الحجم',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 1,
            'affects_variant' => true,
        ]);

        foreach (['S','M','L'] as $s) {
            ProductOptionValue::create([
                'option_id' => $dressSize->id,
                'value' => $s,
                'label' => $s,
            ]);
        }

        // ==============================
        // 8) إكسسوارات – ساعة
        // ==============================
        $watch = Product::create([
            'store_id' => $user->id,
            'store_category_id' => $acc->id,
            'name' => 'ساعة جلد كلاسيكية',
            'description' => 'ساعة أنيقة مناسبة للإطلالات اليومية.',
            'price' => 55,
            'preparation_time' => '00:10:00',
        ]);

        $watchImages = [
            'https://images.pexels.com/photos/190819/pexels-photo-190819.jpeg',
            'https://images.pexels.com/photos/277319/pexels-photo-277319.jpeg',
        ];

        foreach ($watchImages as $i) {
            ProductImage::create([
                'product_id' => $watch->id,
                'image_url' => $i
            ]);
        }

        $watchColor = ProductOption::create([
            'product_id' => $watch->id,
            'name' => 'color',
            'label' => 'لون السوار',
            'selection' => 'single',
            'required' => false,
            'sort_order' => 1,
            'affects_variant' => false,
        ]);

        foreach (['أسود','بني','بيج'] as $c) {
            ProductOptionValue::create([
                'option_id' => $watchColor->id,
                'value' => $c,
                'label' => $c,
            ]);
        }
    }
}
