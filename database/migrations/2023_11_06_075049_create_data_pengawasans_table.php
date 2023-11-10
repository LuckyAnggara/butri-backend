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
        Schema::create('data_pengawasans', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('tahun');
            $table->string('bulan');
            $table->string('location');
            $table->string('sp_number')->nullable();
            $table->date('sp_date')->nullable();
            $table->integer('jenis_pengawasan_id')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('output')->nullable();
            $table->integer('unit_id');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pengawasans');
    }
};
