<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMealsToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('meals',8)->after('accommodation')->default('opt-out');
        });
        Schema::table('registrations', function($table) {
            $table->boolean('meals')->after('accommodation')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('meals');
        });
        Schema::table('registrations', function($table) {
            $table->dropColumn('meals');
        });
    }
}
