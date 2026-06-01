<?php

namespace App\Support;

use App\Models\Materi;
use App\Models\ModulPembelajaran;

class DefaultLearningModules
{
    public static function sync(): void
    {
        foreach (self::definitions() as $definition) {
            $modul = ModulPembelajaran::updateOrCreate(
                ['judul_modul' => $definition['judul_modul']],
                ['deskripsi' => $definition['deskripsi']]
            );

            $expectedTitles = [];

            foreach ($definition['materis'] as $materi) {
                $expectedTitles[] = $materi['judul_materi'];

                Materi::updateOrCreate(
                    [
                        'modul_id' => $modul->id,
                        'judul_materi' => $materi['judul_materi'],
                    ],
                    ['konten' => $materi['konten']]
                );
            }

            Materi::query()
                ->where('modul_id', $modul->id)
                ->whereNotIn('judul_materi', $expectedTitles)
                ->delete();
        }
    }

    public static function questionnaireFor(string $moduleTitle): array
    {
        foreach (self::definitions() as $definition) {
            if ($definition['judul_modul'] === $moduleTitle) {
                return self::prepareQuestionnaire($definition['kuesioner'], $definition['judul_modul']);
            }
        }

        return [];
    }

    public static function sourceFor(string $moduleTitle): array
    {
        if (class_exists(ModuleJournalSources::class)) {
            $sources = ModuleJournalSources::for($moduleTitle);
            if ($sources !== []) {
                return $sources;
            }
        }

        return self::sharedSources();
    }

    public static function allSources(): array
    {
        if (class_exists(ModuleJournalSources::class)) {
            $sources = ModuleJournalSources::all();
            if ($sources !== []) {
                return $sources;
            }
        }

        return self::sharedSources();
    }

