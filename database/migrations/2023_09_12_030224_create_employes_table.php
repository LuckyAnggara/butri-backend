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
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('name');
            $table->enum('gender', ['LAKI LAKI', 'PEREMPUAN']);
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('is_wa')->nullable()->default(false);
            $table->integer('pangkat_id');
            $table->date('tmt_pangkat')->nullable();
            $table->integer('jabatan_id');
            $table->date('tmt_jabatan')->nullable();
            $table->date('tmt_pensiun')->nullable();
            $table->integer('eselon_id')->nullable();
            $table->integer('unit_id');
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
