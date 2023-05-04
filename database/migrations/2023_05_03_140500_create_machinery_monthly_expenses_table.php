<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineryMonthlyExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machinery_monthly_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monthly_expense_types_id')->index();
            $table->unsignedBigInteger('machinery_id')->index();

            $table->text('description')->nullable();
            $table->enum("base_cost_type", ['none', 'invoice', 'total_cost'])->default('none');
            $table->double('base_cost_amount')->default(0);
            $table->decimal('percent')->default(0);
            $table->double('amount')->default(0);

            $table->foreign('monthly_expense_types_id')->references('id')
                ->on('expense_catalogs')->onDelete('cascade');
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
        Schema::dropIfExists('machinery_monthly_expenses');
    }
}