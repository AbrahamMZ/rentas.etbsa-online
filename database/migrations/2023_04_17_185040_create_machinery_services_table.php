<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineryServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machinery_services', function (Blueprint $table) {
            $table->id();
            $table->string('no_order')->nullable();
            $table->double('value')->default(0);
            $table->text('descriptions')->nullable();
            $table->date('worked_date')->nullable();

            $table->string('estatus_id')->nullable();

            $table->unsignedBigInteger('type_service_id')->index();
            $table->foreign('type_service_id')->references('id')
                ->on('types_services')->onDelete('cascade');

            $table->unsignedBigInteger('machinery_id')->index();
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
        Schema::dropIfExists('machinery_services');
    }
}