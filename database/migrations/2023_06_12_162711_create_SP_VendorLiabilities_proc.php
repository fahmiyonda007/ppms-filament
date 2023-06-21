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
        DB::unprepared("CREATE DEFINER=CURRENT_USER PROCEDURE `SP_VendorLiabilities`(IN `status` int, IN `start_period` date,
  IN `end_period` date)
BEGIN
 SET `status` = case when `status` = 2 then 0 else `status` end;
	select
	c.`name` as vendor_name,
	DATE_FORMAT(a.transaction_date,'%d %b %Y') as transaction_date,
	a.scope_of_work as SOW,
	a.description,
	DATE_FORMAT(a.start_date,'%d %b %Y') as start_date,
	DATE_FORMAT(a.est_end_date,'%d %b %Y') as est_end_date,
	DATE_FORMAT(a.end_date,'%d %b %Y') as end_date,
	FORMAT(a.est_price,0) as est_price,
	FORMAT(a.deal_price,0) as deal_price,
	FORMAT(sum(b.amount),0) as total_payment,
	FORMAT(a.outstanding,0) as outstanding,
	case when a.project_status = 1 then \"DONE\" else \"NOT DONE\" end as project_status
from vendor_liabilities a
left join vendor_liability_payments b
	on a.id = b.vendor_liabilities_id
join vendors c
	on a.vendor_id = c.id
where cast( a.transaction_date AS date ) BETWEEN start_period AND end_period
and project_status =  case when `status` = 9 then project_status else `status` end
group by
	c.name,
	a.transaction_date,
	a.scope_of_work,
	a.description,
	a.start_date,
	a.est_end_date,
	a.end_date,
	a.est_price,
	a.deal_price,
	a.outstanding,
	a.project_status
order by a.transaction_date;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS SP_VendorLiabilities");
    }
};
