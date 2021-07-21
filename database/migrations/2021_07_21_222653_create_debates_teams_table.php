<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebatesTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debates_teams', function (Blueprint $table) {
            $table->foreignId('debate')->constrained('debates');
            $table->unsignedInteger('team');
            $table->foreign('team')->references('id')->on('teams');
            $table->enum('side', ['a', 'n']);
            $table->tinyInteger('points')->nullable();
            $table->decimal('tab', 5, 3)->nullable();
            $table->unique(['debate', 'team']);
            $table->unique(['debate', 'side']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debates_teams');
    }
}
