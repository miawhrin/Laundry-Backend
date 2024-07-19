<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserTableSeeder extends Seeder
{
    public function run(): void
    {
    
    // Menetapkan izin untuk peran 'admin'
    $admin = Role::find(1);
    $adminPermissions = Permission::whereIn('id', [1,2,3,4,5,6,7,8,9])->get();
    $admin->syncPermissions($adminPermissions);

    // Menetapkan izin untuk peran 'customer'
    $customer = Role::find(2);
    $customerPermissions = Permission::whereIn('id', [])->get();
    $customer->syncPermissions($customerPermissions);
    
    /**
     * Run the database seeds.
     */
    
        $admin = User::create([
            'name' => 'Admin',
            'phone' => '081267850951',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
        ]);
        $admin->assignRole('admin');

        $customer = User::create([
            'name' => 'Customer',
            'phone' => '081289675468',
            'email' => 'customer@gmail.com',
            'password' => bcrypt('password'),
            'role_id' => 2,
        ]);
        $customer->assignRole('customer');

    }
}
