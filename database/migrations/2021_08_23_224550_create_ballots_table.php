<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBallotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ballots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debate')->nullable();
            $table->unsignedInteger('adjudicator')->nullable();
            $table->foreign('adjudicator')->references('id')->on('people');
            $table->string('filename');
            $table->string('url');
            $table->unsignedInteger('old_greybox_id')->nullable();
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
        Schema::dropIfExists('ballots');
    }
}
