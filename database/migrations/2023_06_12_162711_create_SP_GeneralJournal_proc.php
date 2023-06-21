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
        DB::unprepared("CREATE DEFINER=CURRENT_USER PROCEDURE `SP_GeneralJournal`( IN `ref_code` VARCHAR ( 255 ), IN `start_period` date, IN `end_period` date )
BEGIN
	IF
		start_period IS NULL
		OR end_period IS NULL
		OR start_period = \"1999-01-01\"
		OR end_period = \"1999-01-01\" THEN
		SELECT
			*
		FROM
			(
			SELECT
				1 AS inc,
				id,
				reference_code,
				transaction_date,
				jurnal_description,
				gournal_source AS journal_souce,
				level_thirds_code AS account_code,
				level_thirds_name AS account_name,
				format( debet_amount, 0 ) AS debet_amount,
				format( credit_amount, 0 ) AS credit_amount,
				`name` AS project_name
			FROM
				v_general_journal_details
			WHERE
				reference_code COLLATE utf8mb4_unicode_ci =
			CASE

					WHEN IFNULL( ref_code, \"0\" ) = \"0\" THEN
					reference_code ELSE ref_code
			END UNION
			SELECT
				2,
				0,
				\"Total\",
				NULL,
				NULL,
				NULL,
				NULL,
				NULL,
				format( sum( debet_amount ), 0 ) AS debet_amount,
				format( sum( credit_amount ), 0 ) AS credit_amount,
			NULL
			FROM
				v_general_journal_details
			WHERE
				reference_code COLLATE utf8mb4_unicode_ci =
			CASE

					WHEN IFNULL( ref_code, \"0\" ) = \"0\" THEN
					reference_code ELSE ref_code
				END
				) a
			ORDER BY
				inc,
				id;
			ELSE SELECT
				*
			FROM
				(
				SELECT
					1 AS inc,
					id,
					reference_code,
					transaction_date,
					jurnal_description,
					gournal_source AS journal_souce,
					level_thirds_code AS account_code,
					level_thirds_name AS account_name,
					format( debet_amount, 0 ) AS debet_amount,
					format( credit_amount, 0 ) AS credit_amount,
					`name` AS project_name
				FROM
					v_general_journal_details
				WHERE
					reference_code COLLATE utf8mb4_unicode_ci =
				CASE

						WHEN IFNULL( ref_code, \"0\" ) = \"0\" THEN
						reference_code ELSE ref_code
					END
						AND cast( transaction_date AS date ) BETWEEN start_period
						AND end_period UNION
					SELECT
						2,
						0,
						\"Total\",
						NULL,
						NULL,
						NULL,
						NULL,
						NULL,
						format( sum( debet_amount ), 0 ) AS debet_amount,
						format( sum( credit_amount ), 0 ) AS credit_amount,
					NULL
					FROM
						v_general_journal_details
					WHERE
						reference_code COLLATE utf8mb4_unicode_ci =
					CASE

							WHEN IFNULL( ref_code, \"0\" ) = \"0\" THEN
							reference_code ELSE ref_code
						END
							AND cast( transaction_date AS date ) BETWEEN start_period
							AND end_period
						) a
					ORDER BY
						inc,
						id;

				END IF;

END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS SP_GeneralJournal");
    }
};
