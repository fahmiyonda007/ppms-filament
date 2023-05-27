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
        Schema::create('vendor_liabilitie_payments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('vendor_liabilities_id')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->string('transaction_code')->nullable();
            $table->string('transaction_type', 50)->nullable();
            $table->integer('inc')->nullable();
            $table->integer('coa_id_source')->nullable();
            $table->integer('coa_id_destination')->nullable();
            $table->decimal('amount', 10, 0)->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('is_jurnal')->nullable();
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
        Schema::dropIfExists('vendor_liabilitie_payments');
    }
};
