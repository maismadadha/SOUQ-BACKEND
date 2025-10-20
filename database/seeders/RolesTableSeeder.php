<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = ['seller', 'customer', 'delivery'];

        foreach ($roles as $roleName) {
            // إذا الدور موجود مسبقًا ما يضيفه
            Role::firstOrCreate(['name' => $roleName]);
        }
    }
}
