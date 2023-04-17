<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->double('amount')->default(0);
            $table->double('balance')->default(0);
            $table->integer('term_months')->default(1);
            $table->integer('residual_percent')->default(0);
            $table->double('residual_amount')->default(0);
            $table->double('interest_rate')->default(0);

            $table->date('lease_start_date')->nullable();
            $table->date('lease_end_date')->nullable();

            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->references('id')->on('status');

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
        Schema::dropIfExists('leases');
    }
}
