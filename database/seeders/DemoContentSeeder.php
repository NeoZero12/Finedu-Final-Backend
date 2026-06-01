<?php

namespace Database\Seeders;

use App\Models\DigitalNudge;
use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\HasilSkor;
use App\Models\Kuesioner;
use App\Models\Materi;
use App\Models\ModulPembelajaran;
use App\Models\ModulProgress;
use App\Models\Profil;
use App\Models\Produk;
use App\Models\Simulasi;
use App\Models\TransaksiSimulasi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate([
            'email' => 'admin@finedu.com',
        ], [
            'name' => 'Admin Finedu',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        Profil::updateOrCreate([
            'user_id' => $admin->id,
        ], [
            'tingkat_literasi' => 'tinggi',
            'tipe_budget' => 'longgar',
            'avatar' => 'https://ui-avatars.com/api/?name=Admin+Finedu&background=1A0354&color=fff',
            'banner' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=1200&q=80',
            'nudge_aktif' => true,
            'informed_consent' => true,
            'status_verifikasi' => true,
            'usia' => 24,
            'jenis_kelamin' => 'perempuan',
            'universitas' => 'Universitas Brawijaya',
            'nim' => 'ADMIN001',
            'kelompok_eksperimen' => 'A',
            'kode_sertifikat' => 'FINEDU-ADMIN01',
        ]);

        $mahasiswa = User::updateOrCreate([
            'email' => 'mhs@student.ub.ac.id',
        ], [
            'name' => 'Mahasiswa Test',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        Profil::updateOrCreate([
            'user_id' => $mahasiswa->id,
        ], [
            'tingkat_literasi' => 'menengah',
            'tipe_budget' => 'ketat',
            'avatar' => 'https://ui-avatars.com/api/?name=Mahasiswa+Test&background=0f172a&color=fff',
            'banner' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
            'nudge_aktif' => true,
            'informed_consent' => true,
            'status_verifikasi' => true,
            'usia' => 20,
            'jenis_kelamin' => 'laki-laki',
            'universitas' => 'Universitas Brawijaya',
            'nim' => '225150200111001',
            'kelompok_eksperimen' => 'A',
            'kode_sertifikat' => 'FINEDU-STU001',
        ]);

        $modules = collect([
            [
                'judul_modul' => 'Pengenalan Literasi Keuangan Mahasiswa',
                'deskripsi' => 'Dasar-dasar mengatur uang saku, membedakan kebutuhan dan keinginan, serta membangun kebiasaan finansial sehat.',
                'materis' => [
                    ['judul_materi' => 'Kenapa Literasi Keuangan Penting?', 'konten' => 'Materi pengantar tentang keputusan finansial harian mahasiswa dan dampaknya pada masa depan.'],
                    ['judul_materi' => 'Membuat Anggaran Bulanan', 'konten' => 'Langkah praktis membagi uang saku ke pos kebutuhan, tabungan, dan hiburan.'],
                ],
            ],
            [
                'judul_modul' => 'Strategi Menggunakan E-Wallet dengan Aman',
                'deskripsi' => 'Pelajari manfaat, risiko, dan trik agar transaksi digital tetap aman serta tidak mendorong impulsive buying.',
                'materis' => [
                    ['judul_materi' => 'Fitur Penting E-Wallet', 'konten' => 'Mengenal top up, pembayaran QRIS, histori transaksi, dan pengingat anggaran.'],
                    ['judul_materi' => 'Keamanan Akun Digital', 'konten' => 'Tips menjaga OTP, PIN, dan keamanan perangkat saat memakai dompet digital.'],
                ],
            ],
            [
                'judul_modul' => 'Belanja Cerdas dan Anti Impulsif',
                'deskripsi' => 'Modul untuk memahami pemicu pembelian impulsif dan cara menahan keputusan belanja yang tidak perlu.',
                'materis' => [
                    ['judul_materi' => 'Mengenali Trigger Belanja', 'konten' => 'Cara mengenali diskon, FOMO, dan pengaruh sosial saat berbelanja.'],
                    ['judul_materi' => 'Checklist Sebelum Checkout', 'konten' => 'Panduan 5 pertanyaan sederhana untuk menyaring keputusan pembelian.'],
                ],
            ],
        ])->map(function (array $item) {
            $modul = ModulPembelajaran::updateOrCreate(
                ['judul_modul' => $item['judul_modul']],
                ['deskripsi' => $item['deskripsi']]
            );

            foreach ($item['materis'] as $materi) {
                Materi::updateOrCreate(
                    [
                        'modul_id' => $modul->id,
                        'judul_materi' => $materi['judul_materi'],
                    ],
                    ['konten' => $materi['konten']]
                );
            }

            return $modul;
        });

        $products = collect([
            ['nama_produk' => 'Buku Tulis Semester', 'harga' => 35000, 'kategori' => 'kebutuhan', 'gambar_url' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=900&q=80'],
            ['nama_produk' => 'Paket Internet Bulanan', 'harga' => 85000, 'kategori' => 'kebutuhan', 'gambar_url' => 'https://images.unsplash.com/photo-1516321497487-e288fb19713f?auto=format&fit=crop&w=900&q=80'],
            ['nama_produk' => 'Headset Belajar Online', 'harga' => 250000, 'kategori' => 'kebutuhan', 'gambar_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=80'],
            ['nama_produk' => 'Minuman Kekinian', 'harga' => 28000, 'kategori' => 'keinginan', 'gambar_url' => 'https://images.unsplash.com/photo-1517705008128-361805f42e86?auto=format&fit=crop&w=900&q=80'],
            ['nama_produk' => 'Skin Game Digital', 'harga' => 150000, 'kategori' => 'keinginan', 'gambar_url' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=80'],
            ['nama_produk' => 'Sepatu Tren Kampus', 'harga' => 420000, 'kategori' => 'keinginan', 'gambar_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80'],
        ])->map(fn (array $item) => Produk::updateOrCreate(
            ['nama_produk' => $item['nama_produk']],
            $item
        ));

        HasilSkor::updateOrCreate([
            'user_id' => $mahasiswa->id,
            'skor_literasi_akhir' => 82,
        ], [
            'total_overspending' => 150000,
        ]);

        HasilSkor::updateOrCreate([
            'user_id' => $admin->id,
            'skor_literasi_akhir' => 95,
        ], [
            'total_overspending' => 0,
        ]);

        Kuesioner::where('user_id', $mahasiswa->id)->delete();
        foreach ([
            ['pertanyaan' => 'Seberapa percaya diri Anda mengelola anggaran bulanan?', 'jawaban_skala' => 4],
            ['pertanyaan' => 'Seberapa sering Anda membandingkan kebutuhan dan keinginan sebelum membeli?', 'jawaban_skala' => 5],
            ['pertanyaan' => 'Seberapa membantu modul dan simulasi dalam memahami literasi keuangan?', 'jawaban_skala' => 4],
        ] as $jawaban) {
            Kuesioner::create(['user_id' => $mahasiswa->id] + $jawaban);
        }

        DigitalNudge::where('user_id', $mahasiswa->id)->delete();
        foreach ([
            ['tipe_nudge' => 'notifikasi', 'diabaikan' => false],
            ['tipe_nudge' => 'goal_reminder', 'diabaikan' => false],
            ['tipe_nudge' => 'warna_merah', 'diabaikan' => true],
        ] as $nudge) {
            DigitalNudge::create(['user_id' => $mahasiswa->id] + $nudge);
        }

        $simulasiAktif = Simulasi::updateOrCreate([
            'user_id' => $mahasiswa->id,
            'status' => 'berlangsung',
        ], [
            'anggaran_awal' => 500000,
            'anggaran_sisa' => 352000,
        ]);

        TransaksiSimulasi::where('simulasi_id', $simulasiAktif->id)->delete();
        foreach ([
            ['nama_item' => 'Top Up Saldo Awal', 'nominal' => 500000, 'arah_transaksi' => 'pemasukan', 'kategori_label' => 'pemasukan', 'pembelian_impulsif' => false],
            ['produk' => 'Buku Tulis Semester', 'nominal' => 35000, 'pembelian_impulsif' => false],
            ['produk' => 'Minuman Kekinian', 'nominal' => 28000, 'pembelian_impulsif' => true],
            ['produk' => 'Paket Internet Bulanan', 'nominal' => 85000, 'pembelian_impulsif' => false],
        ] as $trx) {
            if (($trx['arah_transaksi'] ?? null) === 'pemasukan') {
                TransaksiSimulasi::create([
                    'simulasi_id' => $simulasiAktif->id,
                    'produk_id' => null,
                    'nama_item' => $trx['nama_item'],
                    'kategori_label' => $trx['kategori_label'],
                    'arah_transaksi' => $trx['arah_transaksi'],
                    'nominal' => $trx['nominal'],
                    'pembelian_impulsif' => $trx['pembelian_impulsif'],
                ]);

                continue;
            }

            $produk = $products->firstWhere('nama_produk', $trx['produk']);

            if ($produk) {
                TransaksiSimulasi::create([
                    'simulasi_id' => $simulasiAktif->id,
                    'produk_id' => $produk->id,
                    'nominal' => $trx['nominal'],
                    'pembelian_impulsif' => $trx['pembelian_impulsif'],
                ]);
            }
        }

        if ($modules->isEmpty()) {
            $this->command?->warn('Tidak ada modul yang berhasil dibuat.');
        }

        if ($modules->isNotEmpty()) {
            ModulProgress::updateOrCreate([
                'user_id' => $mahasiswa->id,
                'modul_pembelajaran_id' => $modules->first()->id,
            ], [
                'selesai' => true,
                'completed_at' => now(),
            ]);
        }

        $post = ForumPost::updateOrCreate([
            'user_id' => $mahasiswa->id,
            'title' => 'Tips menahan impulsive buying saat pakai e-wallet',
        ], [
            'body' => 'Saya sering tergoda checkout cepat saat ada promo. Kalian biasanya pakai cara apa supaya tetap sesuai budget?',
        ]);

        ForumComment::updateOrCreate([
            'forum_post_id' => $post->id,
            'user_id' => $admin->id,
        ], [
            'body' => 'Coba aktifkan checklist kebutuhan sebelum checkout dan lihat indikator anggaran lebih dulu.',
        ]);
    }
}
