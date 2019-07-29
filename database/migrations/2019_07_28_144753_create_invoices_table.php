<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fakturoid_id');
            $table->string('number',31);
            $table->unsignedInteger('client');
            $table->string('status',31);
            $table->date('issued_on');
            $table->date('taxable_fulfillment_due');
            $table->date('due_on');
            $table->string('currency',3);
            $table->string('language',2);
            $table->float('total');
            $table->float('paid_amount');
            $table->foreign('client')->references('id')->on('clients');
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
        Schema::dropIfExists('invoices');
    }
}
