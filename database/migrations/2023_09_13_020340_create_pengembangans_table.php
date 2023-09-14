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
        Schema::create('pengembangans', function (Blueprint $table) {
            $table->id();
            $table->string('surat_tugas')->nullable();
            $table->string('kegiatan');
            $table->integer('jumlah_peserta');
            $table->integer('jumlah_hari');
            $table->text('notes');
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->string('tempat');
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
        Schema::dropIfExists('pengembangans');
    }
};
