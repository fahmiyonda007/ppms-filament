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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CashFlow`(IN `id` int, IN `start_period` date, 
  IN `end_period` date)
BEGIN DECLARE total_pendapatan, 
total_biaya numeric;
DROP 
  TABLE IF EXISTS temp_table;
CREATE TEMPORARY TABLE temp_table(
  inc int primary key auto_increment, 
  rowcode varchar(10), 
  description varchar(100), 
  amount NUMERIC, 
  total NUMERIC
);
select 
	transaction_date,
	jurnal_code,
	reference_code as transaction_code,
	jurnal_description as description,
	0 as start_balance,
	credit_amount as cash_in,
	debet_amount as cash_out,
	0 as end_balance
from v_general_journal_details
where level_first_code = '1' and level_second_code='01' and coa_id = id
order by transaction_date desc;

END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS SP_CashFlow");
    }
};
