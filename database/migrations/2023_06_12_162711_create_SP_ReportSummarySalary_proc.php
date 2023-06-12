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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ReportSummarySalary`( IN start_period date, IN end_period date )
BEGIN
	DROP TABLE
	IF
		EXISTS temp_table;
	CREATE TEMPORARY TABLE temp_table ( inc INT PRIMARY KEY auto_increment, rowcode VARCHAR ( 20 ), `name` VARCHAR ( 100 ), total_person int, amount_salary NUMERIC, amount_loan NUMERIC, amount_liability_payment NUMERIC );
	INSERT INTO temp_table ( `name` ) SELECT
	`name` 
	FROM
		project_plans 
	WHERE
		progress < 100;
		
	UPDATE temp_table z
	JOIN (
		SELECT
			c.`name`,
			SUM( total_net_salary ) AS amount 
		FROM
			employee_payroll_details a
			JOIN employee_payrolls b ON a.payroll_id = b.id
			JOIN project_plans c ON b.project_plan_id = c.id 
		WHERE
			start_date >= start_period 
			AND end_date <= end_period 
			AND b.is_jurnal = 1 
			AND salary_type = \"DAILY\" 
		GROUP BY
			c.`name` 
		) AS x ON z.`name` = x.`name` 
		SET z.amount_salary = x.amount;
		
	UPDATE temp_table z
	JOIN (
		SELECT
			c.`name`,
			COUNT(1) AS total_person 
		FROM
			employee_payroll_details a
			JOIN employee_payrolls b ON a.payroll_id = b.id
			JOIN project_plans c ON b.project_plan_id = c.id 
		WHERE
			start_date >= start_period 
			AND end_date <= end_period 
			AND b.is_jurnal = 1 
			AND salary_type = \"DAILY\" 
		GROUP BY
			c.`name` 
		) AS x ON z.`name` = x.`name` 
		SET z.total_person = x.total_person;
	
	UPDATE temp_table z
	JOIN (
		SELECT
			b.`name`,
			sum( amount ) AS amount 
		FROM
			employee_loans a
			JOIN project_plans b ON a.project_plan_id = b.id 
		WHERE
			cast( transaction_date AS date ) BETWEEN start_period 
			AND end_period 
			AND a.is_jurnal = 1 
		GROUP BY
			b.`name` 
		) AS x ON z.`name` = x.`name` 
		SET z.amount_loan = x.amount;
	UPDATE temp_table z
	JOIN (
		SELECT
			c.`name`,
			sum( amount ) AS amount 
		FROM
			vendor_liability_payments a
			JOIN vendor_liabilities b ON a.vendor_liabilities_id = b.id
			JOIN project_plans c ON b.project_plan_id = c.id 
		WHERE
			cast( a.transaction_date AS date ) BETWEEN start_period 
			AND end_period 
			AND a.is_jurnal = 1 
		GROUP BY
			c.`name` 
		) AS x ON z.`name` = x.`name` 
		SET z.amount_liability_payment = x.amount;
	DELETE 
	FROM
		temp_table 
	WHERE
		amount_salary IS NULL 
		AND amount_loan IS NULL 
		AND amount_liability_payment IS NULL;
		
		
	SELECT
		`name`,
		total_person as total_tukang,
		FORMAT(amount_salary,0) AS total_gajian,
		FORMAT(amount_loan,0) AS total_kasbon_tukang,
		FORMAT(amount_liability_payment,0) AS total_kasbon_vendor,
		FORMAT(IFNULL(amount_salary,0) + IFNULL(amount_loan,0) + IFNULL(amount_liability_payment,0),0) AS total 
	FROM
		temp_table UNION
	SELECT
		\"TOTAL\" AS `name`,
		SUM(total_person) as total_tukang,
		FORMAT(sum( amount_salary ),0) AS total_gajian,
		FORMAT(sum( amount_loan ),0) AS total_kasbon_tukang,
		FORMAT(sum( amount_liability_payment ),0) AS total_kasbon_vendor,
		FORMAT(sum( IFNULL(amount_salary,0) + IFNULL(amount_loan,0) + IFNULL(amount_liability_payment,0) ),0) AS total 
	FROM
		temp_table;

END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS SP_ReportSummarySalary");
    }
};
