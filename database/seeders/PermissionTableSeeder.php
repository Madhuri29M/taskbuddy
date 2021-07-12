<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
           'permission-list',
           'permission-create',
           'permission-delete',
           
           'cms-list',
           'cms-create',
           'cms-edit',
           // 'cms-delete',
           
           'country-list',
           'country-create',
           'country-edit',
           'country-delete',

           'customer-list',
           'customer-create',
           'customer-edit',
           'customer-delete',
           'customer_message-list',

           'setting-list',
           'setting-create',
           'setting-edit',
           'setting-delete',

           'faqs-list',
           
           'user-list',
           'user-create',
           'user-edit',
           'user-delete'
        ];
        
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }
    }
}
