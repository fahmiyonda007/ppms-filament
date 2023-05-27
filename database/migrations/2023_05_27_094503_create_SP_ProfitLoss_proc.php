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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ProfitLoss`(IN `project_id` int, IN `start_period` date, 
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

/*pendapatan*/
insert into temp_table(
  rowcode, description, amount, total
) 
SELECT 
  'detail', 
  CONCAT(
    level_thirds_code, \" - \", level_thirds_name
  ) as description, 
  sum(credit_amount) as amount, 
  0 
FROM 
  v_general_journal_details 
WHERE 
  cast(transaction_date as date) between start_period 
  and end_period 
  and project_plan_id =  CASE project_id WHEN 1 THEN project_plan_id ELSE project_id END  
  and level_first_code = 4 
  and credit_amount > 0 
GROUP BY 
  level_thirds_code, 
  level_thirds_name 
ORDER BY 
  level_thirds_code;
SELECT 
  sum(credit_amount) into total_pendapatan 
FROM 
  v_general_journal_details 
WHERE 
  cast(transaction_date as date) between start_period 
  and end_period 
  and project_plan_id =  CASE project_id WHEN 1 THEN project_plan_id ELSE project_id END  
  and level_first_code = 4 
  and credit_amount > 0;
SET 
  total_pendapatan = IFNULL(total_pendapatan, 0);
insert into temp_table(
  rowcode, description, amount, total
) 
SELECT 
  'total', 
  CONCAT(
    \"     \", \"PENDAPATAN KOTOR\"
  ) as description, 
  0, 
  total_pendapatan;
/*biaya*/
insert into temp_table(
  rowcode, description, amount, total
) 
SELECT 
  'detail', 
  CONCAT(
    level_thirds_code, \" - \", level_thirds_name
  ) as description, 
  sum(debet_amount) as amount, 
  0 
FROM 
  v_general_journal_details 
WHERE 
  cast(transaction_date as date) between start_period 
  and end_period 
  and project_plan_id =  CASE project_id WHEN 1 THEN project_plan_id ELSE project_id END  
  and level_first_code = 5 
  and debet_amount > 0 
GROUP BY 
  level_thirds_code, 
  level_thirds_name 
ORDER BY 
  level_thirds_code;
SELECT 
  sum(debet_amount) into total_biaya 
FROM 
  v_general_journal_details 
WHERE 
  cast(transaction_date as date) between start_period 
  and end_period 
  and project_plan_id =  CASE project_id WHEN 1 THEN project_plan_id ELSE project_id END  
  and level_first_code = 5 
  and debet_amount > 0;
SET 
  total_biaya = IFNULL(total_biaya, 0);
insert into temp_table(
  rowcode, description, amount, total
) 
SELECT 
  'total', 
  CONCAT(\"     \", \"TOTAL BIAYA\") as description, 
  0, 
  total_biaya as total;
/*laba rugi*/
insert into temp_table(
  rowcode, description, amount, total
) 
SELECT 
  'total', 
  CONCAT(\"     \", \"LABA / RUGI\") as description, 
  0, 
  total_pendapatan - total_biaya as total;
select 
  inc, 
  rowcode, 
  description, 
  FORMAT(amount, 2) as amount, 
  FORMAT(total, 2) as total 
from 
  temp_table 
order by 
  inc;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS SP_ProfitLoss");
    }
};
