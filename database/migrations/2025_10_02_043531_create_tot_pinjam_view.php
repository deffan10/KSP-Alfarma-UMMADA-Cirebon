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
        DB::statement("CREATE VIEW `tot_pinjam` AS select ifnull(sum(`miniksp`.`pinjamans`.`total`),0) AS `total` from `miniksp`.`pinjamans` where `miniksp`.`pinjamans`.`status` = '1\'1'");
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
