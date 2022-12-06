<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('name')->nullable();
            $table->timestamps();
        });

        Schema::table('registrations', function($table) {
            $table->unsignedInteger('role')->after('event_id')->nullable();
            $table->foreign('role')->references('id')->on('roles');
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
            $table->dropForeign(['role']);
            $table->dropColumn('role');
        });

        Schema::dropIfExists('roles');
    }
}
