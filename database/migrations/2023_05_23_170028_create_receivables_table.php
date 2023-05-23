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
        Schema::create('receivables', function (Blueprint $table) {
            $table->integer('id', true);
            $table->timestamp('transaction_date')->nullable();
            $table->integer('loan_id')->nullable();
            $table->decimal('total_loan', 10, 0)->nullable();
            $table->decimal('payment_amount', 10, 0)->nullable();
            $table->decimal('outstanding', 10, 0)->nullable()->virtualAs('(`total_loan` - `payment_amount`)');
            $table->tinyInteger('is_jurnal')->nullable();
            $table->integer('coa_id_source')->nullable();
            $table->integer('coa_id_destination')->nullable();
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
        Schema::dropIfExists('receivables');
    }
};
