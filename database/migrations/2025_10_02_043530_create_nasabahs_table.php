<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nasabahs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_lengkap', 100);
            $table->string('no_rekening', 8)->unique('no_rekening');
            $table->text('alamat');
            $table->string('telp', 20)->nullable();
            $table->text('foto')->nullable();
            $table->text('no_ktp')->nullable();
            $table->integer('saldo_akhir')->default(0);
            $table->enum('status_pinjaman', ['0', '1'])->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nasabahs');
    }
};
