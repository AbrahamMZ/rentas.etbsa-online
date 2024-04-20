<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJdfValueToMachineries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machineries', function (Blueprint $table) {
            $table->double('jdf_amount', 12, 2)->default(0)->nullable();
            $table->date('jdf_start_date')->nullable();
            $table->date('jdf_end_date')->nullable();
            $table->integer('jdf_terms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machineries', function (Blueprint $table) {
            //
        });
    }
}
