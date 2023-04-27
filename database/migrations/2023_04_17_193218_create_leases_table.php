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
            $table->unsignedBigInteger('machinery_id')->index();
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('customer_id');


            $table->string('customer_name');

            $table->double('monthly_fee')->default(0);
            $table->double('daily_fee')->default(0);
            
            $table->double('balance')->default(0);
            $table->integer('residual_percent')->default(0);
            $table->double('residual_amount')->default(0);
            
            $table->double('interest_rate')->default(0);
            
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('term_months')->default(1);

            $table->foreign('machinery_id')->references('id')
            ->on('machineries')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('status');

            $table->timestamps();
            $table->softDeletes();
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
