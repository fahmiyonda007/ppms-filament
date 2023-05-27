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
        Schema::create('project_payments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('transaction_code', 20)->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->integer('project_plan_id')->nullable();
            $table->integer('project_plan_detail_id')->nullable();
            $table->integer('booking_by')->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->string('kpr_type', 50)->nullable();
            $table->integer('sales_id')->nullable();
            $table->string('description', 2000)->nullable();
            $table->tinyInteger('is_jurnal')->nullable()->default(0);
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
        Schema::dropIfExists('project_payments');
    }
};
