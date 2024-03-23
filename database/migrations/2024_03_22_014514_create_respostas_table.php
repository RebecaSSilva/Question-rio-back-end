<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRespostasTable extends Migration
{

    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->onDelete('cascade');
            $table->string('field_slug');
            $table->string('field_title');
            $table->string('field_type');
            $table->longText('value');
            $table->boolean('is_last')->default(false);
            $table->text('value_key')->nullable();
            $table->uuid('public_user_id')->unique();
            $table->timestamps();
            $table->index('public_user_id'); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('answers');
    }
}
