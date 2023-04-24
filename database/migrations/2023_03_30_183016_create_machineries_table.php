<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machineries', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('brand_id')->index();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('status_id')->default(1);

            $table->string('name');
            $table->string('equipment_serial')->unique()->nullable();
            $table->string('economic_serial')->nullable();
            $table->string('engine_serial')->unique()->nullable();

            $table->string('slug')->nullable();
            $table->text('description')->nullable();

            $table->decimal('total_cost_price', 12, 2)->nullable();
            $table->decimal('current_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();

            $table->integer('months_depreciation')->default(48);

            $table->date('acquisition_date')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('sale_date')->nullable();


            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('status_id')->references('id')->on('status');
            // $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');

            $table->softDeletes();
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
        Schema::dropIfExists('machineries');
    }
}