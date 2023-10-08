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
        Schema::create('kinerja_keuangans', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->double('capaian_sasaran_program')->default(0);
            $table->double('penyerapan')->default(0);
            $table->double('konsistensi')->default(0);
            $table->double('capaian_output_program')->default(0);
            $table->double('efisiensi')->default(0);
            $table->double('nilai_efisiensi')->default(0);
            $table->double('rata_nka_satker')->default(0);
            $table->double('nilai_kinerja')->default(0);
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kinerja_keuangans');
    }
};
