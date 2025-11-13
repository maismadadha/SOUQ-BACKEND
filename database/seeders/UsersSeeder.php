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

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::create([
          'email' => 'anaqa@example.com',
          'phone' => '0790000001',
          'role'  => 'seller',
        ]);

        SellerProfile::create([
          'user_id' => $user1->id,
          'name' => 'أناقة',
          'password' => bcrypt('123456'),
          'store_description' => 'متجر يقدم أحدث صيحات الملابس للرجال والنساء',
          'main_category_id' => 3, // ملابس
          'store_logo_url' => 'https://images.pexels.com/photos/2401832/pexels-photo-2401832.jpeg',
          'store_cover_url' => 'https://images.pexels.com/photos/3683586/pexels-photo-3683586.jpeg',
        ]);

        $categories = [
            'الأكثر مبيعًا',
            'الملابس الرجالية',
            'الملابس النسائية',
        ];

        foreach ($categories as $catName) {
            StoreCategory::create([
                'name' => $catName,
                'store_id' => $user1->id,
            ]);
        }

        // --------------------------------------
        // الفئة "الأكثر مبيعًا"
        $mostSellingCategory = StoreCategory::where('store_id', $user1->id)
            ->where('name', 'الأكثر مبيعًا')
            ->first();

        $product1 = Product::create([
            'store_id' => $user1->id,
            'store_category_id' => $mostSellingCategory->id,
            'name' => 'قميص كاجوال',
            'description' => 'قميص رجالي مريح للارتداء اليومي',
            'price' => 25,
            'preparation_time' => '00:15:00',
        ]);

        $images1 = [
            'https://images.pexels.com/photos/34433553/pexels-photo-34433553.jpeg',
            'https://images.pexels.com/photos/31540234/pexels-photo-31540234.jpeg',
        ];

        foreach ($images1 as $img) {
            ProductImage::create([
                'product_id' => $product1->id,
                'image_url' => $img,
            ]);
        }

        $sizeOption1 = ProductOption::create([
            'product_id' => $product1->id,
            'name' => 'size',
            'label' => 'الحجم',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 1,
            'affects_variant' => true,
        ]);

        foreach (['S','M','L'] as $size) {
            ProductOptionValue::create([
                'option_id' => $sizeOption1->id,
                'value' => $size,
                'label' => $size,
                'price_delta' => 0,
                'sort_order' => 0,
            ]);
        }

        $sizeOption2 = ProductOption::create([
            'product_id' => $product1->id,
            'name' => 'color',
            'label' => 'اللون',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 2,
            'affects_variant' => true,
        ]);

        foreach (['احمر','ابيض','ازرق'] as $color) {
            ProductOptionValue::create([
                'option_id' => $sizeOption2->id,
                'value' => $color,
                'label' => $color,
                'price_delta' => 0,
                'sort_order' => 0,
            ]);
        }

        $product2 = Product::create([
            'store_id' => $user1->id,
            'store_category_id' => $mostSellingCategory->id,
            'name' => 'فستان صيفي',
            'description' => 'فستان نسائي خفيف ومريح',
            'price' => 30,
            'preparation_time' => '00:15:00',
        ]);

        $images2 = [
            'https://images.pexels.com/photos/34474459/pexels-photo-34474459.jpeg',
            'https://images.pexels.com/photos/34474454/pexels-photo-34474454.jpeg',
        ];

        foreach ($images2 as $img) {
            ProductImage::create([
                'product_id' => $product2->id,
                'image_url' => $img,
            ]);
        }

        $sizeOption2 = ProductOption::create([
            'product_id' => $product2->id,
            'name' => 'size',
            'label' => 'الحجم',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 1,
            'affects_variant' => true,
        ]);

        foreach (['S','M','L'] as $size) {
            ProductOptionValue::create([
                'option_id' => $sizeOption2->id,
                'value' => $size,
                'label' => $size,
                'price_delta' => 0,
                'sort_order' => 0,
            ]);
        }

        // --------------------------------------
        // الفئة "الملابس الرجالية"
        $mensCategory = StoreCategory::where('store_id', $user1->id)
            ->where('name', 'الملابس الرجالية')
            ->first();

        $product3 = Product::create([
            'store_id' => $user1->id,
            'store_category_id' => $mensCategory->id,
            'name' => 'بنطال جينز',
            'description' => 'بنطال جينز كلاسيكي للرجال',
            'price' => 28,
            'preparation_time' => '00:15:00',
        ]);

        $images3 = [
            'https://images.pexels.com/photos/9775494/pexels-photo-9775494.jpeg',
            'https://images.pexels.com/photos/9775463/pexels-photo-9775463.jpeg',
        ];

        foreach ($images3 as $img) {
            ProductImage::create([
                'product_id' => $product3->id,
                'image_url' => $img,
            ]);
        }

        $sizeOption3 = ProductOption::create([
            'product_id' => $product3->id,
            'name' => 'size',
            'label' => 'الحجم',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 1,
            'affects_variant' => true,
        ]);

        foreach (['S','M','L'] as $size) {
            ProductOptionValue::create([
                'option_id' => $sizeOption3->id,
                'value' => $size,
                'label' => $size,
                'price_delta' => 0,
                'sort_order' => 0,
            ]);
        }

        $product4 = Product::create([
            'store_id' => $user1->id,
            'store_category_id' => $mensCategory->id,
            'name' => 'جاكيت خفيف',
            'description' => 'جاكيت رجالي خفيف للربيع',
            'price' => 40,
            'preparation_time' => '00:15:00',
        ]);

        $images4 = [
            'https://images.pexels.com/photos/14296982/pexels-photo-14296982.jpeg',
            'https://images.pexels.com/photos/14296966/pexels-photo-14296966.jpeg',
        ];

        foreach ($images4 as $img) {
            ProductImage::create([
                'product_id' => $product4->id,
                'image_url' => $img,
            ]);
        }

        $sizeOption4 = ProductOption::create([
            'product_id' => $product4->id,
            'name' => 'size',
            'label' => 'الحجم',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 1,
            'affects_variant' => true,
        ]);

        foreach (['S','M','L'] as $size) {
            ProductOptionValue::create([
                'option_id' => $sizeOption4->id,
                'value' => $size,
                'label' => $size,
                'price_delta' => 0,
                'sort_order' => 0,
            ]);
        }

        // --------------------------------------
        // الفئة "الملابس النسائية"
        $womensCategory = StoreCategory::where('store_id', $user1->id)
            ->where('name', 'الملابس النسائية')
            ->first();

        $product5 = Product::create([
            'store_id' => $user1->id,
            'store_category_id' => $womensCategory->id,
            'name' => 'تيشيرت قطني',
            'description' => 'تيشيرت نسائي قطني ناعم',
            'price' => 15,
            'preparation_time' => '00:15:00',
        ]);

        $images5 = [
            'https://images.pexels.com/photos/34474459/pexels-photo-34474459.jpeg',
            'https://images.pexels.com/photos/34474454/pexels-photo-34474454.jpeg',
        ];

        foreach ($images5 as $img) {
            ProductImage::create([
                'product_id' => $product5->id,
                'image_url' => $img,
            ]);
        }

        $sizeOption5 = ProductOption::create([
            'product_id' => $product5->id,
            'name' => 'size',
            'label' => 'الحجم',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 1,
            'affects_variant' => true,
        ]);

        foreach (['S','M','L'] as $size) {
            ProductOptionValue::create([
                'option_id' => $sizeOption5->id,
                'value' => $size,
                'label' => $size,
                'price_delta' => 0,
                'sort_order' => 0,
            ]);
        }

        $product6 = Product::create([
            'store_id' => $user1->id,
            'store_category_id' => $womensCategory->id,
            'name' => 'بلوزة أنيقة',
            'description' => 'بلوزة نسائية مناسبة للعمل والسهرات',
            'price' => 20,
            'preparation_time' => '00:15:00',
        ]);

        $images6 = [
            'https://images.pexels.com/photos/32498772/pexels-photo-32498772.jpeg',
            'https://images.pexels.com/photos/32498774/pexels-photo-32498774.jpeg',
        ];

        foreach ($images6 as $img) {
            ProductImage::create([
                'product_id' => $product6->id,
                'image_url' => $img,
            ]);
        }

        $sizeOption6 = ProductOption::create([
            'product_id' => $product6->id,
            'name' => 'size',
            'label' => 'الحجم',
            'selection' => 'single',
            'required' => true,
            'sort_order' => 1,
            'affects_variant' => true,
        ]);

        foreach (['S','M','L'] as $size) {
            ProductOptionValue::create([
                'option_id' => $sizeOption6->id,
                'value' => $size,
                'label' => $size,
                'price_delta' => 0,
                'sort_order' => 0,
            ]);
        }
    }
}
