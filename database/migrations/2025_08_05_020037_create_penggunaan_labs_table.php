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
        Schema::create('penggunaan_labs', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->integer('no_lab');
            $table->string('fungsi_lab');
            $table->string('kondisi_akhir');
            $table->string('catatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggunaan_labs');
    }
};
