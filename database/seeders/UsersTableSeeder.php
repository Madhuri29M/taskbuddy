<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	
    	/*User::firstOrCreate(
            ['user_type' => 'admin'],
            ['first_name' => 'Admin',
            'mobile_number' => '12345678',
            'email' => 'admin@admin.com',
            'password' => '$2y$10$DRK2v.MUCH2M.wTzNtoezeRBx3d1Vw/cseGMncpb5Hnw1Punm9S7O',
            'registered_on' => 'web',
            'status' => 'active',
            'verified' => 1
            ]
        );

    	$this->command->getOutput()->writeln("<question>Admin Panel Admin Credentials</question>");
        $this->command->getOutput()->writeln("<comment>Username:</comment><info>admin@admin.com</info>");
        $this->command->getOutput()->writeln("<comment>Password:</comment><info>12345678</info>");*/

        for ($i=0; $i <= 1; $i++) {
                if($i == 0){
                    $role = Role::where('name','developer')->first();
                    $email = 'developer@mail.com';
                    $password = '12345678';
                    $phone = '1234567890';
                }else if($i == 1){
                    $role = Role::where('name','admin')->first();
                    $email = 'administrator@user.com';
                    $password = 'admin@123';
                    $phone = '4569078123';
                }
                $user = User::firstOrCreate([
                            'first_name' => $role->name,
                            'email' => $email,
                            'mobile_number' => $phone,
                            'password' => Hash::make($password),
                            'user_type' => $role->name,
                            'verified' => 1,
                            'registered_on' => 'web',
                            'status' => 'active',
                            'email_verified_at' => date('Y-m-d'),
                        ]);
                $user->assignRole([$role->id]);

                //if($role->name != 'developer'){
                $this->command->getOutput()->writeln("<question>".strtoupper($role->name)." Panel Credentials</question>");
                $this->command->getOutput()->writeln("<comment>Username:</comment><info>".$email."</info>");
                $this->command->getOutput()->writeln("<comment>Password:</comment><info>$password</info>");
                //}
        }
        //factory('App\Models\User', 10)->create();
    }
}
