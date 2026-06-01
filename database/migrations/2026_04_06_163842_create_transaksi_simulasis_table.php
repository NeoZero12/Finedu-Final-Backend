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
        Schema::create('transaksi_simulasis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('simulasi_id')->constrained('simulasis')->onDelete('cascade');
        $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
        $table->decimal('nominal', 15, 2);
        $table->boolean('pembelian_impulsif')->default(false); // TRUE jika beli barang 'keinginan' berlebihan
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_simulasis');
    }
};
