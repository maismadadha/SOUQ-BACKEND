<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $categories = [
            'إلكترونيات',
            'ملابس',
            'تجميل',
            'كتب',
            'منزل ومطبخ',
            'ألعاب',
            'رياضة',
            'صحة',
            'مجوهرات',
            'أدوات مكتبية',
            'أحذية',
            'إكسسوارات',
            'اعمال يدوية'
        ];

         foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}
