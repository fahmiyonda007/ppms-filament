<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_cost_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_cost_id')->nullable();
            $table->string('coa_id', 10)->nullable();
            $table->string('uom', 10)->nullable();
            $table->float('qty', 10, 0)->nullable();
            $table->decimal('unit_price', 10, 0)->nullable();
            $table->decimal('amount', 10, 0)->nullable();
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_cost_details');
    }
};
