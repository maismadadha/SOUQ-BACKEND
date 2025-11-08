<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,   // يضيف الأدوار (seller, customer, delivery)
            CategoriesSeeder::class,   // يضيف فئات المنتجات
            UsersOtpSeeder::class,     // يحدّث كود الـ OTP لكل المستخدمين
            UsersSeeder::class,        // ينشئ المستخدمين والمتاجر والمنتجات
            
        ]);
    }
}
