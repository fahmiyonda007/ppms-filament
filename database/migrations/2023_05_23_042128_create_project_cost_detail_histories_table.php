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
        Schema::create('project_cost_detail_histories', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('vendor_id');
            $table->integer('coa_id');
            $table->string('uom');
            $table->decimal('unit_price', 10, 0);
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
        Schema::dropIfExists('project_cost_detail_histories');
    }
};
