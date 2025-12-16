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
        DB::statement("CREATE VIEW `laba` AS select (select ifnull(sum(`a`.`jumlah_cicilan`),0) from `miniksp`.`pengembalians` `a` where `a`.`status_pinjam` = '0' and `a`.`aktif` = '1') - (select ifnull(sum(`b`.`total`),0) from `miniksp`.`pinjamans` `b` where `b`.`status` = '0' and `b`.`aktif` = '1') AS `laba`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `laba`");
    }
};
