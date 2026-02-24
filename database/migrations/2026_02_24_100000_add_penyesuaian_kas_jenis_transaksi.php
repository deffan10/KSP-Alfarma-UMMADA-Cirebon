<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jenis transaksi: Penyesuaian Kas / Operasional diluar KSP.
     * Mengurangi kas (masuk ke kas_keluar).
     */
    public function up()
    {
        Schema::table('general_ledgers', function (Blueprint $table) {
            $table->string('keterangan', 255)->nullable()->after('jenis_transaksi');
        });

        DB::statement("ALTER TABLE general_ledgers MODIFY COLUMN jenis_transaksi ENUM('debet', 'wajib', 'sukarela', 'operasional', 'pinjaman', 'pengembalian', 'shu', 'denda', 'penyesuaian') NOT NULL");

        DB::statement("DROP VIEW IF EXISTS `kas_keluar`");
        DB::statement("
            CREATE VIEW `kas_keluar` AS
            SELECT (
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'debet' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'operasional' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'pinjaman' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'penyesuaian' AND status_pembukuan = '1'), 0)
            ) AS total
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS `kas_keluar`");
        DB::statement("
            CREATE VIEW `kas_keluar` AS
            SELECT (
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'debet' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'operasional' AND status_pembukuan = '1'), 0) +
                IFNULL((SELECT SUM(total) FROM general_ledgers WHERE jenis_transaksi = 'pinjaman' AND status_pembukuan = '1'), 0)
            ) AS total
        ");
        Schema::table('general_ledgers', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
        DB::statement("ALTER TABLE general_ledgers MODIFY COLUMN jenis_transaksi ENUM('debet', 'wajib', 'sukarela', 'operasional', 'pinjaman', 'pengembalian', 'shu', 'denda') NOT NULL");
    }
};
