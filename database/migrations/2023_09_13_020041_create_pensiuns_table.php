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
        Schema::create('pensiuns', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sk');
            $table->text('tentang');
            $table->integer('employe_id');
            $table->date('tmt_pensiun');
            $table->text('notes');
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
        Schema::dropIfExists('pensiuns');
    }
};
