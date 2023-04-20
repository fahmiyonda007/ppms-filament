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
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nik', 15);
            $table->string('ktp', 16)->nullable();
            $table->string('employee_name', 50)->nullable();
            $table->string('phone', 13)->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('department')->nullable();
            $table->timestamp('join_date')->nullable();
            $table->decimal('salary_day', 10, 0)->nullable();
            $table->decimal('overtime', 10, 0)->nullable();
            $table->decimal('total_loan', 10, 0)->nullable();
            $table->integer('bank_account_id', 30)->nullable();
            $table->boolean('is_resign')->nullable();
            $table->timestamp('resign_date')->nullable();
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
        Schema::dropIfExists('employees');
    }
};
