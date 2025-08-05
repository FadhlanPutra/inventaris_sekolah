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
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('nama_barang');
            $table->string('category')->nullable();
            $table->enum('kondisi', ['baik', 'rusak', 'perbaikan'])->default('baik');
            $table->integer('jumlah')->default(0);
            $table->enum('status', ['tersedia', 'digunakan', 'maintenance', 'hancur'])->default('tersedia');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
