<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoiceTextToEventAndInvoiceToRegistration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function($table) {
            $table->unsignedInteger('invoice_text')->after('hard_deadline')->nullable();
            $table->foreign('invoice_text')->references('id')->on('translations');
        });

        Schema::table('registrations', function($table) {
            $table->unsignedInteger('invoice')->after('registered_by')->nullable();
            $table->foreign('invoice')->references('id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function($table) {
            $table->dropForeign(['invoice_text']);
            $table->dropColumn('invoice_text');
        });

        Schema::table('registrations', function($table) {
            $table->dropForeign(['invoice']);
            $table->dropColumn('invoice');
        });
    }
}
