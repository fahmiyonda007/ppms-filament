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
        Schema::create('cash_transfers', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('transaction_id', 20)->nullable();
            $table->timestamp('transaction_date')->useCurrentOnUpdate()->useCurrent();
            $table->string('description', 500)->nullable();
            $table->integer('coa_id_source')->nullable();
            $table->integer('coa_id_destination')->nullable();
            $table->timestamps();
            $table->decimal('amount', 10, 0)->nullable();
            $table->decimal('source_start_balance', 10, 0)->nullable();
            $table->decimal('source_end_balance', 10, 0)->nullable();
            $table->decimal('destination_start_balance', 10, 0)->nullable();
            $table->decimal('destination_send_balance', 10, 0)->nullable();
            $table->tinyInteger('is_jurnal')->nullable()->default(0);
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
        Schema::dropIfExists('cash_transfers');
    }
};
