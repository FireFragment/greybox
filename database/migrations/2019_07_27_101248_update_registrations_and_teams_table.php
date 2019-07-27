<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRegistrationsAndTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function($table) {
            $table->string('event',15)->nullable()->change();
            $table->unsignedInteger('registered_by')->nullable()->change();
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
            $table->dropColumn('confirmed');
        });

        Schema::table('registrations', function($table) {
            $table->string('name',31)->nullable()->change();
            $table->string('surname',31)->nullable()->change();
            $table->date('birthdate')->nullable()->change();
            $table->string('id_number',31)->nullable()->change();
            $table->string('street',255)->nullable()->change();
            $table->string('city',127)->nullable()->change();
            $table->string('zip',7)->nullable()->change();
            $table->string('event',31)->nullable()->change();
            $table->unsignedInteger('person')->after('id')->nullable();
            $table->foreign('person')->references('id')->on('people');

            $table->unique(['person', 'event_id']);
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
            $table->unsignedInteger('event_id')->after('event')->nullable();
            $table->foreign('event_id')->references('id')->on('events');
            $table->boolean('confirmed')->default(0);
        });

        Schema::table('registrations', function($table) {
            $table->dropForeign(['person']);
            $table->dropColumn('person');
        });
    }
}
