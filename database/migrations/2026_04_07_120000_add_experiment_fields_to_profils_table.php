<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profils', function (Blueprint $table) {
            $table->boolean('informed_consent')->default(false)->after('banner');
            $table->boolean('status_verifikasi')->default(false)->after('informed_consent');
            $table->unsignedTinyInteger('usia')->nullable()->after('status_verifikasi');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable()->after('usia');
            $table->string('universitas')->nullable()->after('jenis_kelamin');
            $table->string('nim')->nullable()->after('universitas');
            $table->enum('kelompok_eksperimen', ['A', 'B', 'C', 'D', 'E'])->nullable()->after('nim');
            $table->string('kode_sertifikat')->nullable()->after('kelompok_eksperimen');
        });
    }

    public function down(): void
    {
        Schema::table('profils', function (Blueprint $table) {
            $table->dropColumn([
                'informed_consent',
                'status_verifikasi',
                'usia',
                'jenis_kelamin',
                'universitas',
                'nim',
                'kelompok_eksperimen',
                'kode_sertifikat',
            ]);
        });
    }
};
