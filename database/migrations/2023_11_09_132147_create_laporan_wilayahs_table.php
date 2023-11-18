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
        Schema::create('laporan_wilayahs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('link');
            $table->string('ttd_name');
            $table->string('ttd_nip');
            $table->string('ttd_location');
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
        Schema::dropIfExists('laporans');
    }
};
