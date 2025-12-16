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
        DB::statement("CREATE VIEW `chart` AS select month(`miniksp`.`transaksis`.`created_at`) AS `Month`,sum(`miniksp`.`transaksis`.`total`) AS `total` from `miniksp`.`transaksis` where `miniksp`.`transaksis`.`jenis_transaksi` = 'wajib' or `miniksp`.`transaksis`.`jenis_transaksi` = 'sukarela' group by month(`miniksp`.`transaksis`.`created_at`) limit 0,1212");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `chart`");
    }
};
