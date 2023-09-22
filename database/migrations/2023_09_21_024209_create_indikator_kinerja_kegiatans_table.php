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
        Schema::create('indikator_kinerja_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor');
            $table->string('tahun');
            $table->integer('sk_id')->nullable();
            $table->string('name');
            $table->integer('group_id');
            $table->string('target');
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_kinerja_kegiatans');
    }
};
