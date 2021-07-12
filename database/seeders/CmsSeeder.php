<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cms;
use App\Models\Translations\CmsTranslation;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

       	Cms::updateOrCreate(
            ['id'=> 1,'slug' => 'terms_conditions' ,'display_order' => '1' , 'status' => '1'],
        );

        Cms::updateOrCreate(
            ['id'=> 2,'slug' => 'privacy_policy' ,'display_order' => '1' , 'status' => '1'],
        );	

        Cms::updateOrCreate(
            ['id'=> 3,'slug' => 'about_us' ,'display_order' => '1' , 'status' => '1'],
        );  


		//1
	    CmsTranslation::updateOrCreate(
            ['cms_id' => '1','locale' => 'en'],
            [
                'content'  => '<p align="CENTER"><font size="4"><b>TERMS &amp; CONDITIONS</b></font></p> 
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>',
             	'page_name'   =>  'Terms & Conditions',
          	],

        );

	    // 2
	    CmsTranslation::updateOrCreate(
            ['cms_id' => '2','locale' => 'en'],
            [
                'content'  => '<p align="CENTER"><font size="4"><b>PRIVACY POLICY</b></font></p>

                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>',
             	'page_name'   =>  'Privacy Policy',
          	],
        );



	    // 3
	    CmsTranslation::updateOrCreate(
            ['cms_id' => '3','locale' => 'en'],
            [
             	'content'  => '<p align="CENTER"><font size="4"><b>About Us</b></font></p>

                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>',
             	'page_name'   =>  'About Us',
          	],
        );

	}

}

