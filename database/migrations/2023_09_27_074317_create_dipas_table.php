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
        Schema::create('dipas', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->string('kode');
            $table->string('name');
            $table->double('pagu');
            $table->string('jenis')->nullable();
            $table->integer('group_id');
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
        Schema::dropIfExists('dipas');
    }
};
