<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
     public function run(): void
    {
        $categories = [
            ['name' => 'أعمال يدوية', 'image' => 'https://images.pexels.com/photos/18486386/pexels-photo-18486386.jpeg'],
            ['name' => 'إلكترونيات', 'image' => 'https://images.pexels.com/photos/34624327/pexels-photo-34624327.jpeg'],
            ['name' => 'ملابس', 'image' => 'https://images.pexels.com/photos/996329/pexels-photo-996329.jpeg'],
            ['name' => 'تجميل', 'image' => 'https://images.pexels.com/photos/31455821/pexels-photo-31455821.jpeg'],
            ['name' => 'كتب', 'image' => 'https://images.pexels.com/photos/159866/pexels-photo-159866.jpeg'],
            ['name' => 'منزل ومطبخ', 'image' => 'https://images.pexels.com/photos/15280175/pexels-photo-15280175.jpeg'],
            ['name' => 'ألعاب', 'image' => 'https://images.pexels.com/photos/7436136/pexels-photo-7436136.jpeg'],
            ['name' => 'رياضة', 'image' => 'https://images.pexels.com/photos/841130/pexels-photo-841130.jpeg'],
            ['name' => 'صحة', 'image' => 'https://images.pexels.com/photos/406152/pexels-photo-406152.jpeg'],
            ['name' => 'مجوهرات', 'image' => 'https://images.pexels.com/photos/2735981/pexels-photo-2735981.jpeg'],
            ['name' => 'أدوات مكتبية', 'image' => 'https://images.pexels.com/photos/8947692/pexels-photo-8947692.jpeg'],
            ['name' => 'أحذية', 'image' => 'https://images.pexels.com/photos/19090/pexels-photo-19090.jpeg'],
            ['name' => 'إكسسوارات', 'image' => 'https://images.pexels.com/photos/1191531/pexels-photo-1191531.jpeg'],

        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }

}
