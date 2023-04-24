<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineryServiceExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machinery_service_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machinery_id')->index();
            $table->unsignedBigInteger('status_id');

            $table->string('name');
            $table->double('amount')->default(0);
            $table->string('description')->nullable();
            $table->date('work_start_date')->nullable();
            $table->date('work_end_date')->nullable();

            $table->foreign('machinery_id')->references('id')
                ->on('machineries')->onDelete('cascade');
            $table->foreign('status_id')->references('id')
                ->on('status')->onDelete('cascade');
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
        Schema::dropIfExists('machinery_service_expenses');
    }
}