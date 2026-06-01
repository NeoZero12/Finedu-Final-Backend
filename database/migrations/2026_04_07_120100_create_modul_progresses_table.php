<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modul_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('modul_pembelajaran_id')->constrained('modul_pembelajarans')->cascadeOnDelete();
            $table->boolean('selesai')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'modul_pembelajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modul_progresses');
    }
};
