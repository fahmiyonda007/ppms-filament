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
        DB::statement("CREATE VIEW `v_general_journal_details` AS select `ppms-filament`.`general_journal_details`.`id` AS `id`,`ppms-filament`.`general_journal_details`.`jurnal_id` AS `jurnal_id`,`ppms-filament`.`general_journals`.`project_plan_id` AS `project_plan_id`,`ppms-filament`.`general_journals`.`transaction_date` AS `transaction_date`,`ppms-filament`.`project_plans`.`code` AS `code`,`ppms-filament`.`project_plans`.`name` AS `name`,`ppms-filament`.`general_journal_details`.`no_inc` AS `no_inc`,`ppms-filament`.`general_journal_details`.`coa_id` AS `coa_id`,`ppms-filament`.`coa_level_firsts`.`code` AS `level_first_code`,`ppms-filament`.`coa_level_seconds`.`code` AS `level_second_code`,`ppms-filament`.`general_journal_details`.`coa_code` AS `level_thirds_code`,`ppms-filament`.`general_journal_details`.`debet_amount` AS `debet_amount`,`ppms-filament`.`general_journal_details`.`credit_amount` AS `credit_amount`,`ppms-filament`.`general_journal_details`.`description` AS `description`,`ppms-filament`.`coa_level_firsts`.`name` AS `level_first_name`,`ppms-filament`.`coa_level_seconds`.`name` AS `level_second_name`,`ppms-filament`.`coa_level_thirds`.`name` AS `level_thirds_name` from (((((`ppms-filament`.`general_journal_details` join `ppms-filament`.`coa_level_thirds` on((`ppms-filament`.`general_journal_details`.`coa_id` = `ppms-filament`.`coa_level_thirds`.`id`))) join `ppms-filament`.`general_journals` on((`ppms-filament`.`general_journal_details`.`jurnal_id` = `ppms-filament`.`general_journals`.`id`))) join `ppms-filament`.`project_plans` on((`ppms-filament`.`general_journals`.`project_plan_id` = `ppms-filament`.`project_plans`.`id`))) join `ppms-filament`.`coa_level_seconds` on((`ppms-filament`.`coa_level_thirds`.`level_second_id` = `ppms-filament`.`coa_level_seconds`.`id`))) join `ppms-filament`.`coa_level_firsts` on((`ppms-filament`.`coa_level_thirds`.`level_first_id` = `ppms-filament`.`coa_level_firsts`.`id`))) order by `ppms-filament`.`general_journal_details`.`jurnal_id`,`ppms-filament`.`general_journal_details`.`no_inc`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `v_general_journal_details`");
    }
};
