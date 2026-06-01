<?php

namespace App\Support;

use App\Models\DigitalNudge;
use App\Models\Profil;
use App\Models\Simulasi;

class SimulationSummary
{
    private const DISPLAY_TIMEZONE = 'Asia/Jakarta';

    public static function format(Simulasi $simulasi, ?Profil $profil): array
    {
        $simulasi->loadMissing('transaksi.produk');

        $transaksi = $simulasi->transaksi;
        $totalPemasukan = (float) $transaksi
            ->where('arah_transaksi', 'pemasukan')
            ->sum('nominal');
        $totalPengeluaran = (float) $transaksi
            ->where('arah_transaksi', '!=', 'pemasukan')
            ->sum('nominal');

        $anggaranAwal = (float) $simulasi->anggaran_awal;
        $anggaranSisa = (float) $simulasi->anggaran_sisa;
        $hasBudget = $anggaranAwal > 0;
        $persentaseTerpakai = $anggaranAwal > 0
            ? ($totalPengeluaran / $anggaranAwal) * 100
            : ($totalPengeluaran > 0 ? 100 : 0);
        $persentaseSisa = $anggaranAwal > 0
            ? ($anggaranSisa / $anggaranAwal) * 100
            : 0;
        $persentaseOverbudget = $anggaranSisa < 0
            ? ($anggaranAwal > 0 ? (abs($anggaranSisa) / $anggaranAwal) * 100 : 0)
            : 0;
        $persentaseHemat = $anggaranAwal > 0 && $anggaranSisa > 0
            ? ($anggaranSisa / $anggaranAwal) * 100
            : 0;

        [$budgetIndicator, $nudgeType, $nudgeMessage, $nudgeStage] = self::resolveNudge(
            $hasBudget,
            $persentaseTerpakai,
            $totalPengeluaran,
            $persentaseSisa,
            $persentaseOverbudget,
            $persentaseHemat
        );

        $nudgeAktif = $nudgeType !== null;
        if ($nudgeAktif && $nudgeType) {
            DigitalNudge::firstOrCreate([
                'user_id' => $simulasi->user_id,
                'tipe_nudge' => $nudgeType,
                'diabaikan' => false,
            ]);
        }

        $dimulaiPada = $simulasi->created_at?->copy()->timezone(self::DISPLAY_TIMEZONE);
        $diselesaikanPada = $simulasi->status === 'selesai'
            ? $simulasi->updated_at?->copy()->timezone(self::DISPLAY_TIMEZONE)
            : null;

        return [
            ...$simulasi->toArray(),
            'transaksi' => $transaksi
                ->map(function ($item) {
                    $arahTransaksi = $item->arah_transaksi ?: 'pengeluaran';
                    $kategoriLabel = $item->kategori_label
                        ?: $item->produk?->kategori
                        ?: ($arahTransaksi === 'pemasukan' ? 'pemasukan' : 'lainnya');

                    return [
                        ...$item->toArray(),
                        'nama_item' => $item->nama_item ?: $item->produk?->nama_produk ?: 'Transaksi',
                        'kategori_label' => $kategoriLabel,
                        'arah_transaksi' => $arahTransaksi,
                        'catatan' => $item->catatan,
                        'sumber_transaksi' => $item->produk_id ? 'produk' : 'manual',
                    ];
                })
                ->values()
                ->all(),
            'jumlah_transaksi' => $transaksi->count(),
            'dimulai_pada' => $dimulaiPada?->toIso8601String(),
            'diselesaikan_pada' => $diselesaikanPada?->toIso8601String(),
            'total_pemasukan' => round($totalPemasukan, 2),
            'total_pengeluaran' => round($totalPengeluaran, 2),
            'persentase_terpakai' => round($persentaseTerpakai, 1),
            'persentase_sisa' => round($persentaseSisa, 1),
            'persentase_overbudget' => round($persentaseOverbudget, 1),
            'persentase_hemat' => round($persentaseHemat, 1),
            'budget_indicator' => $budgetIndicator,
            'nudge' => [
                'aktif' => $nudgeAktif,
                'tipe' => $nudgeAktif ? $nudgeType : null,
                'pesan' => $nudgeAktif ? $nudgeMessage : null,
                'tahap' => $nudgeAktif ? $nudgeStage : null,
                'persentase_overbudget' => round($persentaseOverbudget, 1),
                'persentase_hemat' => round($persentaseHemat, 1),
            ],
        ];
    }

    private static function resolveNudge(
        bool $hasBudget,
        float $persentaseTerpakai,
        float $totalPengeluaran,
        float $persentaseSisa,
        float $persentaseOverbudget,
        float $persentaseHemat
    ): array
    {
        if (! $hasBudget && $totalPengeluaran > 0) {
            return [
                'overspending-2',
                'warna_merah',
                'Saldo sudah berkurang, tetapi budget belum pernah ditambahkan. Tambahkan saldo terlebih dahulu agar simulasi kembali akurat.',
                2,
            ];
        }

        if ($persentaseOverbudget >= 25) {
            return [
                'overspending-4',
                'notifikasi',
                sprintf(
                    'Peringatan tahap 4: pengeluaran Anda sudah melewati budget sebesar %.1f%%. Hentikan belanja baru dan prioritaskan kebutuhan yang paling penting.',
                    $persentaseOverbudget
                ),
                4,
            ];
        }

        if ($persentaseOverbudget >= 10) {
            return [
                'overspending-3',
                'notifikasi',
                sprintf(
                    'Peringatan tahap 3: Anda sudah overbudget %.1f%%. Kurangi belanja non-prioritas agar kondisi keuangan tidak makin berat.',
                    $persentaseOverbudget
                ),
                3,
            ];
        }

        if ($persentaseOverbudget > 0) {
            return [
                'overspending-2',
                'warna_merah',
                sprintf(
                    'Peringatan tahap 2: saldo Anda sudah minus dan overbudget %.1f%%. Tahan pengeluaran berikutnya sampai anggaran kembali aman.',
                    $persentaseOverbudget
                ),
                2,
            ];
        }

        if ($persentaseTerpakai >= 90) {
            return [
                'overspending-1',
                'warna_merah',
                sprintf(
                    'Peringatan tahap 1: %.1f%% budget sudah terpakai. Sisa anggaran Anda tinggal %.1f%%, jadi pilih belanja berikutnya dengan lebih hati-hati.',
                    $persentaseTerpakai,
                    max($persentaseSisa, 0)
                ),
                1,
            ];
        }

        if ($persentaseTerpakai >= 25) {
            return [
                'overspending-1',
                'goal_reminder',
                sprintf(
                    'Pengeluaran Anda sudah memakai %.1f%% budget. Pastikan transaksi berikutnya tetap sesuai prioritas.',
                    $persentaseTerpakai
                ),
                1,
            ];
        }

        if ($totalPengeluaran > 0) {
            return [
                'hemat',
                'goal_reminder',
                sprintf(
                    'Bagus, pengeluaran Anda masih hemat. Anda masih menyimpan %.1f%% dari budget dan pengeluaran tetap terkendali.',
                    $persentaseHemat
                ),
                0,
            ];
        }

        return ['awal', null, null, null];
    }
}
