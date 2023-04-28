<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaseIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lease_incomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machinery_id')->index();

            $table->string("contract_lease");
            $table->string("reference");
            $table->integer("term_lease")->default(1);
            $table->double("amount")->default(0);
            $table->double("balance")->default(0);
            $table->date("start_date");
            $table->date("end_date");
            $table->double("total_income")->default(0);

            $table->foreign('machinery_id')->references('id')
                ->on('machineries')->onDelete('cascade');

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
        Schema::dropIfExists('lease_incomes');
    }
}