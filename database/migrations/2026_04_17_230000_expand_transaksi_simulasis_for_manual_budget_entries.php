<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_simulasis', function (Blueprint $table) {
            $table->dropForeign(['produk_id']);
        });

        DB::statement('ALTER TABLE transaksi_simulasis MODIFY produk_id BIGINT UNSIGNED NULL');

        Schema::table('transaksi_simulasis', function (Blueprint $table) {
            $table->string('nama_item')->nullable()->after('produk_id');
            $table->string('kategori_label')->nullable()->after('nama_item');
            $table->text('catatan')->nullable()->after('kategori_label');
            $table->string('arah_transaksi', 20)->default('pengeluaran')->after('catatan');
            $table->foreign('produk_id')->references('id')->on('produks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_simulasis', function (Blueprint $table) {
            $table->dropForeign(['produk_id']);
        });

        Schema::table('transaksi_simulasis', function (Blueprint $table) {
            $table->dropColumn([
                'nama_item',
                'kategori_label',
                'catatan',
                'arah_transaksi',
            ]);
        });

        DB::table('transaksi_simulasis')->whereNull('produk_id')->delete();
        DB::statement('ALTER TABLE transaksi_simulasis MODIFY produk_id BIGINT UNSIGNED NOT NULL');

        Schema::table('transaksi_simulasis', function (Blueprint $table) {
            $table->foreign('produk_id')->references('id')->on('produks')->cascadeOnDelete();
        });
    }
};
