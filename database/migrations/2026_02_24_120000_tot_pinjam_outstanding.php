<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Dana bergulir = total SISA pinjaman yang belum dibayar (angsuran status=1),
     * bukan jumlah awal pinjaman. Hanya pinjaman aktif (status=1).
     */
    public function up()
    {
        DB::statement("DROP VIEW IF EXISTS `tot_pinjam`");
        DB::statement("
            CREATE VIEW `tot_pinjam` AS
            SELECT IFNULL(SUM(a.jumlah_cicilan), 0) AS total
            FROM angsurans a
            INNER JOIN pinjamans p ON p.id = a.pinjaman_id AND p.status = '1'
            WHERE a.status = '1'
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `tot_pinjam`");
        DB::statement("CREATE VIEW `tot_pinjam` AS SELECT IFNULL(SUM(total), 0) AS total FROM pinjamans WHERE status = '1'");
    }
};
