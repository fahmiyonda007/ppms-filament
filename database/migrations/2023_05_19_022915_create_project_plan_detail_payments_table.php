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
        Schema::create('project_plan_detail_payments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('plan_detail_id');
            $table->decimal('amount', 10, 0)->nullable();
            $table->timestamp('transaction_date')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_plan_detail_payments');
    }
};
