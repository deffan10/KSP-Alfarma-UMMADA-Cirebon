<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perbesar kolom jumlah_cicilan agar tidak error "Out of range".
     * Sebelumnya: pengembalians double(8,2) max ~999999.99, angsurans float(10,0).
     */
    public function up()
    {
        DB::statement('ALTER TABLE pengembalians MODIFY jumlah_cicilan DECIMAL(14,2) NOT NULL');
        DB::statement('ALTER TABLE angsurans MODIFY jumlah_cicilan DECIMAL(14,2) NOT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE pengembalians MODIFY jumlah_cicilan DOUBLE(8,2) NOT NULL');
        DB::statement('ALTER TABLE angsurans MODIFY jumlah_cicilan FLOAT(10,0) NOT NULL');
    }
};
