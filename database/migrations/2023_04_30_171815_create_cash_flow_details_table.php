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
        Schema::create('cash_flow_details', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('cash_flow_id')->nullable();
            $table->integer('coa_id')->nullable();
            $table->decimal('amount', 10, 0)->nullable();
            $table->string('description', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_flow_details');
    }
};