    public static function definitions(): array
    {
        return [
            [
                'judul_modul' => 'Dasar Investasi untuk Mahasiswa',
                'deskripsi' => 'Sintesis 10 jurnal tentang literasi keuangan, investasi, kontrol diri, dan perilaku finansial yang relevan untuk mahasiswa pemula.',
                'sumber_jurnal' => self::sharedSources(),
                'materis' => [
                    [
                        'judul_materi' => 'Sintesis Temuan 10 Jurnal',
                        'konten' => 'Sepuluh jurnal yang dipakai dalam modul ini menunjukkan pola yang konsisten: keputusan investasi yang sehat lahir dari kombinasi literasi keuangan, kontrol diri, perhatian pada detail, dan kemampuan membaca risiko. Investasi bukan aktivitas yang berdiri sendiri, melainkan bagian dari perilaku finansial yang juga dipengaruhi inflasi, kebiasaan menabung, utang, keamanan digital, dan ketahanan terhadap penipuan. Karena itu, mahasiswa perlu memandang investasi sebagai langkah lanjutan setelah fondasi keuangan pribadi cukup stabil, bukan sebagai jalan pintas untuk cepat kaya.',
                    ],
                    [
                        'judul_materi' => 'Makna Investasi bagi Mahasiswa',
                        'konten' => 'Bagi mahasiswa, investasi sebaiknya dimulai dari pertanyaan sederhana: tujuan uang ini apa, kapan akan dipakai, dan apa yang terjadi jika nilainya turun. Jika tujuan masih dekat, misalnya biaya semester, uang kos, sertifikasi, atau kebutuhan magang, maka instrumen yang dipilih harus lebih stabil dan mudah dicairkan. Jika tujuan lebih panjang, mahasiswa bisa mulai belajar instrumen yang risikonya lebih tinggi, tetapi tetap dengan porsi kecil dan dana yang memang siap berfluktuasi. Intinya, investasi yang baik adalah investasi yang sesuai konteks hidup mahasiswa, bukan yang paling ramai dibicarakan.',
                    ],
                    [
                        'judul_materi' => 'Kerangka Pengambilan Keputusan',
                        'konten' => "Sebelum membeli instrumen apa pun, gunakan urutan berpikir berikut:\n- Pisahkan biaya hidup, dana darurat, dan dana investasi.\n- Tentukan tujuan spesifik dan horizon waktunya.\n- Tulis alasan membeli instrumen tersebut dengan bahasa sederhana.\n- Pahami risiko utama, bukan hanya potensi keuntungannya.\n- Tentukan kapan harus evaluasi, bukan hanya kapan harus membeli.\nKerangka ini penting karena banyak kesalahan investasi terjadi bukan karena kurang cerdas, tetapi karena keputusan diambil terlalu cepat, terlalu emosional, atau terlalu dipengaruhi tren.",
                    ],
                    [
                        'judul_materi' => 'Kesalahan yang Sering Terjadi',
                        'konten' => 'Mahasiswa pemula sering melakukan empat kesalahan utama. Pertama, memakai uang yang masih dibutuhkan untuk kebutuhan pokok. Kedua, memilih aset karena teman, influencer, atau FOMO. Ketiga, berfokus pada return tanpa menghitung risiko dan likuiditas. Keempat, tidak memiliki catatan alasan masuk sehingga ketika harga bergerak, keputusan berikutnya menjadi panik dan tidak konsisten. Jurnal-jurnal rujukan menegaskan bahwa kualitas proses berpikir lebih menentukan daripada seberapa cepat seseorang mulai berinvestasi.',
                    ],
                    [
                        'judul_materi' => 'Checklist Praktis untuk Mahasiswa',
                        'konten' => "- Mulai dari nominal kecil agar proses belajar tidak mengganggu keuangan utama.\n- Gunakan tujuan yang dekat dengan kehidupan kampus seperti sertifikasi, laptop, atau dana magang.\n- Jangan menaruh uang kos, uang makan, atau biaya kuliah ke aset berisiko.\n- Lakukan evaluasi berkala berdasarkan tujuan, bukan berdasarkan tren pasar harian.\n- Anggap investasi sebagai alat mencapai tujuan, bukan permainan untuk menebak harga.",
                    ],
                ],
                'kuesioner' => self::questionsInvestmentBasics(),
            ],
            [
                'judul_modul' => 'Memahami Inflasi dan Daya Beli',
                'deskripsi' => 'Sintesis 10 jurnal tentang inflasi, daya beli, penyesuaian anggaran, dan cara mahasiswa merespons kenaikan harga secara rasional.',
                'sumber_jurnal' => self::sharedSources(),
                'materis' => [
                    [
                        'judul_materi' => 'Sintesis Temuan 10 Jurnal',
                        'konten' => 'Jurnal-jurnal yang dirujuk menjelaskan bahwa inflasi tidak hanya berarti harga naik, tetapi juga berarti nilai riil uang menurun. Saat inflasi meningkat, biaya hidup naik, daya beli melemah, dan keputusan menyimpan maupun menempatkan dana menjadi lebih sensitif terhadap risiko. Dalam konteks perilaku finansial, inflasi menekan anggaran rumah tangga, memengaruhi pilihan aset, dan dapat mendorong keputusan yang terburu-buru jika individu tidak memiliki perencanaan yang cukup.',
                    ],
                    [
                        'judul_materi' => 'Dampak Nyata pada Kehidupan Mahasiswa',
                        'konten' => 'Mahasiswa biasanya merasakan inflasi melalui pengeluaran harian yang paling sering dibayar: makan, kos, transportasi, fotokopi, paket data, biaya tugas, dan kebutuhan organisasi. Jika uang saku atau pemasukan tidak ikut naik, maka ruang gerak anggaran otomatis menyempit. Masalahnya, kenaikan kecil pada banyak pos sering tidak langsung terasa di awal, tetapi sangat memengaruhi kondisi kas bulanan. Karena itu, mahasiswa perlu belajar membaca inflasi sebagai sinyal untuk menyesuaikan prioritas, bukan sekadar mengeluh bahwa harga naik.',
                    ],
                    [
                        'judul_materi' => 'Strategi Menjaga Daya Beli',
                        'konten' => 'Ada tiga strategi utama yang disarankan oleh sintesis jurnal. Pertama, perbarui anggaran berdasarkan kondisi harga terbaru, bukan berdasarkan asumsi bulan lalu. Kedua, kurangi porsi uang menganggur yang tidak punya tujuan jelas, karena nilainya terus tergerus. Ketiga, simpan dana sesuai fungsinya: dana harian harus likuid, dana cadangan harus aman, dan dana tujuan jangka menengah perlu ditempatkan dengan lebih terencana. Strategi ini membantu mahasiswa menjaga daya beli tanpa harus membuat keputusan ekstrem.',
                    ],
                    [
                        'judul_materi' => 'Risiko jika Inflasi Diabaikan',
                        'konten' => 'Jika inflasi diabaikan, mahasiswa cenderung tetap memakai pola pengeluaran lama padahal kondisi harga sudah berubah. Akibatnya, tabungan tergerus pelan-pelan, saldo habis lebih cepat, dan kebutuhan penting sering dikorbankan di akhir bulan. Selain itu, keputusan investasi juga bisa menjadi tidak tepat karena fokus hanya pada nominal keuntungan tanpa mempertimbangkan penurunan daya beli. Dalam jangka panjang, ketidakpekaan terhadap inflasi membuat keuangan terasa selalu ketat walaupun pemasukan tidak berubah.',
                    ],
                    [
                        'judul_materi' => 'Checklist Praktis untuk Mahasiswa',
                        'konten' => "- Catat tiga pengeluaran kampus yang paling cepat naik nilainya.\n- Perbarui batas jajan dan hiburan saat harga kebutuhan pokok meningkat.\n- Biasakan belanja dengan daftar agar perubahan harga tidak memicu keputusan panik.\n- Bandingkan harga kebutuhan utama secara berkala, minimal bulanan.\n- Pastikan ada penyesuaian anggaran, bukan hanya penyesalan di akhir bulan.",
                    ],
                ],
                'kuesioner' => self::questionsInflation(),
            ],
            [
                'judul_modul' => 'Dana Darurat dan Ketahanan Finansial',
                'deskripsi' => 'Sintesis 10 jurnal tentang dana darurat, tekanan finansial, perilaku menabung, dan ketahanan mahasiswa saat menghadapi situasi tidak terduga.',
                'sumber_jurnal' => self::sharedSources(),
                'materis' => [
                    [
                        'judul_materi' => 'Sintesis Temuan 10 Jurnal',
                        'konten' => 'Jurnal-jurnal rujukan menunjukkan bahwa ketahanan finansial sangat dipengaruhi oleh kemampuan seseorang membangun cadangan dana yang mudah diakses. Tekanan kewajiban keuangan memang dapat menghambat tabungan darurat, tetapi pengetahuan finansial dan kebiasaan mengatur prioritas terbukti membantu individu tetap memiliki perlindungan minimum. Artinya, dana darurat bukan hanya persoalan besarnya saldo, melainkan seberapa sadar seseorang terhadap risiko hidup sehari-hari dan seberapa disiplin ia menjaga cadangan tersebut.',
                    ],
                    [
                        'judul_materi' => 'Mengapa Mahasiswa Membutuhkannya',
                        'konten' => 'Mahasiswa sering merasa belum perlu dana darurat karena belum punya tanggungan keluarga besar. Padahal, risiko finansial mahasiswa tetap nyata: laptop rusak, biaya kesehatan, tiket pulang mendadak, keterlambatan kiriman, kebutuhan akademik tambahan, atau kehilangan barang penting. Dalam kondisi seperti ini, dana darurat berfungsi menjaga agar masalah sementara tidak berubah menjadi utang, gangguan akademik, atau pengorbanan atas kebutuhan pokok.',
                    ],
                    [
                        'judul_materi' => 'Cara Membangun Dana Darurat',
                        'konten' => 'Pendekatan paling realistis untuk mahasiswa adalah memulai dari target sederhana, misalnya satu bulan pengeluaran pokok. Target itu bisa dibagi menjadi setoran kecil yang konsisten setiap menerima uang saku, beasiswa, atau penghasilan tambahan. Dana ini sebaiknya disimpan terpisah dari uang operasional harian dan ditempatkan di tempat yang aman serta mudah dicairkan. Semakin sederhana sistemnya, semakin besar peluang dana darurat benar-benar terbentuk dan tidak tercampur dengan pengeluaran lain.',
                    ],
                    [
                        'judul_materi' => 'Prinsip Penggunaan yang Benar',
                        'konten' => 'Dana darurat hanya boleh dipakai untuk kebutuhan yang benar-benar mendesak, tidak direncanakan, dan penting. Ini bukan dana untuk promo, jalan-jalan, atau belanja spontan yang diberi label darurat. Jika aturan ini kabur, saldo dana darurat akan terus bocor. Jurnal rujukan menegaskan bahwa kekuatan utama dana darurat bukan pada jumlah besar, tetapi pada kepastian bahwa dana tersebut tersedia saat dibutuhkan dan tidak habis untuk hal yang seharusnya bisa ditunda.',
                    ],
                    [
                        'judul_materi' => 'Checklist Praktis untuk Mahasiswa',
                        'konten' => "- Dana darurat kecil tetap berguna untuk mencegah utang mendadak.\n- Simpan terpisah dari uang operasional harian.\n- Isi sedikit demi sedikit setiap menerima kiriman atau pemasukan.\n- Gunakan hanya untuk kebutuhan mendesak, penting, dan tidak terencana.\n- Perlakukan dana ini sebagai pelindung, bukan saldo cadangan belanja.",
                    ],
                ],
                'kuesioner' => self::questionsEmergencyFund(),
            ],
            [
                'judul_modul' => 'Keamanan Digital dalam Transaksi Keuangan',
                'deskripsi' => 'Sintesis 10 jurnal tentang keamanan pembayaran digital, rasa kontrol pengguna, dan kebiasaan yang membuat transaksi online tetap aman.',
                'sumber_jurnal' => self::sharedSources(),
                'materis' => [
                    [
                        'judul_materi' => 'Sintesis Temuan 10 Jurnal',
                        'konten' => 'Jurnal-jurnal yang digunakan menunjukkan bahwa keamanan digital tidak hanya ditentukan oleh teknologi di belakang layar, tetapi juga oleh pengalaman pengguna saat melakukan transaksi. Rasa aman, rasa kontrol, kejelasan antarmuka, dan kemampuan membaca peringatan sangat memengaruhi apakah seseorang akan memakai layanan pembayaran digital secara konsisten dan aman. Dengan kata lain, sistem yang kuat tetap bisa menghasilkan kesalahan jika penggunanya bingung, terburu-buru, atau tidak memahami langkah verifikasi yang tersedia.',
                    ],
                    [
                        'judul_materi' => 'Risiko yang Dekat dengan Mahasiswa',
                        'konten' => 'Mahasiswa adalah pengguna aktif mobile banking, e-wallet, marketplace, dan pembayaran QR. Risiko yang paling dekat biasanya bukan serangan teknis yang rumit, tetapi kebiasaan klik cepat, mengabaikan detail merchant, membagikan OTP karena panik, memakai jaringan atau perangkat yang kurang aman, dan tidak memeriksa riwayat transaksi. Kebiasaan ini membuat akun yang sebenarnya memiliki fitur keamanan tetap rentan terhadap salah transfer, akses tidak sah, atau penipuan berkedok bantuan.',
                    ],
                    [
                        'judul_materi' => 'Kebiasaan Aman yang Harus Dibangun',
                        'konten' => 'Mahasiswa perlu membangun kebiasaan yang sederhana tetapi konsisten: aktifkan PIN dan verifikasi tambahan, baca nama merchant sebelum bayar, cek nominal sebelum menekan konfirmasi, dan tinjau riwayat transaksi secara berkala. Jika ada transaksi asing, langkah awalnya harus jelas: ubah kredensial, keluar dari perangkat lain jika perlu, dan hubungi kanal resmi layanan. Kebiasaan ini meningkatkan rasa kontrol pengguna dan secara langsung menurunkan peluang kesalahan finansial digital.',
                    ],
                    [
                        'judul_materi' => 'Prinsip Evaluasi Transaksi',
                        'konten' => 'Setiap transaksi digital sebaiknya melewati tiga pemeriksaan: siapa penerimanya, berapa nominalnya, dan melalui kanal apa transaksi dilakukan. Tiga pertanyaan ini terlihat sederhana, tetapi justru menjadi garis pertahanan paling efektif terhadap kesalahan pengguna. Jurnal-jurnal rujukan memperlihatkan bahwa pengguna yang merasa paham dan berdaya atas proses transaksi cenderung lebih aman dibanding pengguna yang hanya mengandalkan rasa percaya pada aplikasinya.',
                    ],
                    [
                        'judul_materi' => 'Checklist Praktis untuk Mahasiswa',
                        'konten' => "- Jangan pernah membagikan OTP, PIN, atau password.\n- Biasakan membaca nama merchant sebelum membayar.\n- Cek histori transaksi minimal sekali seminggu.\n- Gunakan perangkat pribadi untuk transaksi yang sensitif.\n- Jika ada transaksi asing, segera ubah kredensial dan hubungi kanal resmi.",
                    ],
                ],
                'kuesioner' => self::questionsSecurity(),
            ],
            [
                'judul_modul' => 'E-Wallet, QRIS, dan Kebiasaan Aman',
                'deskripsi' => 'Sintesis 10 jurnal tentang adopsi e-wallet, QRIS, kepercayaan pengguna, dan cara memakai pembayaran digital tanpa kehilangan kontrol.',
                'sumber_jurnal' => self::sharedSources(),
                'materis' => [
                    [
                        'judul_materi' => 'Sintesis Temuan 10 Jurnal',
                        'konten' => 'Rangkuman dari 10 jurnal menunjukkan bahwa pembayaran digital diterima pengguna bukan hanya karena teknologinya modern, tetapi karena dianggap memberi manfaat nyata: cepat, praktis, aman, dan mudah dipahami. Kepercayaan, kemudahan penggunaan, desain yang jelas, serta rasa bahwa sistem membantu aktivitas harian menjadi faktor utama adopsi. Namun, manfaat ini hanya benar-benar terasa jika pengguna tetap memegang kontrol atas saldo, tujuan penggunaan, dan pola transaksi yang dilakukan.',
                    ],
                    [
                        'judul_materi' => 'Mengapa Mahasiswa Mudah Tergoda',
                        'konten' => 'Mahasiswa sangat dekat dengan e-wallet dan QRIS karena hampir semua transaksi harian bisa dilakukan lewat ponsel. Masalahnya, kemudahan ini sering mengaburkan rasa keluar uang. Transaksi terasa ringan karena tidak melihat uang fisik berpindah tangan, sementara promo, cashback, dan notifikasi membuat pembelian terasa wajar. Jurnal rujukan menunjukkan bahwa tanpa batas penggunaan yang jelas, teknologi yang seharusnya membantu efisiensi justru mempercepat perilaku impulsif.',
                    ],
                    [
                        'judul_materi' => 'Cara Memakai E-Wallet dengan Sehat',
                        'konten' => 'Strategi yang paling sehat adalah mengisi saldo sesuai rencana mingguan atau sesuai kategori pengeluaran tertentu. Mahasiswa juga bisa memisahkan fungsi dompet digital, misalnya satu untuk kebutuhan rutin dan satu lagi untuk hiburan jika memang diperlukan. Riwayat pembayaran perlu dibaca ulang, bukan diabaikan, agar pengguna paham ke mana uang benar-benar keluar. E-wallet dan QRIS akan membantu jika dipakai untuk transaksi yang memang sudah direncanakan, bukan sebagai pemicu checkout spontan.',
                    ],
                    [
                        'judul_materi' => 'Tanda Penggunaan yang Mulai Tidak Sehat',
                        'konten' => 'Ada beberapa tanda bahwa penggunaan dompet digital mulai tidak sehat: saldo sering habis tanpa tahu untuk apa, top up dilakukan berulang kali dalam satu minggu tanpa rencana, promo menjadi alasan utama belanja, dan riwayat transaksi tidak pernah diperiksa. Saat tanda-tanda ini muncul, masalahnya bukan pada teknologinya, tetapi pada hilangnya kendali perilaku. Di titik ini, mahasiswa perlu kembali ke fungsi dasar pembayaran digital: mempermudah transaksi, bukan mengaburkan keputusan keuangan.',
                    ],
                    [
                        'judul_materi' => 'Checklist Praktis untuk Mahasiswa',
                        'konten' => "- Isi saldo e-wallet secukupnya, bukan sebanyak mungkin.\n- Bedakan pembayaran kebutuhan kuliah dan hiburan.\n- Matikan notifikasi promo yang terlalu sering memicu checkout.\n- Cek riwayat pembayaran secara rutin.\n- Gunakan dompet digital untuk efisiensi, bukan untuk impulsivitas.",
                    ],
                ],
                'kuesioner' => self::questionsEwallet(),
            ],
            [
                'judul_modul' => 'Anggaran Bulanan dan Prioritas Pengeluaran',
                'deskripsi' => 'Sintesis 10 jurnal tentang anggaran, perhatian terbatas, kebocoran pengeluaran, dan cara mahasiswa menjaga prioritas bulanan.',
                'sumber_jurnal' => self::sharedSources(),
                'materis' => [
                    [
                        'judul_materi' => 'Sintesis Temuan 10 Jurnal',
                        'konten' => 'Jurnal-jurnal rujukan menunjukkan bahwa literasi keuangan memang membantu perilaku finansial, tetapi pengetahuan saja tidak cukup. Banyak keputusan buruk terjadi karena perhatian pengguna terbatas: pengeluaran kecil tidak dicatat, langganan dibiarkan berjalan, biaya tambahan dianggap sepele, dan realisasi anggaran tidak pernah dibandingkan dengan rencana awal. Karena itu, anggaran bukan hanya dokumen perencanaan, tetapi sistem perhatian yang membantu seseorang tetap sadar terhadap ke mana uang bergerak.',
                    ],
                    [
                        'judul_materi' => 'Masalah Umum pada Keuangan Mahasiswa',
                        'konten' => 'Mahasiswa sering mengetahui pentingnya membuat anggaran, tetapi tidak punya kebiasaan memeriksa realisasinya. Akibatnya, kebocoran justru datang dari hal-hal kecil yang berulang: kopi, ongkir, jajanan, top up mendadak, biaya langganan aplikasi, atau pembelian yang tidak direncanakan. Jika pengeluaran seperti ini tidak disadari, anggaran akan gagal bukan karena rancangannya salah, tetapi karena perhatian pengguna terlalu mudah terpecah.',
                    ],
                    [
                        'judul_materi' => 'Cara Membuat Anggaran yang Bisa Dipakai',
                        'konten' => 'Anggaran yang baik untuk mahasiswa tidak perlu rumit. Cukup gunakan kategori yang mudah dipahami seperti kebutuhan pokok, akademik, tabungan, transportasi, dan hiburan. Setelah itu, tetapkan batas realistis, bukan batas yang terlalu ketat hingga mustahil dijalankan. Kunci utamanya adalah review rutin, minimal mingguan, agar pengguna bisa melihat sejak awal jika ada pos yang mulai melampaui batas. Semakin cepat kebocoran terlihat, semakin mudah diperbaiki.',
                    ],
                    [
                        'judul_materi' => 'Prinsip Prioritas Pengeluaran',
                        'konten' => 'Saat anggaran terbatas, urutan prioritas harus jelas. Kebutuhan hidup pokok, biaya akademik, dan kewajiban penting harus ditempatkan lebih dulu daripada hiburan atau pengeluaran sosial. Ini bukan berarti mahasiswa tidak boleh menikmati uangnya, tetapi semua pengeluaran harus dikembalikan pada pertanyaan apakah dampaknya sepadan dengan kondisi keuangan saat ini. Prinsip prioritas membantu mahasiswa mengambil keputusan tanpa harus menunggu saldo benar-benar habis.',
                    ],
                    [
                        'judul_materi' => 'Checklist Praktis untuk Mahasiswa',
                        'konten' => "- Gunakan kategori anggaran yang sederhana dan mudah dipantau.\n- Review mingguan lebih efektif daripada menunggu akhir bulan.\n- Langganan kecil berulang perlu diawasi karena sering luput.\n- Dahulukan kebutuhan pokok dan akademik sebelum hiburan.\n- Anggaran yang realistis lebih berguna daripada anggaran yang terlalu ketat.",
                    ],
                ],
                'kuesioner' => self::questionsBudget(),
            ],
            [
                'judul_modul' => 'Menabung Konsisten dan Bunga Majemuk',
                'deskripsi' => 'Sintesis 10 jurnal tentang kebiasaan menabung, tujuan jangka panjang, safety net, dan logika bunga majemuk untuk mahasiswa.',
                'sumber_jurnal' => self::sharedSources(),
                'materis' => [
                    [
                        'judul_materi' => 'Sintesis Temuan 10 Jurnal',
                        'konten' => 'Jurnal-jurnal rujukan memperlihatkan bahwa kebiasaan menabung bertahan lebih lama ketika seseorang memiliki tujuan jangka panjang yang jelas, sistem penyimpanan yang terpisah, dan perlindungan dasar seperti dana darurat. Menabung bukan sekadar tindakan menyisihkan uang, tetapi proses membangun struktur mental bahwa tidak semua uang hari ini harus dihabiskan hari ini. Dalam kerangka ini, bunga majemuk menjadi relevan karena waktu bekerja lebih efektif ketika kebiasaan menabung dilakukan secara konsisten.',
                    ],
                    [
                        'judul_materi' => 'Mengapa Mahasiswa Sulit Konsisten',
                        'konten' => 'Mahasiswa sering gagal menabung bukan karena tidak paham pentingnya tabungan, tetapi karena tujuan menabung terlalu abstrak. Saat tabungan tidak diberi nama, uang terasa selalu tersedia untuk kebutuhan jangka pendek atau keinginan spontan. Sebaliknya, ketika tabungan diberi tujuan yang konkret seperti biaya sertifikasi, dana magang, laptop, atau modal proyek, motivasi menjadi lebih kuat dan keputusan untuk tidak mengganggu saldo menjadi lebih mudah dipertahankan.',
                    ],
                    [
                        'judul_materi' => 'Sistem Menabung yang Lebih Efektif',
                        'konten' => 'Sistem yang efektif biasanya sederhana: tentukan tanggal tetap menabung, gunakan akun terpisah, dan pecah target besar menjadi target bulanan yang masuk akal. Menabung setelah membelanjakan sisa uang sering gagal, sedangkan menabung lebih awal membuat prioritas menjadi lebih jelas. Prinsip bunga majemuk di sini perlu dipahami secara praktis: semakin cepat kebiasaan dimulai dan semakin konsisten dilakukan, semakin besar manfaat waktu terhadap pertumbuhan dana.',
                    ],
                    [
                        'judul_materi' => 'Hubungan dengan Safety Net',
                        'konten' => 'Jurnal rujukan juga menekankan bahwa kebiasaan menabung menjadi lebih stabil jika seseorang sudah memiliki safety net dasar. Alasannya sederhana: saat tidak ada perlindungan minimum, tabungan tujuan jangka panjang akan terus terganggu oleh kebutuhan mendadak. Karena itu, mahasiswa sebaiknya membangun dana darurat dasar terlebih dahulu atau setidaknya berjalan beriringan dengan tabungan tujuan. Dengan begitu, tabungan tidak selalu dikorbankan setiap kali ada masalah kecil.',
                    ],
                    [
                        'judul_materi' => 'Checklist Praktis untuk Mahasiswa',
                        'konten' => "- Beri nama tabungan sesuai tujuan agar motivasinya jelas.\n- Gunakan akun terpisah dari uang jajan atau uang harian.\n- Menabung pada tanggal tetap membantu membangun disiplin.\n- Mulai dari nominal kecil tetapi konsisten.\n- Pahami bahwa waktu dan kebiasaan lebih kuat daripada menunggu saldo besar.",
                    ],
                ],
                'kuesioner' => self::questionsSaving(),
            ],
            [
                'judul_modul' => 'Pinjaman Online, Bunga, dan Risiko Utang',
                'deskripsi' => 'Sintesis 10 jurnal tentang BNPL, pinjaman digital, bunga, dan risiko utang konsumtif yang paling dekat dengan mahasiswa.',
                'sumber_jurnal' => self::sharedSources(),
                'materis' => [
                    [
                        'judul_materi' => 'Sintesis Temuan 10 Jurnal',
                        'konten' => 'Rujukan jurnal memperlihatkan bahwa pinjaman digital dan skema buy now pay later tampak menarik karena cepat, mudah, dan terasa ringan di awal. Namun, kemudahan itu justru dapat mendorong konsumsi berlebih ketika pengguna tidak menghitung total biaya, durasi kewajiban, dan dampaknya pada arus kas. Dalam banyak kasus, keputusan utang yang buruk tidak lahir dari kebutuhan mendesak, tetapi dari kenyamanan teknologi, tekanan promo, dan ilusi bahwa cicilan kecil selalu aman.',
                    ],
                    [
                        'judul_materi' => 'Mengapa Mahasiswa Rentan',
                        'konten' => 'Mahasiswa rentan karena sering berada pada fase ingin mengikuti gaya hidup, membutuhkan perangkat pendukung kuliah, dan terpapar promosi digital hampir setiap hari. Di saat yang sama, pendapatan biasanya masih terbatas dan belum stabil. Kombinasi ini membuat utang digital terlihat seperti solusi cepat. Padahal, jika pinjaman diambil untuk kebutuhan konsumtif atau keputusan impulsif, yang terjadi hanyalah memindahkan beban ke masa depan sambil mempersempit ruang gerak anggaran bulanan.',
                    ],
                    [
                        'judul_materi' => 'Cara Menilai Apakah Utang Layak',
                        'konten' => 'Sebelum mengambil pinjaman atau cicilan, mahasiswa harus menjawab empat hal: apa manfaat barang atau kebutuhan yang dibiayai, berapa total biaya yang harus dibayar, dari mana sumber pembayaran tiap periode, dan apa yang dikorbankan jika pembayaran itu tetap berjalan. Jika jawaban untuk empat pertanyaan ini tidak jelas, maka keputusan utang belum layak diambil. Fokus evaluasi harus pada total beban, bukan pada nominal kecil per hari atau per minggu yang sering dipakai dalam iklan.',
                    ],
                    [
                        'judul_materi' => 'Batas Aman yang Perlu Dijaga',
                        'konten' => 'Utang tidak selalu salah, tetapi harus berada dalam batas yang jelas. Biaya makan, kos, transportasi, kesehatan, dan kuliah harus tetap aman sebelum mahasiswa mengambil cicilan apa pun. Jika kewajiban pinjaman membuat kebutuhan pokok menjadi rapuh, maka utang tersebut sudah melewati batas sehat. Jurnal-jurnal rujukan menegaskan bahwa kenyamanan awal utang digital sering menyembunyikan tekanan jangka menengah yang justru lebih berat.',
                    ],
                    [
                        'judul_materi' => 'Checklist Praktis untuk Mahasiswa',
                        'konten' => "- Hitung total cicilan, bukan hanya nominal kecil per hari.\n- Hindari utang untuk gaya hidup, promo sesaat, atau FOMO.\n- Pastikan biaya makan, kos, transportasi, dan kuliah tetap aman.\n- Jika alasan pinjam atau sumber pembayarannya tidak jelas, batalkan.\n- Anggap pinjaman sebagai kewajiban masa depan, bukan kelonggaran belanja hari ini.",
                    ],
                ],
                'kuesioner' => self::questionsDebt(),
            ],
            [
                'judul_modul' => 'Literasi Digital dan Jejak Finansial',
                'deskripsi' => 'Sintesis 10 jurnal tentang literasi digital, kontrol diri, jejak transaksi, dan cara mahasiswa mengevaluasi perilaku finansialnya.',
                'sumber_jurnal' => self::sharedSources(),
                'materis' => [
                    [
                        'judul_materi' => 'Sintesis Temuan 10 Jurnal',
                        'konten' => 'Jurnal-jurnal yang dirujuk menunjukkan bahwa literasi keuangan tidak otomatis menghasilkan perilaku finansial yang baik jika tidak disertai kontrol diri. Dalam lingkungan digital, keputusan keuangan dipengaruhi oleh notifikasi, promo, rekomendasi aplikasi, kemudahan checkout, dan jejak transaksi yang terus bertambah. Jejak digital ini sebenarnya sangat berharga karena memperlihatkan pola nyata perilaku keuangan. Jika dibaca dengan benar, data tersebut bisa menjadi alat evaluasi yang lebih jujur daripada sekadar mengandalkan ingatan atau niat.',
                    ],
                    [
                        'judul_materi' => 'Mengapa Jejak Finansial Penting bagi Mahasiswa',
                        'konten' => 'Mahasiswa hidup dalam ekosistem digital yang padat distraksi: marketplace, layanan pesan-antar, hiburan berlangganan, e-wallet, dan media sosial yang terus memunculkan ajakan konsumsi. Dalam situasi seperti ini, jejak transaksi menjadi cermin perilaku yang sangat penting. Dari sana mahasiswa bisa melihat pengeluaran mana yang rutin, mana yang emosional, mana yang dipicu FOMO, dan mana yang benar-benar menunjang kebutuhan. Kesadaran seperti ini membuat evaluasi keuangan menjadi lebih konkret.',
                    ],
                    [
                        'judul_materi' => 'Cara Membaca Pola dari Riwayat Transaksi',
                        'konten' => 'Riwayat transaksi sebaiknya dibaca secara mingguan dengan pertanyaan yang spesifik: pengeluaran mana yang paling sering muncul, apa pemicunya, kapan saya paling sering belanja impulsif, dan aplikasi apa yang paling banyak menyedot saldo. Dari pertanyaan ini, mahasiswa bisa mengenali hubungan antara teknologi dan perilaku. Literasi digital finansial berarti mampu membaca pola tersebut lalu mengubah kebiasaan, misalnya dengan membatasi notifikasi, menghapus aplikasi pemicu, atau menetapkan jam bebas checkout.',
                    ],
                    [
                        'judul_materi' => 'Hubungan antara Kontrol Diri dan Teknologi',
                        'konten' => 'Teknologi modern dirancang untuk mengurangi friksi, sedangkan pengelolaan keuangan yang baik justru sering membutuhkan jeda. Inilah sebabnya kontrol diri menjadi pusat dari literasi finansial digital. Mahasiswa perlu sadar bahwa mudahnya top up, checkout, dan pembayaran satu klik bukan berarti semua transaksi layak dilakukan. Jurnal-jurnal rujukan menegaskan bahwa pengetahuan akan lebih berguna jika disertai kemampuan menahan diri dan kemauan untuk mengevaluasi data perilaku sendiri.',
                    ],
                    [
                        'judul_materi' => 'Checklist Praktis untuk Mahasiswa',
                        'konten' => "- Riwayat transaksi adalah alat evaluasi, bukan sekadar arsip.\n- Batasi izin aplikasi dan notifikasi yang tidak relevan.\n- Kenali pengeluaran yang dipicu FOMO, stres, atau kebosanan.\n- Evaluasi transaksi mingguan dengan pertanyaan yang spesifik.\n- Latih kontrol diri saat teknologi memudahkan belanja terlalu cepat.",
                    ],
                ],
                'kuesioner' => self::questionsDigitalLiteracy(),
            ],
            [
                'judul_modul' => 'Diversifikasi dan Manajemen Risiko',
                'deskripsi' => 'Sintesis 10 jurnal tentang diversifikasi, batas manfaat penyebaran aset, dan cara mahasiswa memahami risiko secara lebih realistis.',
                'sumber_jurnal' => self::sharedSources(),
                'materis' => [
                    [
                        'judul_materi' => 'Sintesis Temuan 10 Jurnal',
                        'konten' => 'Jurnal-jurnal rujukan menjelaskan bahwa diversifikasi memang penting, tetapi manfaatnya tidak otomatis muncul hanya karena dana disebar ke banyak tempat. Efektivitas diversifikasi bergantung pada pemahaman pengguna terhadap aset yang dipilih, fungsi masing-masing instrumen, tingkat korelasi risikonya, dan kondisi ketidakpastian yang sedang terjadi. Karena itu, diversifikasi harus dipahami sebagai bagian dari manajemen risiko yang sadar, bukan sekadar kebiasaan membeli banyak aset agar terasa aman.',
                    ],
                    [
                        'judul_materi' => 'Kesalahan Umum Mahasiswa Pemula',
                        'konten' => 'Mahasiswa pemula sering jatuh ke dua ekstrem. Ekstrem pertama adalah menaruh seluruh dana pada satu aset yang sedang populer. Ekstrem kedua adalah membeli banyak instrumen sekaligus tanpa memahami apa yang dibeli. Kedua pendekatan ini sama-sama berbahaya. Yang pertama membuat risiko terlalu terkonsentrasi, sedangkan yang kedua menciptakan portofolio yang tampak beragam tetapi sebenarnya tidak dipahami dan sulit dievaluasi.',
                    ],
                    [
                        'judul_materi' => 'Cara Menyusun Portofolio yang Masuk Akal',
                        'konten' => 'Pendekatan yang lebih sehat adalah membangun portofolio sederhana yang setiap unsurnya bisa dijelaskan. Untuk tiap instrumen, mahasiswa harus tahu mengapa instrumen itu dipilih, tujuan apa yang dilayani, risiko apa yang dibawa, dan kapan dana tersebut mungkin dibutuhkan kembali. Jika sebuah aset tidak bisa dijelaskan fungsinya, besar kemungkinan aset itu dibeli tanpa dasar yang kuat. Portofolio yang sederhana tetapi dipahami jauh lebih berguna daripada portofolio ramai yang membingungkan.',
                    ],
                    [
                        'judul_materi' => 'Prinsip Manajemen Risiko',
                        'konten' => 'Manajemen risiko berarti menerima bahwa risiko tidak bisa dihapus, hanya bisa dikelola. Diversifikasi membantu dengan cara membatasi dampak jika satu instrumen bermasalah, tetapi tidak menghapus kebutuhan untuk memahami produk, menjaga likuiditas, dan menyesuaikan dengan tujuan. Mahasiswa perlu melihat risiko sebagai bagian dari keputusan, bukan gangguan yang bisa diabaikan selama aset tersebut sedang naik.',
                    ],
                    [
                        'judul_materi' => 'Checklist Praktis untuk Mahasiswa',
                        'konten' => "- Jangan menaruh semua dana pada satu aset populer.\n- Jangan merasa aman hanya karena jumlah aset banyak.\n- Pilih instrumen yang benar-benar dipahami fungsi dan risikonya.\n- Tulis peran tiap aset dalam tujuan keuangan.\n- Portofolio sederhana tetapi jelas biasanya lebih sehat untuk pemula.",
                    ],
                ],
                'kuesioner' => self::questionsDiversification(),
            ],
            [
                'judul_modul' => 'Mengenali Penipuan Finansial Online',
                'deskripsi' => 'Sintesis 10 jurnal tentang tekanan waktu, psikologi korban, dan cara mahasiswa mengenali serta menghindari penipuan finansial online.',
                'sumber_jurnal' => self::sharedSources(),
                'materis' => [
                    [
                        'judul_materi' => 'Sintesis Temuan 10 Jurnal',
                        'konten' => 'Jurnal-jurnal rujukan menunjukkan bahwa penipuan finansial online sangat sering berhasil bukan karena teknologinya canggih, tetapi karena pelaku mampu memanipulasi keadaan psikologis korban. Tekanan waktu, rasa takut, rasa panik, dan janji hadiah cepat membuat kemampuan verifikasi melemah. Saat seseorang merasa harus segera bertindak, ia cenderung melewati proses cek fakta, mengabaikan tanda bahaya, dan mengambil keputusan yang justru merugikan dirinya sendiri.',
                    ],
                    [
                        'judul_materi' => 'Modus yang Dekat dengan Mahasiswa',
                        'konten' => 'Mahasiswa sangat rentan terhadap modus yang meniru situasi yang terasa akrab: pemberitahuan akun diblokir, beasiswa palsu, promo terbatas, tautan hadiah, lowongan kerja fiktif, hingga pesan yang mengatasnamakan admin kampus atau layanan pembayaran. Modus ini bekerja karena terlihat relevan dan mendesak. Penipu tidak perlu selalu meretas sistem; cukup membuat korban percaya bahwa menunda verifikasi adalah hal yang berbahaya.',
                    ],
                    [
                        'judul_materi' => 'Aturan Respon yang Benar',
                        'konten' => 'Aturan yang paling penting adalah: jika sebuah pesan menuntut keputusan segera, justru perlakukan itu sebagai tanda bahaya pertama. Hentikan proses, jangan klik apa pun dulu, dan verifikasi hanya melalui kanal resmi yang Anda cari sendiri. Jangan pernah membagikan OTP, PIN, password, atau kode verifikasi dalam situasi mendesak. Dalam keamanan finansial digital, jeda beberapa menit hampir selalu lebih aman daripada reaksi cepat yang tidak diperiksa.',
                    ],
                    [
                        'judul_materi' => 'Tanda-Tanda yang Harus Dicurigai',
                        'konten' => 'Beberapa tanda perlu langsung dicurigai: bahasa yang mendesak, ancaman akun akan diblokir, hadiah yang harus segera diklaim, permintaan data sensitif, tautan yang dikirim dari nomor atau akun tidak jelas, dan ajakan memindahkan percakapan ke kanal tidak resmi. Jurnal rujukan menegaskan bahwa penipu memanfaatkan kecepatan respons korban. Maka, meningkatkan kewaspadaan berarti memperlambat respons secara sadar.',
                    ],
                    [
                        'judul_materi' => 'Checklist Praktis untuk Mahasiswa',
                        'konten' => "- Tekanan waktu adalah sinyal bahaya, bukan alasan untuk tergesa-gesa.\n- Verifikasi akun atau promosi hanya lewat kanal resmi.\n- Jangan pernah memberikan OTP, PIN, atau password pada siapa pun.\n- Curigai pesan yang memaksa klik, transfer, atau verifikasi cepat.\n- Jeda sebentar untuk mengecek bisa menyelamatkan dana dan akun.",
                    ],
                ],
                'kuesioner' => self::questionsFraud(),
            ],
        ];
    }

