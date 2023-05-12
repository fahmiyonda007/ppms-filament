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
        DB::unprepared("CREATE DEFINER=`root`@`%` PROCEDURE `SP_ProfitLoss`( IN `project_id` INT, IN `start_period` DATE, IN `end_period` DATE )
BEGIN
	DECLARE
		total_pendapatan,
		total_biaya NUMERIC;
	DROP TABLE
	IF
		EXISTS temp_table;
	CREATE TEMPORARY TABLE temp_table ( inc INT PRIMARY KEY auto_increment, rowcode VARCHAR ( 10 ), description VARCHAR ( 100 ), amount NUMERIC, total NUMERIC );
/*pendapatan*/
	INSERT INTO temp_table ( rowcode, description, amount, total ) SELECT
	'detail',
	CONCAT( level_thirds_code, \" - \", level_thirds_name ) AS description,
	sum( credit_amount ) AS amount,
	0 
	FROM
		v_general_journal_details 
	WHERE
		cast( transaction_date AS DATE ) BETWEEN start_period 
		AND end_period 
		AND project_plan_id = project_id 
		AND level_first_code = 4 
		AND credit_amount > 0 
	GROUP BY
		level_thirds_code,
		level_thirds_name 
	ORDER BY
		level_thirds_code;
	SELECT
		sum( credit_amount ) INTO total_pendapatan 
	FROM
		v_general_journal_details 
	WHERE
		cast( transaction_date AS DATE ) BETWEEN start_period 
		AND end_period 
		AND project_plan_id = project_id 
		AND level_first_code = 4 
		AND credit_amount > 0;
	
	SET total_pendapatan = IFNULL( total_pendapatan, 0 );
	INSERT INTO temp_table ( rowcode, description, amount, total ) SELECT
	'total',
	CONCAT( \"          \", \"PENDAPATAN KOTOR\" ) AS description,
	0,
	total_pendapatan;
/*biaya*/
	INSERT INTO temp_table ( rowcode, description, amount, total ) SELECT
	'detail',
	CONCAT( level_thirds_code, \" - \", level_thirds_name ) AS description,
	sum( debet_amount ) AS amount,
	0 
	FROM
		v_general_journal_details 
	WHERE
		cast( transaction_date AS DATE ) BETWEEN start_period 
		AND end_period 
		AND project_plan_id = project_id 
		AND level_first_code = 5 
		AND debet_amount > 0 
	GROUP BY
		level_thirds_code,
		level_thirds_name 
	ORDER BY
		level_thirds_code;
	SELECT
		sum( debet_amount ) INTO total_biaya 
	FROM
		v_general_journal_details 
	WHERE
		cast( transaction_date AS DATE ) BETWEEN start_period 
		AND end_period 
		AND project_plan_id = project_id 
		AND level_first_code = 5 
		AND debet_amount > 0;
	
	SET total_biaya = IFNULL( total_biaya, 0 );
	INSERT INTO temp_table ( rowcode, description, amount, total ) SELECT
	'total',
	CONCAT( \"          \", \"TOTAL BIAYA\" ) AS description,
	0,
	total_biaya AS total;
/*laba rugi*/
	INSERT INTO temp_table ( rowcode, description, amount, total ) SELECT
	'total',
	CONCAT( \"          \", \"LABA / RUGI\" ) AS description,
	0,
	total_pendapatan - total_biaya AS total;
	SELECT
		inc,
		rowcode,
		description,
		FORMAT( amount, 2 ) AS amount,
		FORMAT( total, 2 ) AS total 
	FROM
		temp_table 
	ORDER BY
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
