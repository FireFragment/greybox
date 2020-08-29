<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeletedAutofillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deleted_autofills', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user');
            $table->foreign('user')->references('id')->on('users');
            $table->unsignedInteger('person')->nullable();
            $table->foreign('person')->references('id')->on('people');
            $table->unsignedInteger('team')->nullable();
            $table->foreign('team')->references('id')->on('teams');
            $table->timestamps();
            $table->unique(['user', 'person']);
            $table->unique(['user', 'team']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deleted_autofills');
    }
}
