<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineryFixesCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machinery_fixes_costs', function (Blueprint $table) {
            $table->id();

            $table->double('amount')->default(0);
            $table->unsignedBigInteger('machinery_id')->index();
            $table->unsignedBigInteger('fixes_costs_id')->index();
            $table->foreign('machinery_id')->references('id')
                ->on('machineries')->onDelete('cascade');
            $table->foreign('fixes_costs_id')->references('id')
                ->on('fixes_costs')->onDelete('cascade');


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
        Schema::dropIfExists('machinery_fixes_costs');
    }
}