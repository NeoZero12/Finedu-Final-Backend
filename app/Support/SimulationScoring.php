<?php

namespace App\Support;

use App\Models\Simulasi;

class SimulationScoring
{
    public static function calculate(Simulasi $simulasi): array
    {
        $simulasi->loadMissing('transaksi');

        $totalOverspending = (float) $simulasi->transaksi
            ->where('pembelian_impulsif', true)
            ->sum('nominal');

        $anggaranAwal = (float) $simulasi->anggaran_awal;
        $anggaranSisa = (float) $simulasi->anggaran_sisa;
        $scoreBase = $anggaranAwal > 0
            ? (($anggaranSisa / $anggaranAwal) * 60) + 40
            : ($anggaranSisa < 0 ? 0 : 40);

        return [
            'skor' => max(0, min(100, (int) round($scoreBase - ($totalOverspending > 0 ? 10 : 0)))),
            'total_overspending' => $totalOverspending,
        ];
    }
}
