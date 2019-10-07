<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prices', function($table) {
            $table->dropForeign(['role']);
            $table->dropForeign(['event']);
            $table->dropUnique('prices_event_role_unique');
            $table->unsignedInteger('description')->after('role')->nullable();
            $table->foreign('description')->references('id')->on('translations');
            $table->foreign('event')->references('id')->on('events');
            $table->foreign('role')->references('id')->on('roles');
            $table->unique(['event', 'role', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prices', function($table) {
            $table->dropForeign(['role']);
            $table->dropForeign(['event']);
            $table->dropForeign(['description']);
            $table->dropUnique('prices_event_role_description_unique');
            $table->dropColumn('description');
            $table->foreign('event')->references('id')->on('events');
            $table->foreign('role')->references('id')->on('roles');
            $table->unique(['event', 'role']);
        });
    }
}
