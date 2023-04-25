<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineryExpensePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machinery_expense_pivot_table', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machinery_id')->index();
            $table->unsignedBigInteger('expense_id');

            $table->string('reference');
            $table->string('folio')->nullable();
            $table->double('amount')->default(0);
            $table->date('applied_date')->nullable();

            $table->foreign('machinery_id')->references('id')
                ->on('machineries')->onDelete('cascade');
            $table->foreign('expense_id')->references('id')
                ->on('expense_catalogs')->onDelete('cascade');
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
        Schema::dropIfExists('machinery_expense_pivot_table');
    }
}
