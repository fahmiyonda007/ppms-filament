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
        DB::unprepared(
        "CREATE DEFINER=`root`@`%` PROCEDURE `SetNewPermission`(
            IN _module VARCHAR(250)
        )
        BEGIN

            INSERT INTO permissions (`name`, guard_name, created_at, updated_at)
            VALUES
                (CONCAT(_module, ':view'), 'web', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
                ,(CONCAT(_module, ':create'), 'web', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
                ,(CONCAT(_module, ':update'), 'web', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
                ,(CONCAT(_module, ':delete'), 'web', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
                ,(CONCAT(_module, ':restore'), 'web', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
                ,(CONCAT(_module, ':force_delete'), 'web', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
                ;
        END");

        DB::statement("CALL SetNewPermission('user')");
        DB::statement("CALL SetNewPermission('role')");
        DB::statement("CALL SetNewPermission('permission')");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS SetNewPermission");
    }
};
