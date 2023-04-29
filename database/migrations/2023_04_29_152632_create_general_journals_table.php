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
        Schema::create('general_journals', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('project_plan_id')->nullable();
            $table->string('jurnal_id', 25)->nullable();
            $table->string('reference_code', 25)->nullable();
            $table->string('description', 500)->nullable();
            $table->timestamp('transaction_date')->useCurrentOnUpdate()->useCurrent();
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
        Schema::dropIfExists('general_journals');
    }
};