    private static function questionsInvestmentBasics(): array
    {
        return [
            ['pertanyaan' => 'Menurut ringkasan jurnal, investasi yang sehat dimulai dari?', 'opsi' => ['Menentukan tujuan, risiko, dan horizon waktu', 'Mengejar return tertinggi', 'Mengikuti tren teman', 'Meminjam modal'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Literasi keuangan dalam investasi membantu pengguna?', 'opsi' => ['Memahami risiko dan konsekuensi jangka panjang', 'Menghapus semua risiko pasar', 'Mendapat untung pasti', 'Membeli aset tanpa analisis'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Bagi mahasiswa, keputusan investasi sebaiknya disesuaikan dengan?', 'opsi' => ['Tujuan dan profil risiko', 'Jumlah iklan yang dilihat', 'Harga aset termahal', 'Saran anonim'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Sebelum investasi, dana yang perlu dipisahkan lebih dulu adalah?', 'opsi' => ['Biaya hidup dan dana darurat', 'Seluruh uang belanja', 'Semua saldo e-wallet', 'Uang pinjaman'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Kebiasaan penting agar investasi tidak berubah menjadi spekulasi adalah?', 'opsi' => ['Mencatat alasan keputusan', 'Membeli saat bosan', 'Masuk pasar setiap hari', 'Menghindari evaluasi'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Jika tujuan masih jangka pendek, pilihan instrumen sebaiknya?', 'opsi' => ['Lebih stabil dan likuid', 'Paling fluktuatif', 'Tanpa izin resmi', 'Berbasis rumor'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Ringkasan jurnal menekankan bahwa modal kecil?', 'opsi' => ['Tetap berguna untuk membangun kebiasaan analisis', 'Tidak ada manfaatnya', 'Harus dipinjamkan dulu', 'Hanya cocok untuk judi'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Investasi yang baik menurut modul lebih menekankan?', 'opsi' => ['Kecocokan tujuan dan disiplin', 'Kecepatan untung', 'Popularitas aset', 'Frekuensi transaksi'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Apa fungsi evaluasi berkala?', 'opsi' => ['Memastikan keputusan tetap sesuai tujuan', 'Menjamin harga naik', 'Menghapus risiko total', 'Mengganti semua aset harian'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Aset berisiko tinggi sebaiknya dihindari jika?', 'opsi' => ['Dana yang dipakai masih dibutuhkan untuk biaya utama', 'Tujuan investasi sangat jelas', 'Sudah ada anggaran', 'Dana berasal dari surplus'], 'jawaban_benar' => 1],
        ];
    }

    private static function questionsInflation(): array
    {
        return [
            ['pertanyaan' => 'Inflasi dalam modul ini terutama menurunkan?', 'opsi' => ['Daya beli uang', 'Jumlah jam kuliah', 'Akses internet', 'Nilai kartu mahasiswa'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Jurnal menjelaskan bahwa inflasi membuat pasar menjadi?', 'opsi' => ['Lebih sensitif dan volatil', 'Pasti stabil', 'Tidak berubah sama sekali', 'Selalu menguntungkan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Untuk mahasiswa, inflasi paling terasa pada?', 'opsi' => ['Harga kebutuhan harian seperti makan dan transportasi', 'Warna aplikasi', 'Jadwal ujian', 'Jumlah teman'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Respons awal yang tepat saat inflasi naik adalah?', 'opsi' => ['Meninjau ulang anggaran', 'Panik membeli semua barang', 'Menghapus catatan keuangan', 'Menambah cicilan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Menyimpan uang tanpa rencana saat inflasi tinggi berisiko karena?', 'opsi' => ['Nilai riil uang melemah', 'Saldo pasti hilang', 'Uang menjadi ilegal', 'Harga otomatis turun'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Jurnal mengaitkan inflasi dengan peningkatan?', 'opsi' => ['Volatilitas pasar', 'Kepastian laba', 'Stabilitas total', 'Tidak ada perubahan risiko'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Apa yang sebaiknya dibandingkan secara berkala?', 'opsi' => ['Harga kebutuhan utama', 'Jumlah iklan', 'Tema aplikasi', 'Jumlah login'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pengeluaran mana yang perlu dicari peluang efisiensinya?', 'opsi' => ['Pos yang tidak prioritas', 'Biaya hidup pokok dulu', 'Dana darurat', 'Tagihan wajib langsung'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Inflasi tidak bisa dikendalikan individu, tetapi dampaknya bisa dikurangi dengan?', 'opsi' => ['Anggaran fleksibel dan penyimpanan dana yang terarah', 'Belanja lebih banyak', 'Menghindari semua tabungan', 'Meminjam dana'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Dalam modul ini, inflasi bukan hanya isu harga, tetapi juga?', 'opsi' => ['Isu keputusan menyimpan dan menempatkan dana', 'Isu warna uang', 'Isu desain dompet', 'Isu media sosial'], 'jawaban_benar' => 1],
        ];
    }

    private static function questionsEmergencyFund(): array
    {
        return [
            ['pertanyaan' => 'Jurnal menekankan bahwa dana darurat bukan hanya soal nominal, tetapi juga?', 'opsi' => ['Soal prioritas dan kesadaran risiko', 'Soal gaya hidup', 'Soal diskon', 'Soal popularitas'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Fungsi utama dana darurat adalah?', 'opsi' => ['Menahan guncangan biaya mendadak agar tidak langsung berutang', 'Menambah belanja rutin', 'Mempercepat konsumsi', 'Membeli aset berisiko'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Mahasiswa sering salah paham soal dana darurat karena?', 'opsi' => ['Mengira nominal kecil tidak berguna', 'Merasa biaya hidup selalu pasti', 'Selalu punya penghasilan besar', 'Harga tidak pernah berubah'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Dana darurat kecil tetapi konsisten dianggap?', 'opsi' => ['Lebih baik daripada tidak punya perlindungan', 'Tidak ada manfaatnya', 'Harus dipakai belanja', 'Sama dengan utang'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Tabungan darurat sebaiknya disimpan di tempat yang?', 'opsi' => ['Mudah dicairkan', 'Sulit diakses total', 'Paling spekulatif', 'Tanpa catatan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Dana darurat tidak seharusnya dipakai untuk?', 'opsi' => ['Belanja spontan', 'Biaya darurat mendesak', 'Pengeluaran tak terduga', 'Krisis kecil'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Target awal yang realistis menurut modul adalah?', 'opsi' => ['Satu bulan pengeluaran pokok', 'Sepuluh tahun biaya hidup', 'Semua uang saku sekaligus', 'Tidak perlu target'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pengetahuan finansial dalam jurnal ini berperan sebagai?', 'opsi' => ['Faktor penyangga kemampuan menabung darurat', 'Jaminan kaya instan', 'Penghapus semua risiko', 'Pengganti penghasilan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Dana darurat membantu mencegah biaya mendadak berubah menjadi?', 'opsi' => ['Utang atau pengorbanan biaya utama', 'Tabungan tambahan', 'Investasi jangka panjang', 'Hadiah'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pesan inti modul ini adalah?', 'opsi' => ['Cadangan kecil yang terjaga lebih penting daripada menunggu sempurna', 'Dana darurat tidak perlu bagi mahasiswa', 'Utang lebih praktis', 'Belanja lebih dulu baru menabung'], 'jawaban_benar' => 1],
        ];
    }

    private static function questionsSecurity(): array
    {
        return [
            ['pertanyaan' => 'Menurut jurnal, keberlanjutan penggunaan pembayaran digital dipengaruhi oleh?', 'opsi' => ['Rasa aman dan rasa kontrol pengguna', 'Warna aplikasi saja', 'Jumlah promo semata', 'Kecepatan internet saja'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Keamanan digital dalam modul ini tidak hanya soal sistem, tetapi juga?', 'opsi' => ['Pengalaman pengguna saat bertransaksi', 'Jumlah followers aplikasi', 'Tampilan iklan', 'Ukuran layar'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Jika pengguna tidak paham alur transaksi, dampaknya adalah?', 'opsi' => ['Kepercayaan terhadap layanan turun', 'Saldo bertambah', 'Risiko hilang', 'Akun otomatis aman'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'OTP dan PIN sebaiknya?', 'opsi' => ['Dijaga dan tidak dibagikan', 'Dikirim ke teman dekat', 'Disimpan di chat terbuka', 'Diposting saat ada masalah'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Riwayat transaksi penting karena?', 'opsi' => ['Membantu mendeteksi transaksi asing dan kesalahan', 'Agar bisa menghapus semua bukti', 'Tidak ada fungsi', 'Hanya hiasan aplikasi'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Fitur yang memperkuat keamanan pengguna adalah?', 'opsi' => ['Verifikasi tambahan', 'Login publik', 'Auto share kode', 'PIN bersama'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Membaca detail merchant sebelum bayar membantu?', 'opsi' => ['Mengurangi salah transaksi', 'Menambah cashback otomatis', 'Menghapus semua biaya', 'Membuat akun publik'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Jika ada transaksi asing, langkah awal yang tepat adalah?', 'opsi' => ['Ubah kredensial dan hubungi kanal resmi', 'Diamkan saja', 'Sebarkan OTP', 'Isi saldo lagi'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Desain antarmuka ikut penting karena?', 'opsi' => ['Membantu pengguna membaca tanda peringatan', 'Membuat risiko hilang total', 'Menggantikan keamanan sistem', 'Tidak ada kaitan dengan keamanan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Semakin tinggi rasa kontrol pengguna, maka?', 'opsi' => ['Semakin kecil peluang kesalahan finansial digital', 'Semakin besar risiko lupa', 'Semakin tidak perlu keamanan', 'Semakin sering salah transfer'], 'jawaban_benar' => 1],
        ];
    }
    private static function questionsEwallet(): array
    {
        return [
            ['pertanyaan' => 'Jurnal tentang pembayaran digital menekankan pentingnya?', 'opsi' => ['Kepercayaan, kemudahan pakai, dan manfaat nyata', 'Logo yang menarik saja', 'Promo setiap jam', 'Transaksi tanpa riwayat'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Adopsi e-wallet yang sehat seharusnya dibarengi dengan?', 'opsi' => ['Batas saldo dan evaluasi penggunaan', 'Saldo tanpa batas', 'Belanja spontan', 'Utang tambahan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Mahasiswa sering keliru karena?', 'opsi' => ['Cepat mengadopsi teknologi tanpa evaluasi risiko', 'Terlalu sedikit aplikasi', 'Tidak punya ponsel', 'Tidak pernah melihat promo'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Dompet digital paling bermanfaat jika dipakai untuk?', 'opsi' => ['Transaksi yang sudah direncanakan', 'Semua pembelian impulsif', 'Judi online', 'Utang harian'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Memisahkan fungsi e-wallet berguna untuk?', 'opsi' => ['Menjaga kontrol antara kebutuhan dan hiburan', 'Membuat belanja makin bebas', 'Menghilangkan catatan transaksi', 'Menghapus risiko'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Riwayat pembayaran sebaiknya?', 'opsi' => ['Dicek rutin', 'Diabaikan', 'Dihapus sebelum dibaca', 'Dibagikan ke publik'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Promosi yang terlalu agresif sebaiknya?', 'opsi' => ['Dibatasi atau dimatikan', 'Diikuti semuanya', 'Dijadikan alasan utama belanja', 'Dibiarkan mengontrol keputusan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Isi saldo e-wallet yang sehat adalah?', 'opsi' => ['Sesuai rencana mingguan', 'Sebanyak mungkin sekaligus', 'Dari pinjaman konsumtif', 'Tanpa batas harian'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Tujuan batas saldo adalah?', 'opsi' => ['Menjaga efisiensi tanpa kehilangan kontrol', 'Membuat pengguna selalu kehabisan uang', 'Menambah risiko', 'Menghilangkan keamanan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pesan inti modul ini adalah?', 'opsi' => ['Kemudahan digital harus dibarengi disiplin penggunaan', 'Teknologi otomatis membuat pengguna hemat', 'Promo lebih penting dari tujuan', 'Saldo besar selalu aman'], 'jawaban_benar' => 1],
        ];
    }

    private static function questionsBudget(): array
    {
        return [
            ['pertanyaan' => 'Jurnal ini mengaitkan literasi keuangan dengan?', 'opsi' => ['Perilaku finansial dan perhatian terbatas', 'Warna dompet', 'Ukuran saldo semata', 'Kecepatan internet'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Masalah utama banyak orang dalam anggaran adalah?', 'opsi' => ['Kurang memberi perhatian pada uang yang keluar', 'Tidak punya aplikasi', 'Tidak pernah menerima uang', 'Harga selalu tetap'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pembelian kecil berulang menjadi masalah karena?', 'opsi' => ['Sering lolos dari perhatian pengguna', 'Selalu gratis', 'Tidak masuk catatan bank', 'Tidak memengaruhi saldo'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Anggaran bulanan efektif jika?', 'opsi' => ['Disertai kebiasaan memantau secara rutin', 'Dibuat sekali lalu dilupakan', 'Hanya fokus pada hiburan', 'Tanpa kategori'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Kategori sederhana yang disarankan adalah?', 'opsi' => ['Kebutuhan pokok, akademik, tabungan, hiburan', 'Hanya hiburan', 'Hanya transportasi', 'Hanya investasi'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Kapan anggaran sebaiknya ditinjau?', 'opsi' => ['Minimal mingguan', 'Hanya tiap lima tahun', 'Saat saldo nol saja', 'Tidak perlu ditinjau'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Perhatian terbatas dapat menyebabkan?', 'opsi' => ['Kebocoran anggaran', 'Tabungan bertambah otomatis', 'Harga turun', 'Risiko hilang'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Mahasiswa sering tahu teori anggaran tetapi gagal karena?', 'opsi' => ['Kurang konsisten mengawasi realisasi pengeluaran', 'Selalu punya pendapatan besar', 'Biaya hidup selalu nol', 'Tidak butuh prioritas'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Semakin sering pengeluaran ditinjau, maka?', 'opsi' => ['Semakin kecil peluang anggaran bocor tanpa sadar', 'Semakin besar risiko belanja', 'Semakin tidak perlu tabungan', 'Semakin sulit mengontrol uang'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Modul ini menekankan bahwa pengetahuan saja?', 'opsi' => ['Tidak cukup tanpa perhatian dan kebiasaan memantau', 'Sudah cukup untuk semua situasi', 'Menghapus semua kesalahan', 'Lebih penting dari perilaku'], 'jawaban_benar' => 1],
        ];
    }

    private static function questionsSaving(): array
    {
        return [
            ['pertanyaan' => 'Jurnal ini menekankan tabungan kuat terbentuk saat pengguna punya?', 'opsi' => ['Tujuan jangka panjang dan safety net', 'Banyak promo belanja', 'Utang rutin', 'Beban cicilan konsumtif'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Kebiasaan menabung tidak cukup dibangun dari?', 'opsi' => ['Niat sesaat saja', 'Tujuan konkret', 'Akun terpisah', 'Jadwal rutin'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Mahasiswa lebih mudah menabung jika?', 'opsi' => ['Tujuannya diberi nama dan jelas', 'Tabungan bercampur dengan uang harian', 'Semua uang dibiarkan di satu akun', 'Tidak ada target'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Memisahkan akun tabungan penting karena?', 'opsi' => ['Mengurangi campur antara uang masa depan dan konsumsi hari ini', 'Agar lebih cepat dibelanjakan', 'Agar saldo tidak terlihat', 'Agar tidak perlu tujuan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Contoh tujuan tabungan yang baik adalah?', 'opsi' => ['Biaya sertifikasi atau dana magang', 'Semua promo bulanan', 'Belanja impulsif', 'Jajan tanpa batas'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Tanggal tetap menabung berguna untuk?', 'opsi' => ['Membentuk kebiasaan konsisten', 'Menghapus kebutuhan prioritas', 'Menambah belanja spontan', 'Menunda terus tabungan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Dalam modul ini, bunga majemuk dipahami sebagai?', 'opsi' => ['Manfaat waktu atas kebiasaan menabung yang konsisten', 'Diskon belanja musiman', 'Biaya administrasi', 'Potongan cicilan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Tabungan yang paling sulit dipertahankan adalah saat?', 'opsi' => ['Tidak ada tujuan yang jelas', 'Ada target yang spesifik', 'Ada akun terpisah', 'Ada evaluasi rutin'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Jurnal ini mendukung ide bahwa tabungan?', 'opsi' => ['Perlu struktur mental dan tujuan, bukan sekadar niat', 'Harus menunggu uang besar', 'Tidak penting bagi mahasiswa', 'Boleh bercampur dengan semua pengeluaran'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pesan inti modul ini adalah?', 'opsi' => ['Konsistensi kecil lebih kuat daripada menunda', 'Tabungan harus sempurna dulu', 'Belanja lebih dulu selalu benar', 'Tujuan jangka panjang tidak relevan'], 'jawaban_benar' => 1],
        ];
    }

    private static function questionsDebt(): array
    {
        return [
            ['pertanyaan' => 'BNPL pada jurnal ini dipandang berisiko karena?', 'opsi' => ['Dapat mendorong konsumsi berlebih tanpa perhitungan', 'Selalu bebas biaya', 'Tidak pernah menambah beban', 'Hanya dipakai bisnis besar'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Cicilan kecil terasa ringan, tetapi masalahnya?', 'opsi' => ['Total bebannya bisa tersembunyi', 'Selalu gratis', 'Tidak perlu dibayar', 'Tidak memengaruhi keuangan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Utang digital yang paling berbahaya bagi mahasiswa sering datang dalam bentuk?', 'opsi' => ['Layanan yang terlihat cepat dan ramah', 'Tagihan listrik', 'Uang saku orang tua', 'Tabungan tetap'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Utang konsumtif sebaiknya dihindari untuk?', 'opsi' => ['Gaya hidup dan promo sesaat', 'Kebutuhan mendesak yang terukur', 'Biaya penting yang direncanakan', 'Kondisi darurat valid'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Sebelum berutang, pengguna harus menghitung dampaknya pada?', 'opsi' => ['Biaya makan, transportasi, dan akademik', 'Warna aplikasi', 'Jumlah notifikasi', 'Kuota media sosial'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Indikator bahwa utang belum layak diambil adalah?', 'opsi' => ['Tidak bisa menjelaskan alasan dan sumber pembayarannya', 'Ada iklan menarik', 'Bisa checkout cepat', 'Teman juga memakai'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Fokus evaluasi utang seharusnya pada?', 'opsi' => ['Total cicilan', 'Nominal per hari saja', 'Tampilan promo', 'Jumlah pengguna'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Jurnal menghubungkan BNPL dengan?', 'opsi' => ['Perilaku belanja generasi muda dan risiko kredit', 'Penghapusan semua biaya', 'Pendapatan otomatis meningkat', 'Keamanan data tanpa batas'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Mahasiswa sebaiknya mengambil utang hanya jika?', 'opsi' => ['Manfaat dan kemampuan bayar jelas', 'Promo sedang besar', 'Teman mengajak', 'Bisa dibayar nanti tanpa hitung'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pesan inti modul ini adalah?', 'opsi' => ['Utang nyaman di awal bisa berat di akhir', 'Semua cicilan aman', 'Utang selalu meningkatkan kesejahteraan', 'Risiko utang hanya mitos'], 'jawaban_benar' => 1],
        ];
    }

    private static function questionsDigitalLiteracy(): array
    {
        return [
            ['pertanyaan' => 'Jurnal ini menekankan bahwa literasi keuangan perlu didampingi oleh?', 'opsi' => ['Kontrol diri', 'Promo besar', 'Teknologi baru', 'Aset mahal'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pengetahuan finansial saja tidak cukup jika?', 'opsi' => ['Kontrol diri lemah', 'Pendapatan tinggi', 'Akun banyak', 'Ponsel baru'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Dalam konteks digital, distraksi dapat datang dari?', 'opsi' => ['Notifikasi promosi dan kemudahan checkout', 'Catatan transaksi', 'Tujuan tabungan', 'Evaluasi mingguan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Riwayat transaksi seharusnya dipakai untuk?', 'opsi' => ['Evaluasi pola keuangan', 'Diabaikan total', 'Dihapus sebelum dibaca', 'Dibagikan ke publik'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Semakin sadar pengguna pada pola digitalnya, maka?', 'opsi' => ['Semakin mudah mengendalikan keputusan finansial berikutnya', 'Semakin sulit menghemat', 'Semakin perlu berutang', 'Semakin tidak perlu anggaran'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Jejak transaksi penting karena?', 'opsi' => ['Menunjukkan kebiasaan yang bisa diperbaiki', 'Tidak punya fungsi', 'Hanya arsip formal', 'Tidak berhubungan dengan perilaku'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'FOMO dan promosi digital dapat meningkatkan?', 'opsi' => ['Keputusan finansial yang buruk bila kontrol diri lemah', 'Ketahanan finansial otomatis', 'Dana darurat', 'Disiplin anggaran'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Membatasi izin aplikasi berguna untuk?', 'opsi' => ['Mengurangi eksposur dan distraksi yang tidak perlu', 'Menambah promosi', 'Mempercepat belanja impulsif', 'Menghilangkan histori transaksi'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Modul ini melihat literasi digital finansial sebagai?', 'opsi' => ['Gabungan pemahaman data dan pengendalian diri', 'Hanya kemampuan install aplikasi', 'Hanya kemampuan top up', 'Hanya kemampuan transfer'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pesan inti modul ini adalah?', 'opsi' => ['Teknologi harus diiringi kontrol diri dan evaluasi perilaku', 'Aplikasi otomatis membuat pengguna rasional', 'Riwayat transaksi tidak penting', 'Semua distraksi digital aman'], 'jawaban_benar' => 1],
        ];
    }

    private static function questionsDiversification(): array
    {
        return [
            ['pertanyaan' => 'Jurnal ini mengkritik anggapan bahwa diversifikasi?', 'opsi' => ['Selalu otomatis aman tanpa batas', 'Selalu salah', 'Tidak pernah berguna', 'Hanya untuk bank'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Manfaat diversifikasi bergantung pada?', 'opsi' => ['Pengetahuan, struktur aset, dan kondisi ketidakpastian', 'Warna aplikasi', 'Jumlah iklan', 'Kecepatan internet'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Memiliki banyak aset tanpa paham isinya berarti?', 'opsi' => ['Belum tentu menurunkan risiko', 'Pasti aman', 'Pasti untung', 'Pasti lebih baik dari analisis'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Bagi mahasiswa pemula, langkah yang lebih sehat adalah?', 'opsi' => ['Portofolio sederhana yang bisa dijelaskan', 'Membeli semua aset populer', 'Masuk ke semua platform', 'Menghapus evaluasi'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Manajemen risiko berarti?', 'opsi' => ['Memahami fungsi tiap aset', 'Memperbanyak aplikasi', 'Mengejar aset viral', 'Menyamakan semua instrumen'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pertanyaan penting untuk tiap aset adalah?', 'opsi' => ['Mengapa aset ini ada dan apa perannya', 'Apakah temannya membeli', 'Apakah logonya menarik', 'Apakah iklannya sering muncul'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Diversifikasi yang buruk biasanya menghasilkan?', 'opsi' => ['Kebingungan tanpa pemahaman risiko', 'Pengurangan risiko pasti', 'Tujuan lebih jelas', 'Portofolio lebih sederhana'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Satu aset populer sebaiknya tidak menampung?', 'opsi' => ['Seluruh dana pengguna', 'Sebagian kecil dana yang terukur', 'Dana untuk eksperimen kecil', 'Dana dengan tujuan jelas'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Modul ini menekankan bahwa diversifikasi bukan?', 'opsi' => ['Pengganti analisis', 'Bagian dari manajemen risiko', 'Strategi yang berguna', 'Alat pembelajaran'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pesan inti modul ini adalah?', 'opsi' => ['Sebar dana dengan alasan yang jelas, bukan asal banyak', 'Semakin banyak aplikasi semakin aman', 'Diversifikasi menghapus semua risiko', 'Satu aset populer selalu cukup'], 'jawaban_benar' => 1],
        ];
    }

    private static function questionsFraud(): array
    {
        return [
            ['pertanyaan' => 'Faktor utama yang membuat orang lebih rentan tertipu menurut jurnal adalah?', 'opsi' => ['Tekanan waktu', 'Warna situs', 'Ukuran ponsel', 'Jumlah kontak'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Saat merasa harus bertindak cepat, kemampuan pengguna untuk?', 'opsi' => ['Memverifikasi informasi cenderung melemah', 'Menjadi lebih akurat', 'Selalu meningkat', 'Menjadi kebal penipuan'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Penipu sering memanfaatkan emosi seperti?', 'opsi' => ['Takut dan terburu-buru', 'Tenang dan sabar', 'Fokus akademik', 'Rasa aman berlebih'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Contoh modus yang memanfaatkan tekanan waktu adalah?', 'opsi' => ['Ancaman pemblokiran akun', 'Laporan resmi yang memberi waktu cek', 'Email informasi biasa', 'Bukti pembayaran pribadi'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Langkah pertama jika pesan terasa mendesak adalah?', 'opsi' => ['Berhenti dan verifikasi', 'Segera klik', 'Bagikan OTP', 'Transfer dulu baru cek'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Mahasiswa rentan karena sering menjadi pengguna aktif?', 'opsi' => ['Layanan digital dan pesan instan', 'Hanya buku cetak', 'Hanya surat pos', 'Tidak memakai internet'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'OTP, PIN, dan password seharusnya?', 'opsi' => ['Tidak pernah dibagikan', 'Diberi ke admin palsu', 'Dikirim jika panik', 'Dibagikan saat promo'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Keputusan cepat dalam keamanan finansial digital sering?', 'opsi' => ['Lebih berbahaya daripada menunggu verifikasi', 'Selalu tepat', 'Lebih aman dari cek ulang', 'Tidak ada pengaruhnya'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Kanal verifikasi yang benar adalah?', 'opsi' => ['Kanal resmi layanan', 'Pesan dari nomor acak', 'Kolom komentar publik', 'Akun baru tanpa identitas'], 'jawaban_benar' => 1],
            ['pertanyaan' => 'Pesan inti modul ini adalah?', 'opsi' => ['Jeda beberapa menit lebih aman daripada reaksi panik', 'Cepat selalu lebih baik', 'Semua pesan mendesak valid', 'Penipuan hanya soal teknologi'], 'jawaban_benar' => 1],
        ];
    }

    private static function prepareQuestionnaire(array $questions, string $moduleTitle): array
    {
        $baseQuestions = array_values($questions);
        $prepared = [];

        foreach ($baseQuestions as $index => $question) {
            $prepared[] = self::normalizeQuestion($question, $moduleTitle, $index);
        }

        foreach ($baseQuestions as $index => $question) {
            $prepared[] = self::normalizeQuestion(
                self::createScenarioVariant($question, $index),
                $moduleTitle,
                $index + count($baseQuestions)
            );
        }

        return array_map(
            fn (array $question, int $index) => [
                ...$question,
                'nomor_soal' => $index + 1,
            ],
            $prepared,
            array_keys($prepared)
        );
    }

    private static function normalizeQuestion(array $question, string $moduleTitle, int $seed): array
    {
        $options = array_values($question['opsi'] ?? []);
        $totalOptions = count($options);
        $correctIndex = max(0, min($totalOptions - 1, ((int) ($question['jawaban_benar'] ?? 1)) - 1));
        $shift = $totalOptions > 0 ? $seed % $totalOptions : 0;
        $rotatedOptions = $totalOptions > 0
            ? array_merge(array_slice($options, $shift), array_slice($options, 0, $shift))
            : [];
        $newCorrectIndex = $totalOptions > 0
            ? (($correctIndex - $shift + $totalOptions) % $totalOptions) + 1
            : 1;
        $correctText = $rotatedOptions[$newCorrectIndex - 1] ?? '';
        $focus = self::moduleFocus($moduleTitle);

        return [
            'pertanyaan' => trim((string) ($question['pertanyaan'] ?? '')),
            'opsi' => $rotatedOptions,
            'jawaban_benar' => $newCorrectIndex,
            'penjelasan_benar' => $question['penjelasan_benar']
                ?? sprintf(
                    'Jawaban yang benar adalah "%s" karena %s.',
                    $correctText,
                    $focus
                ),
            'penjelasan_salah' => $question['penjelasan_salah']
                ?? sprintf(
                    'Pilihan lain kurang tepat karena belum mencerminkan bahwa %s.',
                    $focus
                ),
        ];
    }

    private static function createScenarioVariant(array $question, int $index): array
    {
        $templates = [
            '%s Dalam praktiknya, pilihan yang paling tepat adalah?',
            '%s Jika dihadapkan pada situasi serupa, jawaban terbaik adalah?',
            '%s Saat diterapkan dalam kehidupan sehari-hari, pilihan yang paling sesuai adalah?',
            '%s Untuk keputusan nyata, langkah yang paling tepat adalah?',
            '%s Dalam contoh penerapan langsung, jawaban yang paling tepat adalah?',
        ];

        $baseQuestion = rtrim((string) ($question['pertanyaan'] ?? ''), " \t\n\r\0\x0B?");
        $template = $templates[$index % count($templates)];

        return [
            ...$question,
            'pertanyaan' => sprintf($template, $baseQuestion),
        ];
    }

    private static function moduleFocus(string $moduleTitle): string
    {
        return [
            'Dasar Investasi untuk Mahasiswa' => 'investasi yang sehat dimulai dari tujuan yang jelas, pengukuran risiko, dan disiplin evaluasi',
            'Memahami Inflasi dan Daya Beli' => 'inflasi harus direspons dengan penyesuaian anggaran dan penjagaan daya beli',
            'Dana Darurat dan Ketahanan Finansial' => 'cadangan dana perlu dijaga agar kebutuhan mendadak tidak berubah menjadi utang',
            'Keamanan Digital dalam Transaksi Keuangan' => 'keamanan transaksi bergantung pada kewaspadaan, verifikasi, dan kontrol pengguna',
            'E-Wallet, QRIS, dan Kebiasaan Aman' => 'kemudahan pembayaran digital tetap harus dibarengi batas penggunaan dan kontrol diri',
            'Anggaran Bulanan dan Prioritas Pengeluaran' => 'anggaran yang sehat membutuhkan prioritas yang jelas dan kebiasaan memantau pengeluaran',
            'Menabung Konsisten dan Bunga Majemuk' => 'tabungan tumbuh dari tujuan yang jelas, konsistensi, dan kebiasaan yang teratur',
            'Pinjaman Online, Bunga, dan Risiko Utang' => 'utang hanya layak diambil jika manfaat, total beban, dan kemampuan bayar benar-benar jelas',
            'Literasi Digital dan Jejak Finansial' => 'teknologi keuangan harus dipakai dengan kesadaran, kontrol diri, dan evaluasi jejak transaksi',
            'Diversifikasi dan Manajemen Risiko' => 'diversifikasi harus dilakukan dengan alasan yang jelas, bukan sekadar ikut tren',
            'Mengenali Penipuan Finansial Online' => 'kewaspadaan, jeda sebelum bertindak, dan verifikasi resmi adalah pertahanan utama dari penipuan',
        ][$moduleTitle] ?? 'jawaban harus sejalan dengan inti materi modul';
    }

    private static function sharedSources(): array
    {
        return [
            [
                'judul' => 'Mapping Financial Literacy: A Systematic Literature Review of Determinants and Recent Trends',
                'penulis' => 'Azra Zaimovic dkk.',
                'jurnal' => 'Sustainability (2023)',
                'url' => 'https://www.mdpi.com/2071-1050/15/12/9358',
            ],
            [
                'judul' => 'Inflation and Risky Investments',
                'penulis' => 'Hannu Laurila dan Jukka Ilomaki',
                'jurnal' => 'Journal of Risk and Financial Management (2020)',
                'url' => 'https://www.mdpi.com/1911-8074/13/12/329',
            ],
            [
                'judul' => 'The Effect of Student Loan Debt on Emergency Savings and the Moderating Role of Financial Knowledge',
                'penulis' => 'Thomas Korankye dkk.',
                'jurnal' => 'Journal of Risk and Financial Management (2024)',
                'url' => 'https://www.mdpi.com/1911-8074/17/9/420',
            ],
            [
                'judul' => "The Role of Consumers' Perceived Security, Perceived Control, Interface Design Features, and Conscientiousness in Continuous Use of Mobile Payment Services",
                'penulis' => 'Jiaxin Zhang dkk.',
                'jurnal' => 'Sustainability (2019)',
                'url' => 'https://www.mdpi.com/2071-1050/11/23/6843',
            ],
            [
                'judul' => 'Understanding Consumer Acceptance for Blockchain-Based Digital Payment Systems in Bhutan',
                'penulis' => 'Tenzin Norbu dkk.',
                'jurnal' => 'Future Internet (2025)',
                'url' => 'https://www.mdpi.com/1999-5903/17/4/134',
            ],
            [
                'judul' => 'Does Financial Literacy Affect Household Financial Behavior? The Role of Limited Attention',
                'penulis' => 'Shulin Xu dkk.',
                'jurnal' => 'Frontiers in Psychology (2022)',
                'url' => 'https://www.frontiersin.org/journals/psychology/articles/10.3389/fpsyg.2022.906153/full',
            ],
            [
                'judul' => 'The Interplay of Financial Safety Nets, Long-Term Goals, and Saving Habits: A Moderated Mediation Study',
                'penulis' => 'Congrong Ouyang dkk.',
                'jurnal' => 'International Journal of Financial Studies (2025)',
                'url' => 'https://www.mdpi.com/2227-7072/13/1/47',
            ],
            [
                'judul' => 'Buy Now Pay Later - A Fad or a Reality? A Perspective on Electronic Commerce',
                'penulis' => 'Dana Adriana Lupsa-Tataru dkk.',
                'jurnal' => 'Economies (2023)',
                'url' => 'https://www.mdpi.com/2227-7099/11/8/218',
            ],
            [
                'judul' => 'Examining the Impact of Financial Literacy, Financial Self-Control, and Demographic Determinants on Individual Financial Performance and Behavior',
                'penulis' => 'Jeanne Laure Mawad dkk.',
                'jurnal' => 'Sustainability (2022)',
                'url' => 'https://www.mdpi.com/2071-1050/14/22/15129',
            ],
            [
                'judul' => 'The impact of time pressure and type of fraud on susceptibility to online fraud',
                'penulis' => 'Ce Lyu dkk.',
                'jurnal' => 'Frontiers in Psychology (2025)',
                'url' => 'https://www.frontiersin.org/journals/psychology/articles/10.3389/fpsyg.2025.1508363/full',
            ],
        ];
    }
}
