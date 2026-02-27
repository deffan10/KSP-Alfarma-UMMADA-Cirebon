<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Penyesuaian kas yang menambah kas (mis. sponsor, donasi).
     * Masuk ke view kas_masuk.
     */
    public function up()
    {
        DB::statement("ALTER TABLE general_ledgers MODIFY COLUMN jenis_transaksi ENUM('debet', 'wajib', 'sukarela', 'operasional', 'pinjaman', 'pengembalian', 'shu', 'denda', 'penyesuaian', 'penyesuaian_masuk') NOT NULL");

        DB::statement("DROP VIEW IF EXISTS `kas_masuk`");
        DB::statement("
            CREATE VIEW `kas_masuk` AS
            SELECT (
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'pengembalian' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'shu' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'wajib' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'sukarela' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'denda' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'penyesuaian_masuk' AND status_pembukuan = '1'), 0)
            ) AS total
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `kas_masuk`");
        DB::statement("
            CREATE VIEW `kas_masuk` AS
            SELECT (
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'pengembalian' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'shu' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'wajib' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'sukarela' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'denda' AND status_pembukuan = '1'), 0)
            ) AS total
        ");
        DB::statement("ALTER TABLE general_ledgers MODIFY COLUMN jenis_transaksi ENUM('debet', 'wajib', 'sukarela', 'operasional', 'pinjaman', 'pengembalian', 'shu', 'denda', 'penyesuaian') NOT NULL");
    }
};
