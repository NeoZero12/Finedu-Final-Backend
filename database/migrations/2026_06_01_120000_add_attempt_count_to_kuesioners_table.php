<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuesioners', function (Blueprint $table) {
            // Menyimpan percobaan keberapa agar batas ulang kuesioner bisa dihitung.
            $table->unsignedTinyInteger('attempt_count')->default(1)->after('benar');
        });
    }

    public function down(): void
    {
        Schema::table('kuesioners', function (Blueprint $table) {
            $table->dropColumn('attempt_count');
        });
    }
};
