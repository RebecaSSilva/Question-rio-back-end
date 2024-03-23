<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulariosTable extends Migration
{
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade'); 
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('button_color')->nullable();
            $table->string('question_color')->nullable();
            $table->string('answer_color')->nullable();
            $table->string('background_color')->nullable();
            $table->binary('background_image')->nullable();
            $table->binary('logo')->nullable();
            $table->string('font')->nullable();
            $table->timestamps();
            $table->index('user_id'); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('forms');
    }
}
