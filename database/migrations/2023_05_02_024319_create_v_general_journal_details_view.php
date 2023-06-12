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
        DB::statement("CREATE VIEW `v_general_journal_details` AS select `whjproperty`.`general_journal_details`.`id` AS `id`,`whjproperty`.`general_journal_details`.`jurnal_id` AS `jurnal_id`,`whjproperty`.`general_journals`.`project_plan_id` AS `project_plan_id`,`whjproperty`.`general_journals`.`transaction_date` AS `transaction_date`,`whjproperty`.`project_plans`.`code` AS `code`,`whjproperty`.`project_plans`.`name` AS `name`,`whjproperty`.`general_journal_details`.`no_inc` AS `no_inc`,`whjproperty`.`general_journal_details`.`coa_id` AS `coa_id`,`whjproperty`.`coa_level_firsts`.`code` AS `level_first_code`,`whjproperty`.`coa_level_seconds`.`code` AS `level_second_code`,`whjproperty`.`general_journal_details`.`coa_code` AS `level_thirds_code`,`whjproperty`.`general_journal_details`.`debet_amount` AS `debet_amount`,`whjproperty`.`general_journal_details`.`credit_amount` AS `credit_amount`,`whjproperty`.`general_journal_details`.`description` AS `description`,`whjproperty`.`coa_level_firsts`.`name` AS `level_first_name`,`whjproperty`.`coa_level_seconds`.`name` AS `level_second_name`,`whjproperty`.`coa_level_thirds`.`name` AS `level_thirds_name` from (((((`whjproperty`.`general_journal_details` join `whjproperty`.`coa_level_thirds` on((`whjproperty`.`general_journal_details`.`coa_id` = `whjproperty`.`coa_level_thirds`.`id`))) join `whjproperty`.`general_journals` on((`whjproperty`.`general_journal_details`.`jurnal_id` = `whjproperty`.`general_journals`.`id`))) join `whjproperty`.`project_plans` on((`whjproperty`.`general_journals`.`project_plan_id` = `whjproperty`.`project_plans`.`id`))) join `whjproperty`.`coa_level_seconds` on((`whjproperty`.`coa_level_thirds`.`level_second_id` = `whjproperty`.`coa_level_seconds`.`id`))) join `whjproperty`.`coa_level_firsts` on((`whjproperty`.`coa_level_thirds`.`level_first_id` = `whjproperty`.`coa_level_firsts`.`id`))) order by `whjproperty`.`general_journal_details`.`jurnal_id`,`whjproperty`.`general_journal_details`.`no_inc`");
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
