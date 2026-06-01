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
        Schema::create('produks', function (Blueprint $table) {
        $table->id();
        $table->string('nama_produk');
        $table->decimal('harga', 15, 2);
        $table->enum('kategori', ['kebutuhan', 'keinginan']); // Untuk mendeteksi impulsive buying
        $table->string('gambar_url')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
