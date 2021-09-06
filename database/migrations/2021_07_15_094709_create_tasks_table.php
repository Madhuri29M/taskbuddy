<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('assigned_by')->unsigned()->nullable();
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('assigned_to')->unsigned()->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
            $table->longText('title'); 
            $table->longText('description')->nullable(); 
            $table->date('due_date');
            $table->time('due_time');
            $table->date('completed_date')->nullable();
            $table->time('completed_time')->nullable();
            $table->datetime('rescheduled_at')->nullable();
            $table->enum('status',['pending','accepted','rejected','completed','trashed'])->defualt('pending');
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
        Schema::dropIfExists('tasks');
    }
}
