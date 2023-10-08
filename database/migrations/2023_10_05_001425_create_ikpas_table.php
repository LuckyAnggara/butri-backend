<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ikpas', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->string('bulan');
            $table->double('revisi_dipa')->default(0);
            $table->double('halaman_tiga_dipa')->default(0);
            $table->double('penyerapan_anggaran')->default(0);
            $table->double('belanja_kontraktual')->default(0);
            $table->double('penyelesaian_tagihan')->default(0);
            $table->double('pengelolaan_up_tup')->default(0);
            $table->double('dispensasi_spm')->default(0);
            $table->double('capaian_output')->default(0);
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ikpas');
    }
};
