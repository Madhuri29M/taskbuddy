<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title'); 
            $table->longText('content'); 
            $table->string('is_sent')->default('1')->comment('0 = not sent, 1 = sent');
            $table->string('is_read')->default('0')->comment('0 = not read, 1 = read');  
            $table->string('slug')->nullable();
            $table->bigInteger('buddy_id')->unsigned()->nullable();
            $table->foreign('buddy_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('task_id')->unsigned()->nullable();
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
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
        Schema::dropIfExists('notifications');
    }
}
