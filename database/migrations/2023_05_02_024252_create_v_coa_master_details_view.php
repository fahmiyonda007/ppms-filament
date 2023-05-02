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
        DB::statement("CREATE VIEW `v_coa_master_details` AS select `ppms-filament`.`coa_level_thirds`.`level_first_id` AS `level_first_id`,`ppms-filament`.`coa_level_thirds`.`level_second_id` AS `level_second_id`,`ppms-filament`.`coa_level_thirds`.`id` AS `level_third_id`,`ppms-filament`.`coa_level_firsts`.`code` AS `level_first_code`,`ppms-filament`.`coa_level_seconds`.`code` AS `level_second_code`,`ppms-filament`.`coa_level_thirds`.`code` AS `level_third_code`,`ppms-filament`.`coa_level_firsts`.`name` AS `level_first_name`,`ppms-filament`.`coa_level_seconds`.`name` AS `level_second_name`,`ppms-filament`.`coa_level_thirds`.`name` AS `level_third_name`,`ppms-filament`.`coa_level_thirds`.`balance` AS `balance`,`ppms-filament`.`coa_level_thirds`.`normal_position` AS `normal_position` from ((`ppms-filament`.`coa_level_firsts` join `ppms-filament`.`coa_level_seconds`) join `ppms-filament`.`coa_level_thirds` on(((`ppms-filament`.`coa_level_firsts`.`id` = `ppms-filament`.`coa_level_thirds`.`level_first_id`) and (`ppms-filament`.`coa_level_seconds`.`id` = `ppms-filament`.`coa_level_thirds`.`level_second_id`))))");
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
