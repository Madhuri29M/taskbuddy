<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::updateOrCreate(
            ['name' => 'app_name'],
            ['value' => 'Taskbuddy',
            'status' => 'active']
        );

        Setting::updateOrCreate(
            ['name' => 'app_short_name'],
            ['value' => 'TB',
            'status' => 'active']
        );

        Setting::updateOrCreate(
            ['name' => 'email_username'],
            ['value' => 'email_username',
            'status' => 'active']
        );

        Setting::updateOrCreate(
            ['name' => 'email_password'],
            ['value' => 'email_password',
            'status' => 'active']
        );

        Setting::updateOrCreate(
            ['name' => 'email_host'],
            ['value' => 'smtp.googlemail.com',
            'status' => 'active']
        );

        Setting::updateOrCreate(
            ['name' => 'email_port'],
            ['value' => '587',
            'status' => 'active']
        );

        Setting::updateOrCreate(
            ['name' => 'email_encryption'],
            ['value' => 'tls',
            'status' => 'active']
        );

        Setting::updateOrCreate(
            ['name' => 'email_from_address'],
            ['value' => 'taskbuddy@gmail.com',
            'status' => 'active']
        );

        Setting::updateOrCreate(
            ['name' => 'email_from_name'],
            ['value' => 'Taskbuddy',
            'status' => 'active']
        );

        Setting::updateOrCreate(
            ['name' => 'contact_email'],
            ['value' => 'taskbuddy@gmail.com',
            'status' => 'active'],
        );
    }
}
