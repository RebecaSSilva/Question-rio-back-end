<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestoesTable extends Migration 
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('field_slug')->unique();
            $table->string('field_title');
            $table->string('field_description');
            $table->string('field_type');
            $table->boolean('is_last')->default(false);
            $table->boolean('mandatory')->default(false);
            $table->text('value_key')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
