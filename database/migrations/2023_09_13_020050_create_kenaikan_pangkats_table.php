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
        Schema::create('kenaikan_pangkats', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sk');
            $table->text('notes');
            $table->integer('employe_id');
            $table->integer('pangkat_id');
            $table->integer('pangkat_new_id');
            $table->integer('created_by');
            $table->date('tmt_pangkat');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kenaikan_pangkats');
    }
};
