<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuesioners', function (Blueprint $table) {
            $table->foreignId('modul_pembelajaran_id')->nullable()->after('user_id')->constrained('modul_pembelajarans')->nullOnDelete();
            $table->unsignedInteger('nomor_soal')->nullable()->after('modul_pembelajaran_id');
            $table->json('opsi_jawaban')->nullable()->after('pertanyaan');
            $table->unsignedInteger('jawaban_benar')->nullable()->after('opsi_jawaban');
            $table->boolean('benar')->default(false)->after('jawaban_skala');
        });
    }

    public function down(): void
    {
        Schema::table('kuesioners', function (Blueprint $table) {
            $table->dropConstrainedForeignId('modul_pembelajaran_id');
            $table->dropColumn(['nomor_soal', 'opsi_jawaban', 'jawaban_benar', 'benar']);
        });
    }
};
