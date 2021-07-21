<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('text')->nullable();
            $table->foreign('text')->references('id')->on('translations');
            $table->unsignedInteger('short_text')->nullable();
            $table->foreign('short_text')->references('id')->on('translations');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('motion_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('name')->nullable();
            $table->foreign('name')->references('id')->on('translations');
            $table->timestamps();
        });

        Schema::create('motion_categories_motions', function (Blueprint $table) {
            $table->foreignId('motion_category')->constrained('motion_categories');
            $table->foreignId('motion')->constrained('motions');
            $table->unique(['motion_category', 'motion']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motion_categories_motions');
        Schema::dropIfExists('motion_categories');
        Schema::dropIfExists('motions');
    }
}
