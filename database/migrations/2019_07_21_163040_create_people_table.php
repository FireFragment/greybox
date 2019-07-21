<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 31);
            $table->string('surname', 31);
            $table->date('birthdate');
            $table->string('id_number', 31)->nullable();
            $table->string('street', 255);
            $table->string('city', 127);
            $table->string('zip', 7);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function($table) {
            $table->unsignedInteger('person')->after('password')->nullable();
            $table->foreign('person')->references('id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropForeign(['person']);
            $table->dropColumn('person');
        });

        Schema::dropIfExists('people');
    }
}
