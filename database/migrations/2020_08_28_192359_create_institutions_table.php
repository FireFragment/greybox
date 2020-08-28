<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstitutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 127);
            $table->string('short_name', 31);
            $table->string('place', 127);
            $table->unsignedInteger('head')->nullable();
            $table->foreign('head')->references('id')->on('people');
            $table->boolean('application')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::table('people', function($table) {
            $table->foreignId('institution')->after('surname')->nullable()->constrained('institutions');
        });

        Schema::table('teams', function($table) {
            $table->foreignId('institution')->after('name')->nullable()->constrained('institutions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function($table) {
            $table->dropForeign(['institution']);
            $table->dropColumn('institution');
        });

        Schema::table('people', function($table) {
            $table->dropForeign(['institution']);
            $table->dropColumn('institution');
        });

        Schema::dropIfExists('institutions');
    }
}
