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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_DailyCostReport`(IN `date_period` date)
BEGIN
DECLARE total_pendapatan, 
total_biaya numeric;
DROP TABLE IF EXISTS temp_table;
CREATE TEMPORARY TABLE IF NOT EXISTS temp_table AS (
	SELECT * FROM project_costs where cast(payment_date as date) = date_period and payment_status = 'PAID'
);


DROP TABLE IF EXISTS temp_result;
CREATE TEMPORARY TABLE temp_result(
  inc int primary key auto_increment, 
  rowcode varchar(10),
  item varchar(100),
	order_date date,
	payment_date date,
	vendor varchar(100),
	project varchar(100),
	uom varchar(20),
	qty int,
	unit_price NUMERIC, 
  total_price NUMERIC
);

SELECT 
  sum(b.amount) into total_biaya 
FROM temp_table a
	join project_cost_details b
		on a.id = b.project_cost_id;
	
insert into temp_result(rowcode, item, order_date, payment_date, vendor, project, uom, qty, unit_price, total_price)
select 
	'detail',
	c.`name`,
	a.order_date,
	a.payment_date,
	d.`name` as vendor,
	e.`name` as project,
	b.uom,
	b.qty,
	b.unit_price,
	b.amount 
from temp_table a
	join project_cost_details b
		on a.id = b.project_cost_id
	join coa_level_thirds c
		on b.coa_id = c.id
	join vendors d
		on a.vendor_id = d.id
	join project_plans e
		on a.project_plan_id = e.id;
		
insert into temp_result(rowcode, item, total_price)
select 'total', 'TOTAL PENGELUARAN', total_biaya;
		
		
	
select 
	inc, 
  rowcode,
  item,
	order_date,
	payment_date,
	vendor,
	project,
	uom,
	qty,
	FORMAT(unit_price, 2) as unit_price, 
  FORMAT(total_price, 2) as total_price
from temp_result order by inc;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS SP_DailyCostReport");
    }
};
