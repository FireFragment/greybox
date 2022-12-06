<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('cs')->nullable();
            $table->text('en')->nullable();
            $table->timestamps();
        });

        Schema::table('events', function($table) {
            $table->foreign('name')->references('id')->on('translations');
            $table->foreign('note')->references('id')->on('translations');
        });

        Schema::table('roles', function($table) {
            $table->foreign('name')->references('id')->on('translations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('events', function($table) {
            $table->dropForeign(['name']);
            $table->dropForeign(['note']);
        });

        Schema::table('roles', function($table) {
            $table->dropForeign(['name']);
        });

        Schema::dropIfExists('translations');
    }
}
