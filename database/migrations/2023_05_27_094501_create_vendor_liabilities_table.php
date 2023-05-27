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
        Schema::create('vendor_liabilities', function (Blueprint $table) {
            $table->integer('id', true);
            $table->timestamp('transaction_date')->useCurrentOnUpdate()->useCurrent();
            $table->string('transaction_code', 50)->nullable();
            $table->string('scope_of_work')->nullable();
            $table->string('description', 500)->nullable();
            $table->decimal('est_price', 10, 0)->nullable();
            $table->decimal('deal_price', 10, 0)->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('est_end_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->tinyInteger('project_status')->nullable();
            $table->decimal('outstanding', 10, 0)->nullable();
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
        Schema::dropIfExists('vendor_liabilities');
    }
};
