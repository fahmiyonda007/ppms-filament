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
        Schema::create('project_plan_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_plan_id')->nullable();
            $table->string('unit_kavling', 20)->nullable();
            $table->decimal('unit_price', 10, 0)->nullable();
            $table->string('description', 2000)->nullable();
            $table->integer('booking_by')->nullable();
            $table->dateTime('booking_date')->nullable();
            $table->decimal('deal_price', 10, 0)->nullable();
            $table->decimal('down_payment', 10, 0)->nullable();
            $table->string('payment_type', 25)->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->decimal('tax', 10, 0)->nullable();
            $table->decimal('notary_fee', 10, 0)->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->decimal('commission', 10, 0)->nullable();
            $table->decimal('other_commission', 10, 0)->nullable();
            $table->decimal('added_bonus', 10, 0)->nullable();
            $table->decimal('net_price', 10, 0)->nullable();
            $table->string('no_shm')->nullable();
            $table->string('no_imb')->nullable();
            $table->double('land_width')->nullable();
            $table->double('building_width')->nullable();
            $table->integer('sales_id')->nullable();
            $table->integer('coa_id_source')->nullable();
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
        Schema::dropIfExists('project_plan_details');
    }
};
