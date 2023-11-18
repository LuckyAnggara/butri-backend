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
        Schema::create('capaian_indikator_kegiatan_utamas', function (Blueprint $table) {
            $table->id();
            $table->integer('iku_id');
            $table->string('tahun');
            $table->string('bulan');
            $table->string('realisasi');
            $table->string('capaian')->nullable();
            $table->text('analisa')->nullable();
            $table->text('kegiatan')->nullable();
            $table->text('kendala')->nullable();
            $table->text('hambatan')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capaian_indikator_kegiatan_utamas');
    }
};
