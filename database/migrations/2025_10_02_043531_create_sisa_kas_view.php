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
        DB::statement("CREATE VIEW `sisa_kas` AS select (select `kas_masuk`.`total` from `miniksp`.`kas_masuk`) - (select `kas_keluar`.`total` from `miniksp`.`kas_keluar`) AS `total`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `sisa_kas`");
    }
};
