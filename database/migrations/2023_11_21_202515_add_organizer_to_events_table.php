<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('organizer', 31)->default('adk')->after('id');
        });

        DB::table('events')->where('pds', true)->update(['organizer' => 'pds']);

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('pds');
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
            $table->boolean('pds')->default(false)->after('id');
        });

        DB::table('events')->where('organizer', 'pds')->update(['pds' => true]);

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('organizer');
        });
    }
};
