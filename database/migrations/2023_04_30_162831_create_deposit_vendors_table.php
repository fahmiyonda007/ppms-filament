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
        Schema::create('deposit_vendors', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('transaction_code', 20)->nullable();
            $table->timestamp('transaction_date')->useCurrentOnUpdate()->useCurrent();
            $table->integer('vendor_id')->nullable();
            $table->integer('coa_id_source')->nullable();
            $table->integer('coa_id_destination')->nullable();
            $table->decimal('amount', 10, 0)->nullable();
            $table->decimal('source_start_balance', 10, 0)->nullable();
            $table->decimal('source_end_balance', 10, 0)->nullable();
            $table->decimal('destination_start_balance', 10, 0)->nullable();
            $table->decimal('destination_end_balance', 10, 0)->nullable();
            $table->string('description', 500)->nullable();
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
        Schema::dropIfExists('deposit_vendors');
    }
};
