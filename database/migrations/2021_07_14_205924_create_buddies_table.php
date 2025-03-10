<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuddiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buddies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user1')->unsigned()->nullable();
            $table->foreign('user1')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('user2')->unsigned()->nullable();
            $table->foreign('user2')->references('id')->on('users')->onDelete('cascade');
            $table->enum('status',['pending','accepted','rejected'])->defualt('pending');
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
        Schema::dropIfExists('buddies');
    }
}
