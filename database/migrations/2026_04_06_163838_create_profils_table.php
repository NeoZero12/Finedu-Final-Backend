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
        Schema::create('profils', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('avatar')->nullable();
        $table->string('banner')->nullable();
        $table->enum('tingkat_literasi', ['rendah', 'menengah', 'tinggi'])->nullable();
        $table->boolean('nudge_aktif')->default(true); // Kelompok eksperimen Nudges
        $table->enum('tipe_budget', ['ketat', 'longgar'])->default('ketat'); // Kelompok Default Budget
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profils');
    }
};
