<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('password');
            $table->string('designation')->nullable();
            $table->string('company_name')->nullable();
            $table->string('bio')->nullable();
            $table->string('user_type');
            $table->string('profile_image')->nullable();
            $table->string('social_id',255)->nullable();
            $table->string('social_type',255)->default('normal');
            $table->string('registered_on',255)->default('app');
            $table->string('preferred_language')->default('en');
            $table->string('firebase_token')->nullable();
            $table->enum('status',['active','inactive','pending','blocked'])->default('pending');
            $table->enum('verified',['0','1'])->default('0')->comment('0 = not verified,1 = verified');
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
