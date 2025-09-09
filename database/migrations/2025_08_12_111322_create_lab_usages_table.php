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
        Schema::create('lab_usages', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->tinyInteger('num_lab');
            $table->enum('class_name', ['X RPL', 'X DKV', 'X TKJ', 'XI RPL', 'XI DKV', 'XI TKJ', 'XII RPL', 'XII DKV', 'XII TKJ'])->nullable();
            $table->tinyInteger('num_students')->nullable();
            $table->enum('status', ['incomplete', 'complete'])->nullable()->default('incomplete');
            $table->string('lab_function')->nullable();
            $table->string('end_state')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_usages');
    }
};
