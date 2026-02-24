<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * View chart: total simpanan (wajib + sukarela) per bulan, 12 bulan, tanpa hardcode nama DB.
     */
    public function up()
    {
        DB::statement("DROP VIEW IF EXISTS `chart`");
        DB::statement("
            CREATE VIEW `chart` AS
            SELECT
                MONTH(transaksis.created_at) AS `Month`,
                COALESCE(SUM(transaksis.total), 0) AS `total`
            FROM transaksis
            WHERE transaksis.jenis_transaksi IN ('wajib', 'sukarela')
            GROUP BY MONTH(transaksis.created_at)
            ORDER BY `Month`
            LIMIT 12
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `chart`");
    }
};
