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
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_at');
            $table->date('end_at');
            $table->text('output');
            $table->text('notes');
            $table->string('tempat');
            $table->enum('jenis_kegiatan', ['RAPAT INTERNAL', 'FORUM GROUP DISCUSION', 'RAPAT EKSTERNAL', 'DINAS LUAR', 'KOORDINASI', 'KONSINYERING', 'FASILITASI KEGIATAN', 'HELPDESK TI']);
            $table->integer('unit_id');
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
        Schema::dropIfExists('kegiatans');
    }
};
