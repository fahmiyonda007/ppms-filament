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
        Schema::create('general_journal_details', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('jurnal_id')->nullable();
            $table->integer('no_inc')->nullable();
            $table->integer('coa_id')->nullable();
            $table->string('coa_code', 10)->nullable();
            $table->decimal('debet_amount', 10, 0)->nullable();
            $table->decimal('credit_amount', 10, 0)->nullable();
            $table->string('description', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_journal_details');
    }
};
