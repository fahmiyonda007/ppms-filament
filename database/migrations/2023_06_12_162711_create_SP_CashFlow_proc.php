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
        DB::unprepared("CREATE DEFINER=CURRENT_USER PROCEDURE `SP_CashFlow`(IN start_period date,
  IN end_period date)
BEGIN

	DECLARE val DATE;
	DECLARE INC INT;


	IF DATEDIFF(end_period, start_period) > 6 or  DATEDIFF(end_period, start_period)  < 0 THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Tidak sesuai';
	END IF;

-- -----------------------------------------------------------
-- SISA KAS
-- -----------------------------------------------------------
DROP TABLE
IF
	EXISTS t_sisa_kas;
CREATE TEMPORARY TABLE
IF
	NOT EXISTS t_sisa_kas AS (
	SELECT
		\"DATA KAS\" as name,
		\"SISA KAS\" as coa_name,
		a.transaction_date,
		b.start_balance
	FROM
		(
		SELECT
			cast( transaction_date AS date ) AS transaction_date,
			min( id ) AS id
		FROM
			v_general_journal_details
		WHERE
			cast( transaction_date AS date ) BETWEEN start_period AND end_period
			AND level_first_code = 1
			AND level_thirds_code = \"101001\"
		GROUP BY
		cast( transaction_date AS date )) AS a
		JOIN v_general_journal_details b ON a.id = b.id
	ORDER BY
		1,
		2
	);

	SET INC = 0;
	SET val= start_period;
	SET @qry1:= concat('select name COLLATE utf8mb4_unicode_ci as  name, coa_name COLLATE utf8mb4_unicode_ci as coa_name, ');
	REPEAT
		IF INC = 0 THEN
			SET @qry1:= concat(@qry1, 'FORMAT(SUM(IF( CAST(transaction_date AS DATE) = \"', val ,'\", start_balance, 0 )),0) AS \"', val ,'\"');
		ELSE
			SET @qry1:= concat(@qry1, ',FORMAT(SUM(IF( CAST(transaction_date AS DATE) = \"', val ,'\", start_balance, 0 )),0) AS \"', val ,'\"');
		END IF;

		SET val = DATE_ADD(val, INTERVAL 1 DAY);
		SET INC = INC + 1;

	UNTIL val > end_period
	END REPEAT;

	SET @qry1:= concat(@qry1, ' from t_sisa_kas ');

	-- SELECT @qry1;
	SET @qry1:= concat(@qry1, ' UNION ALL ');

-- -----------------------------------------------------------
-- KAS MASUK
-- -----------------------------------------------------------
DROP TABLE
IF
	EXISTS t_kas_masuk;
CREATE TEMPORARY TABLE
IF
	NOT EXISTS t_kas_masuk AS (
	SELECT
		\"DATA KAS\" as name,
		\"KAS MASUK\" as coa_name,
		CAST( a.transaction_date AS DATE ) AS transaction_date,
		SUM( debet_amount ) AS amount
	FROM
		v_general_journal_details a
		JOIN cash_flows b ON a.reference_code = b.transaction_code
	WHERE
		b.cash_flow_type = \"SETOR_MODAL\"
		AND debet_amount > 0
		AND level_first_code = 1
		AND level_thirds_code = \"101001\"
		AND CAST( a.transaction_date AS DATE ) BETWEEN start_period AND end_period
	GROUP BY
	CAST( a.transaction_date AS DATE ));

	SET INC = 0;
	SET val= start_period;
	SET @qry1:= concat(@qry1,'select name COLLATE utf8mb4_unicode_ci, coa_name COLLATE utf8mb4_unicode_ci, ');
	REPEAT
		IF INC = 0 THEN
			SET @qry1:= concat(@qry1, 'FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", amount, 0) ),0) AS \"', val ,'\"');
		ELSE
			SET @qry1:= concat(@qry1, ',FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", amount, 0) ),0) AS \"', val ,'\"');
		END IF;

		SET val = DATE_ADD(val, INTERVAL 1 DAY);
		SET INC = INC + 1;

	UNTIL val > end_period
	END REPEAT;

	SET @qry1:= concat(@qry1, 'from t_kas_masuk ');

-- 	 SELECT @qry1;
	SET @qry1:= concat(@qry1, ' UNION ALL ');

-- -----------------------------------------------------------
-- TOTAL KAS
-- -----------------------------------------------------------
DROP TABLE
IF
	EXISTS t_kas_total;
CREATE TEMPORARY TABLE
IF
	NOT EXISTS t_kas_total AS (
SELECT
	\"DATA KAS\" as name,
	\"TOTAL INFLOWS\" as coa_name,
	case when a_date is null then b_date else a_date end as transaction_date,
	IFNULL(a_amount,0) + IFNULL(b_amount,0) as amount
FROM
	(
	SELECT
		t_sisa_kas.`name` AS a_name,
		t_sisa_kas.coa_name AS a_coa_name,
		t_sisa_kas.transaction_date AS a_date,
		t_sisa_kas.start_balance AS a_amount,
		t_kas_masuk.`name` AS b_name,
		t_kas_masuk.coa_name AS b_coa_name,
		t_kas_masuk.transaction_date AS b_date,
		t_kas_masuk.amount AS b_amount
	FROM
		t_sisa_kas
		LEFT JOIN t_kas_masuk ON t_sisa_kas.transaction_date = t_kas_masuk.transaction_date
		UNION
	SELECT
		t_sisa_kas.`name` AS a_name,
		t_sisa_kas.coa_name AS a_coa_name,
		t_sisa_kas.transaction_date AS a_date,
		t_sisa_kas.start_balance AS a_amount,
		t_kas_masuk.`name` AS b_name,
		t_kas_masuk.coa_name AS b_coa_name,
		t_kas_masuk.transaction_date AS b_date,
		t_kas_masuk.amount AS b_amount
	FROM
		t_sisa_kas
	RIGHT JOIN t_kas_masuk ON t_sisa_kas.transaction_date = t_kas_masuk.transaction_date
	) a);

	SET INC = 0;
	SET val= start_period;
	SET @qry1:= concat(@qry1,'select name COLLATE utf8mb4_unicode_ci, coa_name COLLATE utf8mb4_unicode_ci, ');
	REPEAT
		IF INC = 0 THEN
			SET @qry1:= concat(@qry1, 'FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", amount, 0) ),0) AS \"', val ,'\"');
		ELSE
			SET @qry1:= concat(@qry1, ',FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", amount, 0) ),0) AS \"', val ,'\"');
		END IF;

		SET val = DATE_ADD(val, INTERVAL 1 DAY);
		SET INC = INC + 1;

	UNTIL val > end_period
	END REPEAT;

	SET @qry1:= concat(@qry1, 'from t_kas_total ');

