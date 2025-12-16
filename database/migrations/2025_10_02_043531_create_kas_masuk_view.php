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
        DB::statement("CREATE VIEW `kas_masuk` AS select (select ifnull(sum(`a`.`total`),0) from `miniksp`.`general_ledgers` `a` where `a`.`jenis_transaksi` = 'pengembalian' and `a`.`status_pembukuan` = '1') + (select ifnull(sum(`a`.`total`),0) from `miniksp`.`general_ledgers` `a` where `a`.`jenis_transaksi` = 'shu' and `a`.`status_pembukuan` = '1') + (select ifnull(sum(`a`.`total`),0) from `miniksp`.`general_ledgers` `a` where `a`.`jenis_transaksi` = 'wajib' and `a`.`status_pembukuan` = '1') + (select ifnull(sum(`a`.`total`),0) from `miniksp`.`general_ledgers` `a` where `a`.`jenis_transaksi` = 'sukarela' and `a`.`status_pembukuan` = '1') + (select ifnull(sum(`a`.`total`),0) from `miniksp`.`general_ledgers` `a` where `a`.`jenis_transaksi` = 'denda' and `a`.`status_pembukuan` = '1') AS `total`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `kas_masuk`");
    }
};
