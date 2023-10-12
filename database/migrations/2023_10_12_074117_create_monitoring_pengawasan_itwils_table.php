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
        Schema::create('monitoring_pengawasan_itwils', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->string('bulan');
            $table->integer('group_id');
            $table->double('temuan_jumlah')->default(0);
            $table->double('temuan_nominal')->default(0);
            $table->double('tl_jumlah')->default(0);
            $table->double('tl_nominal')->default(0);
            $table->double('btl_jumlah')->default(0);
            $table->double('btl_nominal')->default(0);
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
        Schema::dropIfExists('monitoring_pengawasan_itwils');
    }
};
