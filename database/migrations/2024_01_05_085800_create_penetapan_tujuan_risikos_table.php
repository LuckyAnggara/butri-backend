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
        Schema::create('penetapan_tujuan_risikos', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->integer('program_kegiatan_id');
            $table->integer('sasaran_kementerian_id');
            $table->integer('iku_id');
            $table->text('permasalahan');
            $table->integer('group_id');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penetapan_tujuan_risikos');
    }
};
