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
        Schema::create('employee_payroll_details', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('payroll_id')->nullable();
            $table->integer('employee_id')->nullable();
            $table->string('employee_nik', 15)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('salary_type', 50)->nullable();
            $table->integer('total_days')->nullable();
            $table->decimal('unit_price', 10, 0)->nullable();
            $table->integer('total_days_overtime')->nullable();
            $table->decimal('overtime', 10, 0)->nullable();
            $table->integer('total_days_support')->nullable();
            $table->decimal('support_price', 10, 0)->nullable();
            $table->integer('total_days_cor')->nullable();
            $table->decimal('cor_price', 10, 0)->nullable();
            $table->decimal('total_loan', 10, 0)->nullable();
            $table->decimal('loan_payment', 10, 0)->nullable();
            $table->decimal('outstanding', 10, 0)->nullable()->virtualAs('(`total_loan` - `loan_payment`)');
            $table->decimal('total_gross_salary', 10, 0)->nullable()->virtualAs('((((`total_days` * `unit_price`) + (`total_days_overtime` * `overtime`)) + (`total_days_support` * `support_price`)) + (`total_days_cor` * `cor_price`))');
            $table->decimal('total_net_salary', 10, 0)->nullable()->virtualAs('(((((`total_days` * `unit_price`) + (`total_days_overtime` * `overtime`)) + (`total_days_support` * `support_price`)) + (`total_days_cor` * `cor_price`)) - `loan_payment`)');
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
        Schema::dropIfExists('employee_payroll_details');
    }
};
