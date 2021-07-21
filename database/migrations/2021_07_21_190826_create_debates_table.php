<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('event')->nullable();
            $table->foreign('event')->references('id')->on('events');
            $table->foreignId('motion')->constrained('motions');
            $table->dateTime('date');
            $table->string('place', 127);
            $table->boolean('affirmativeWinner')->nullable();
            $table->boolean('unanimousDecision')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('debates');
    }
}
