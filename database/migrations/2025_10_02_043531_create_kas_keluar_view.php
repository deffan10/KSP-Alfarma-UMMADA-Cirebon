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
        DB::statement("CREATE VIEW `kas_keluar` AS select (select ifnull(sum(`a`.`total`),0) from `miniksp`.`general_ledgers` `a` where `a`.`jenis_transaksi` = 'debet' and `a`.`status_pembukuan` = '1') + (select ifnull(sum(`a`.`total`),0) from `miniksp`.`general_ledgers` `a` where `a`.`jenis_transaksi` = 'operasional' and `a`.`status_pembukuan` = '1') + (select ifnull(sum(`a`.`total`),0) from `miniksp`.`general_ledgers` `a` where `a`.`jenis_transaksi` = 'pinjaman' and `a`.`status_pembukuan` = '1') AS `total`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `kas_keluar`");
    }
};
