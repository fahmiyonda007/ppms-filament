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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CashFlow`(IN start_period date, 
  IN end_period date)
BEGIN

	DECLARE val DATE;
	DECLARE INC INT;
	SET INC = 0;
	
	IF DATEDIFF(end_period, start_period) > 5 or  DATEDIFF(end_period, start_period)  < 0 THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Tidak sesuai';
	END IF;

	SET val= start_period;
	
	-- kas masuk
	SET @qry1:= concat('select \"DATA KAS\" as name, \"KAS MASUK\" as coa_name, ');
	REPEAT
		IF INC = 0 THEN
			SET @qry1:= concat(@qry1, 'FORMAT(SUM( IF( CAST(a.transaction_date AS DATE) = \"', val ,'\", debet_amount, 0) ),0) AS \"', val ,'\"');
		ELSE
			SET @qry1:= concat(@qry1, ',FORMAT(SUM( IF( CAST(a.transaction_date AS DATE) = \"', val ,'\", debet_amount, 0) ),0) AS \"', val ,'\"');
		END IF;
		
		SET val = DATE_ADD(val, INTERVAL 1 DAY);
		SET INC = INC + 1;
		
	UNTIL val > end_period
	END REPEAT;
	
	SET @qry1:= concat(@qry1, 'from v_general_journal_details a join cash_flows b on a.reference_code = b.transaction_code ',
	'where b.cash_flow_type = \"SETOR_MODAL\" and debet_amount > 0 ',
	'and CAST(a.transaction_date AS DATE) BETWEEN \"', start_period ,'\" and \"', end_period ,'\" ');

	-- SELECT @qry1;
	SET @qry1:= concat(@qry1, ' UNION ALL ');
	
	
	
	-- kas keluar	
	SET INC = 0;
	SET val= start_period;
	SET @qry1:= concat(@qry1,  'SELECT  name,  level_thirds_name as coa_name, ');
	REPEAT
		IF INC = 0 THEN
			SET @qry1:= concat(@qry1, 'FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", debet_amount, 0) ),0) AS \"', val ,'\"');
		ELSE
			SET @qry1:= concat(@qry1, ',FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", debet_amount, 0) ),0) AS \"', val ,'\"');
		END IF;
		
		SET val = DATE_ADD(val, INTERVAL 1 DAY);
		SET INC = INC + 1;
		
	UNTIL val > end_period
	END REPEAT;
	
	SET @qry1:= concat(@qry1, 'FROM v_general_journal_details ',
	'where level_first_code = 5 ',
	'GROUP BY project_plan_id, name, level_thirds_name ');
	
	
	SET @qry1:= concat(@qry1, ' UNION ALL ');
	
	
	-- total	
	SET INC = 0;
	SET val= start_period;
	SET @qry1:= concat(@qry1,  'SELECT  \"TOTAL PENGELUARAN\" as name,  \"TOTAL\" as coa_name, ');
	REPEAT
		IF INC = 0 THEN
			SET @qry1:= concat(@qry1, 'FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", debet_amount, 0) ),0) AS \"', val ,'\"');
		ELSE
			SET @qry1:= concat(@qry1, ',FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", debet_amount, 0) ),0) AS \"', val ,'\"');
		END IF;
		
		SET val = DATE_ADD(val, INTERVAL 1 DAY);
		SET INC = INC + 1;
		
	UNTIL val > end_period
	END REPEAT;
	
	SET @qry1:= concat(@qry1, 'FROM v_general_journal_details ',
	'where level_first_code = 5 ');

	-- SELECT @qry1;
	prepare stmt from @qry1 ;
	execute stmt ;
	
	-- select * from temp_table;
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
