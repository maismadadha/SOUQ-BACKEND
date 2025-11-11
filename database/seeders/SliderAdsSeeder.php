<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SliderAdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // نخلي الإعلانات تنتهي بشهر 2 سنة 2030
        $endDate = Carbon::create(2030, 2, 1, 0, 0, 0); // 1 فبراير 2030

        DB::table('slider_ads')->insert([
            [
                'store_id'    => 1,
                'title'       => 'خصومات الشتاء ❄️',
                'description' => 'استمتع بخصم يصل إلى 50% على أحدث تشكيلات الشتاء!',
                'image_url'   => 'https://images.pexels.com/photos/8386641/pexels-photo-8386641.jpeg',
                'start_date'  => $now,
                'end_date'    => $endDate,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'store_id'    => 1,
                'title'       => 'عرض العطور الفاخر 💎',
                'description' => 'احصل على عطر مجاني عند شراء أي عطر فاخر من مجموعتنا.',
                'image_url'   => 'https://images.pexels.com/photos/7679447/pexels-photo-7679447.jpeg',
                'start_date'  => $now,
                'end_date'    => $endDate,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'store_id'    => 1,
                'title'       => 'أحذية الموسم الجديد 👟',
                'description' => 'تسوق أحدث موديلات الأحذية الرياضية والكلاسيكية الآن.',
                'image_url'   => 'https://images.pexels.com/photos/3839432/pexels-photo-3839432.jpeg',
                'start_date'  => $now,
                'end_date'    => $endDate,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);
    }
}