-- 	 SELECT @qry1;
	SET @qry1:= concat(@qry1, ' UNION ALL ');


-- -----------------------------------------------------------
-- KAS KELUAR
-- -----------------------------------------------------------
DROP TABLE
IF
	EXISTS t_kas_keluar;
CREATE TEMPORARY TABLE
IF
	NOT EXISTS t_kas_keluar AS (
	SELECT
		`name`,
		level_thirds_name AS coa_name,
		CAST( transaction_date AS DATE ) AS transaction_date,
		SUM( debet_amount ) AS amount
	FROM
		v_general_journal_details
	WHERE
		level_first_code = 5
		AND CAST( transaction_date AS DATE ) BETWEEN start_period AND end_period
	GROUP BY
		NAME,
		level_thirds_name,
		CAST( transaction_date AS DATE ))
	ORDER BY NAME, level_thirds_name;

	SET INC = 0;
	SET val= start_period;
	SET @qry1:= concat(@qry1,  'SELECT  name COLLATE utf8mb4_unicode_ci, coa_name COLLATE utf8mb4_unicode_ci, ');
	REPEAT
		IF INC = 0 THEN
			SET @qry1:= concat(@qry1, 'FORMAT(SUM(IF( CAST(transaction_date AS DATE) = \"', val ,'\", amount, 0)),0) AS \"', val ,'\"');
		ELSE
			SET @qry1:= concat(@qry1, ',FORMAT(SUM(IF( CAST(transaction_date AS DATE) = \"', val ,'\", amount, 0)),0) AS \"', val ,'\"');
		END IF;

		SET val = DATE_ADD(val, INTERVAL 1 DAY);
		SET INC = INC + 1;

	UNTIL val > end_period
	END REPEAT;

	SET @qry1:= concat(@qry1, ' FROM t_kas_keluar group by name, coa_name');

	-- select @qry1;
	 SET @qry1:= concat(@qry1, ' UNION ALL ');


-- -----------------------------------------------------------
-- TOTAL KAS KELUAR
-- -----------------------------------------------------------
	SET INC = 0;
	SET val= start_period;
	SET @qry1:= concat(@qry1,  'SELECT  \"TOTAL PENGELUARAN\" as name,  \"TOTAL OUTFLOWS\" as coa_name, ');
	REPEAT
		IF INC = 0 THEN
			SET @qry1:= concat(@qry1, 'FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", amount, 0) ),0) AS \"', val ,'\"');
		ELSE
			SET @qry1:= concat(@qry1, ',FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", amount, 0) ),0) AS \"', val ,'\"');
		END IF;

		SET val = DATE_ADD(val, INTERVAL 1 DAY);
		SET INC = INC + 1;

	UNTIL val > end_period
	END REPEAT;

	SET @qry1:= concat(@qry1, 'FROM t_kas_keluar');

	-- select @qry1;
	 SET @qry1:= concat(@qry1, ' UNION ALL ');

-- -----------------------------------------------------------
-- SISA KAS
-- -----------------------------------------------------------

DROP TABLE
IF
	EXISTS t_sisa_kas_akhir;
CREATE TEMPORARY TABLE
IF
	NOT EXISTS t_sisa_kas_akhir AS(
	select * from t_kas_total);

	UPDATE t_sisa_kas_akhir a
	LEFT JOIN ( SELECT transaction_date, sum( amount ) AS amount FROM t_kas_keluar GROUP BY transaction_date ) AS b ON a.transaction_date = b.transaction_date
	SET
	a.amount = a.amount - IFNULL( b.amount, 0 );


	SET INC = 0;
	SET val= start_period;
	SET @qry1:= concat(@qry1,'select \"TOTAL PENGELUARAN\", \"SISA KAS\", ');
	REPEAT
		IF INC = 0 THEN
			SET @qry1:= concat(@qry1, 'FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", amount, 0) ),0) AS \"', val ,'\"');
		ELSE
			SET @qry1:= concat(@qry1, ',FORMAT(SUM( IF( CAST(transaction_date AS DATE) = \"', val ,'\", amount, 0) ),0) AS \"', val ,'\"');
		END IF;

		SET val = DATE_ADD(val, INTERVAL 1 DAY);
		SET INC = INC + 1;

	UNTIL val > end_period
	END REPEAT;

	SET @qry1:= concat(@qry1, 'from t_sisa_kas_akhir ');

-- -----------------------------------------------------------
-- RESULT
-- -----------------------------------------------------------
  -- SELECT @qry1;
	prepare stmt from @qry1 ;
	execute stmt ;

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
