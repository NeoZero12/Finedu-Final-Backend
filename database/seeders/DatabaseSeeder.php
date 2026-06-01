<?php

namespace Database\Seeders;

use App\Models\Profil;
use App\Models\Produk;
use App\Models\User;
use App\Support\DefaultLearningModules;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin awal untuk masuk ke dashboard admin.
        $admin = User::firstOrCreate(
            ['email' => 'admin@finedu.com'],
            [
                'name' => 'Admin Finedu',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        Profil::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'tingkat_literasi' => 'tinggi',
                'tipe_budget' => 'longgar',
                'status_verifikasi' => true,
                'universitas' => 'Universitas Brawijaya',
            ]
        );

        // Akun mahasiswa contoh dimulai tanpa progress belajar.
        $student = User::firstOrCreate(
            ['email' => 'mhs@student.ub.ac.id'],
            [
                'name' => 'Mahasiswa Test',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'is_active' => true,
            ]
        );

        Profil::updateOrCreate(
            ['user_id' => $student->id],
            [
                'tingkat_literasi' => 'rendah',
                'tipe_budget' => 'ketat',
                'status_verifikasi' => false,
                'usia' => 20,
                'jenis_kelamin' => 'laki-laki',
                'universitas' => 'Universitas Brawijaya',
                'nim' => '235000000000',
            ]
        );

        // Materi default disiapkan agar halaman admin dan modul tidak kosong di database baru.
        DefaultLearningModules::sync();

        // Produk awal dipakai oleh simulasi belanja saat project baru dijalankan.
        foreach ([
            ['nama_produk' => 'Buku Tulis Semester', 'harga' => 35000, 'kategori' => 'kebutuhan'],
            ['nama_produk' => 'Paket Internet Bulanan', 'harga' => 85000, 'kategori' => 'kebutuhan'],
            ['nama_produk' => 'Minuman Kekinian', 'harga' => 28000, 'kategori' => 'keinginan'],
            ['nama_produk' => 'Tabungan Darurat', 'harga' => 150000, 'kategori' => 'kebutuhan'],
        ] as $product) {
            Produk::updateOrCreate(
                ['nama_produk' => $product['nama_produk']],
                $product
            );
        }
    }
}
