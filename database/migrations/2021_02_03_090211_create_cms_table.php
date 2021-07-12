<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug');
            $table->integer('display_order');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

         Schema::create('cms_translations', function(Blueprint $table){
            $table->increments('id');
            $table->bigInteger('cms_id')->unsigned();
            $table->longText('page_name');
            $table->longText('content');
            $table->string('locale')->index();
            $table->unique(['cms_id', 'locale']);
            $table->foreign('cms_id')->references('id')->on('cms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_translations');
        Schema::dropIfExists('cms');
    }
}
