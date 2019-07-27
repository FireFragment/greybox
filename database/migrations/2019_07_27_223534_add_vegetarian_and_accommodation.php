<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVegetarianAndAccommodation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function($table) {
            $table->boolean('vegetarian')->after('zip')->default(0);
        });
        Schema::table('registrations', function($table) {
            $table->boolean('accommodation')->after('role')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function($table) {
            $table->dropColumn('vegetarian');
        });
        Schema::table('registrations', function($table) {
            $table->dropColumn('accommodation');
        });
    }
}
