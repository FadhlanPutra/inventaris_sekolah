<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('item_id')->nullable()->constrained('inventories')->onDelete('set null');
            $table->integer('quantity');
            $table->foreignId('labusage_id')->nullable()->constrained('lab_usages')->onDelete('set null');
            $table->dateTime('borrow_time');
            $table->dateTime('return_time')->nullable();
            $table->enum('status', ['Pending', 'Active', 'Finished'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
