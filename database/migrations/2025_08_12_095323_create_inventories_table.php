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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('item_name')->constrained('borrows')->onDelete('set null');
            $table->string('category')->nullable();
            $table->enum('condition', ['good', 'damaged', 'repaired'])->default('good');
            $table->integer('quantity')->default(0);
            $table->enum('status', ['available', 'in_use', 'maintenance', 'broken'])->default('available');
            $table->text('desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
