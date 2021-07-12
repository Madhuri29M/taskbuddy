<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DEVELPER ROLE
        $role1 = Role::firstOrCreate([
		            'name' => 'developer',
		            // 'title' => 'Developer Team',
		            'guard_name' => 'web'
		        ]);
       	$permissions1 = Permission::whereIn('name',[
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'permission-list',
            'permission-create',
            'permission-delete'
        ])->get();
       	$role1->syncPermissions($permissions1);
        
        // SUPER ADMIN ROLE
        $role2 = Role::firstOrCreate([
                    'name' => 'admin',
                    'guard_name' => 'web'
                ]);
        $permissions2 = Permission::whereIn('name',[
           'cms-list',
           'cms-create',
           'cms-edit',

           'customer-list',
           'customer-create',
           'customer-edit',
           'customer-delete',
           'customer_message-list',

           'setting-list',
           'setting-create',
           'setting-edit',
           'setting-delete',          

           // 'faqs-list',

           'user-list',
           'user-create',
           'user-edit',
           'user-delete',
           
        ])->get();
        $role2->syncPermissions($permissions2);

        // Customer 
        $role3 = Role::firstOrCreate([
          'name' => 'customer',
          'guard_name' => 'web'
        ]);

    }
}
