<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fakturoid_id');
            $table->string('name', 255);
            $table->string('street', 255)->nullable();
            $table->string('street2', 255)->nullable();
            $table->string('city', 127)->nullable();
            $table->string('zip', 15)->nullable();
            $table->string('country', 2)->defatult('CZ');
            $table->string('registration_no', 63)->nullable();
            $table->string('full_name', 63)->nullable();
            $table->string('email', 63)->nullable();
            $table->unsignedInteger('user');
            $table->foreign('user')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
