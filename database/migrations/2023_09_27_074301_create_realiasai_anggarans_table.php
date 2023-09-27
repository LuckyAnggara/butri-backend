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
        Schema::create('realiasai_anggarans', function (Blueprint $table) {
            $table->id();
            $table->string('bulan');
            $table->integer('dipa_id');
            $table->double('realisasi');
            $table->double('dp');
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
        Schema::dropIfExists('realiasai_anggarans');
    }
};
