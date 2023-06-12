<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW `v_coa_master_details` AS select `whjproperty`.`coa_level_thirds`.`level_first_id` AS `level_first_id`,`whjproperty`.`coa_level_thirds`.`level_second_id` AS `level_second_id`,`whjproperty`.`coa_level_thirds`.`id` AS `level_third_id`,`whjproperty`.`coa_level_firsts`.`code` AS `level_first_code`,`whjproperty`.`coa_level_seconds`.`code` AS `level_second_code`,`whjproperty`.`coa_level_thirds`.`code` AS `level_third_code`,`whjproperty`.`coa_level_firsts`.`name` AS `level_first_name`,`whjproperty`.`coa_level_seconds`.`name` AS `level_second_name`,`whjproperty`.`coa_level_thirds`.`name` AS `level_third_name`,`whjproperty`.`coa_level_thirds`.`balance` AS `balance` from ((`whjproperty`.`coa_level_firsts` join `whjproperty`.`coa_level_seconds`) join `whjproperty`.`coa_level_thirds` on(((`whjproperty`.`coa_level_firsts`.`id` = `whjproperty`.`coa_level_thirds`.`level_first_id`) and (`whjproperty`.`coa_level_seconds`.`id` = `whjproperty`.`coa_level_thirds`.`level_second_id`))))");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `v_coa_master_details`");
    }
};
