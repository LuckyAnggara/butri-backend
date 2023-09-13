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
        Schema::create('mutasi_pegawais', function (Blueprint $table) {
            $table->id();
            $table->integer('employe_id');
            $table->integer('jabatan_id');
            $table->integer('jabatan_new_id');
            $table->integer('unit_id');
            $table->integer('unit_new_id');
            $table->integer('created_by');
            $table->date('tmt_jabatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_pegawais');
    }
};
