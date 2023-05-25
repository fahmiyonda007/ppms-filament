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
        Schema::create('project_payment_details', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('inc')->nullable();
            $table->integer('project_payment_id')->nullable();
            $table->string('category', 50)->nullable();
            $table->integer('coa_id_source')->nullable();
            $table->integer('coa_id_destination')->nullable();
            $table->decimal('amount', 10, 0)->nullable();
            $table->timestamp('transaction_date')->useCurrentOnUpdate()->useCurrent();
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
        Schema::dropIfExists('project_payment_details');
    }
};
