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
        Schema::create('project_plans', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code', 25);
            $table->string('name', 50);
            $table->string('description', 1000)->nullable();
            $table->timestamp('start_project')->nullable();
            $table->timestamp('est_end_project')->nullable();
            $table->timestamp('end_project')->nullable();
            $table->float('progress', 10, 0)->nullable();
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
        Schema::dropIfExists('project_plans');
    }
};
