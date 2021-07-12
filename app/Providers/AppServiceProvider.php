<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use Cache;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         Schema::defaultStringLength(191);

        // Get All Settings data From DB & Pass it to View
        // $settings = Setting::pluck('value', 'name')->all();
        if (Schema::hasTable('settings')) {
            
            $settings = Cache::remember('settings', 60, function () {
                return Setting::pluck('value', 'name')->all();
            });

            view()->composer('*',function($view) use($settings) {
                $view->with('app_settings', $settings); 
            });
            // Add runtime setting data into config for controllers
            config()->set('app_settings', $settings);

            config()->set('mail', array_merge(config('mail'), [
                'encryption' => @$settings['email_encryption'],
                'username'   => @$settings['email_username'],
                'password'   => @$settings['email_password'],
                'host'       => @$settings['email_host'],
                'port'       => @$settings['email_port'],
                'from' => [
                    'address' => @$settings['email_from_address'],
                    'name'    => @$settings['email_from_name'],
                ],
            ]));
        }
       
    }
}
