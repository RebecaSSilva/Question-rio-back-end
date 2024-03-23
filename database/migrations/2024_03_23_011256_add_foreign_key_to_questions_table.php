<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToQuestionsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('forms')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->foreignId('form_id')->constrained()->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('forms')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropForeign(['form_id']);
            });
        }
    }
}
