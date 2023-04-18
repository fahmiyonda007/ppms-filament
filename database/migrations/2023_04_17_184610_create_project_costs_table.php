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
        Schema::create('project_costs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('project_plan_id')->nullable();
            $table->string('transaction_code', 25)->nullable();
            $table->string('description', 500)->nullable();
            $table->timestamp('order_date')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('payment_status')->nullable();
            $table->integer('vendor_id')->nullable();
            $table->string('coa_id_source1', 10)->nullable();
            $table->string('coa_id_source2', 10)->nullable();
            $table->string('coa_id_source3', 10)->nullable();
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
        Schema::dropIfExists('project_costs');
    }
};
