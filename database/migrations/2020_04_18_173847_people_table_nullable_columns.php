<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PeopleTableNullableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function($table) {
            $table->date('birthdate')->nullable()->change();
            $table->string('street', 255)->nullable()->change();
            $table->string('city', 127)->nullable()->change();
            $table->string('zip', 7)->nullable()->change();
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
            $table->date('birthdate')->change();
            $table->string('street', 255)->change();
            $table->string('city', 127)->change();
            $table->string('zip', 7)->change();
        });
    }
}
