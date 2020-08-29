<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDietaryRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dietary_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('name')->nullable();
            $table->foreign('name')->references('id')->on('translations');
            $table->smallInteger('order');
            $table->timestamps();
        });

        Schema::table('people', function($table) {
            $table->foreignId('dietary_requirement')->after('vegetarian')->nullable()->constrained('dietary_requirements');
        });

        Schema::create('events_dietary_requirements', function (Blueprint $table) {
            $table->unsignedInteger('event');
            $table->foreign('event')->references('id')->on('events');
            $table->foreignId('dietary_requirement')->constrained('dietary_requirements');
            $table->unique(['event', 'dietary_requirement']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events_dietary_requirements');

        Schema::table('people', function($table) {
            $table->dropForeign(['dietary_requirement']);
            $table->dropColumn('dietary_requirement');
        });

        Schema::dropIfExists('dietary_requirements');
    }
}
