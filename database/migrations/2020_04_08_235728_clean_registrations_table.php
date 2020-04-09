<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CleanRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registrations', function ($table) {
            $table->dropColumn('name');
            $table->dropColumn('surname');
            $table->dropColumn('birthdate');
            $table->dropColumn('id_number');
            $table->dropColumn('street');
            $table->dropColumn('city');
            $table->dropColumn('zip');
            $table->dropColumn('event');
            $table->renameColumn('event_id','event');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registrations', function ($table) {
            $table->string('name', 31);
            $table->string('surname', 31);
            $table->date('birthdate');
            $table->string('id_number', 31)->nullable();
            $table->string('street', 255);
            $table->string('city', 127);
            $table->string('zip', 7);
            $table->renameColumn('event','event_id');
        });

        // Necessary to avoid duplicate column name
        Schema::table('registrations', function ($table) {
            $table->string('event', 31);
        });
    }
}
