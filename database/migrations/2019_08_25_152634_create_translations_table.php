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

        // TODO: drop duplicate columns
        Schema::table('events', function($table) {
            $table->string('name', 127)->nullable()->change();
            $table->unsignedInteger('name_translation')->after('name')->nullable();
            $table->foreign('name_translation')->references('id')->on('translations');
            $table->unsignedInteger('note_translation')->after('note')->nullable();
            $table->foreign('note_translation')->references('id')->on('translations');
        });

        Schema::table('roles', function($table) {
            $table->unsignedInteger('name')->nullable()->change();
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
            $table->string('name', 127)->change();
            $table->dropForeign(['name_translation']);
            $table->dropColumn('name_translation');
            $table->dropForeign(['note_translation']);
            $table->dropColumn('note_translation');
        });

        Schema::table('roles', function($table) {
            $table->string('name', 31)->change();
            $table->dropForeign(['name']);
        });

        Schema::dropIfExists('translations');
    }
}
