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
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('transaction_code', 20)->nullable();
            $table->timestamp('transaction_date')->useCurrentOnUpdate()->useCurrent();
            $table->integer('project_plan_id')->nullable();
            $table->integer('coa_id')->nullable();
            $table->string('cash_flow_type', 50)->nullable();
            $table->string('description', 500)->nullable();
            $table->boolean('is_jurnal')->nullable();
            $table->timestamps();
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_flows');
    }
};
