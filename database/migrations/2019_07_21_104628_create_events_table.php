<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            // TODO: solve translations
            $table->string('name', 127);
            $table->date('beginning');
            $table->date('end');
            $table->string('place', 127);
            $table->dateTime('soft_deadline');
            $table->dateTime('hard_deadline');
            // $table->unsignedInteger('organizer');
            $table->text('note')->nullable();
            // TODO: solve organizers
            // $table->foreign('organizer')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });

        Schema::table('registrations', function($table) {
            // TODO: rename to just event
            $table->unsignedInteger('event_id')->after('event')->nullable();
            $table->foreign('event_id')->references('id')->on('events');
        });

        Schema::table('teams', function($table) {
            // TODO: rename to just event
            $table->unsignedInteger('event_id')->after('event')->nullable();
            $table->foreign('event_id')->references('id')->on('events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registrations', function($table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });

        Schema::table('teams', function($table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });

        Schema::dropIfExists('events');
    }
}
