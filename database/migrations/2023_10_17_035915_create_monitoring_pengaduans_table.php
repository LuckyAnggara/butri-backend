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
        Schema::create('monitoring_pengaduans', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->string('bulan');
            $table->integer('satker_id');
            $table->double('wbs')->default(0);
            $table->double('kotak_pengaduan')->default(0);
            $table->double('aplikasi_lapor')->default(0);
            $table->double('media_sosial')->default(0);
            $table->double('surat_pos')->default(0);
            $table->double('website')->default(0);
            $table->double('sms_gateway')->default(0);
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_pengaduans');
    }
};
