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
        // Create or replace the view with the correct WHERE clause (status = '1')
        DB::statement("CREATE OR REPLACE VIEW `tot_pinjam` AS SELECT IFNULL(SUM(`pinjamans`.`total`),0) AS `total` FROM `pinjamans` WHERE `pinjamans`.`status` = '1'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `tot_pinjam`");
    }
};
