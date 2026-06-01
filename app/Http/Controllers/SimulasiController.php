<?php

namespace App\Http\Controllers;

use App\Models\HasilSkor;
use App\Models\Profil;
use App\Models\Simulasi;
use App\Support\SimulationScoring;
use App\Support\SimulationSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class SimulasiController extends Controller
{
    private const REPORT_TIMEZONE = 'Asia/Jakarta';

    public function index(Request $request)
    {
        $simulasi = Simulasi::where('user_id', $request->user()->id)
            ->where('status', 'berlangsung')
            ->latest()
            ->first();

        if (!$simulasi) {
            $simulasi = Simulasi::where('user_id', $request->user()->id)
                ->latest()
                ->first();
        }

        if (!$simulasi) {
            return response()->json(['message' => 'Tidak ada simulasi aktif'], 404);
        }

        return response()->json(
            SimulationSummary::format($simulasi, $request->user()->profil)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'action' => ['nullable', Rule::in(['resume', 'restart'])],
        ]);

        $user = $request->user();
        $action = $validated['action'] ?? 'resume';
        $profil = Profil::where('user_id', $user->id)->first();
        $simulasiTerakhir = Simulasi::where('user_id', $user->id)
            ->latest()
            ->first();
        $simulasiAktif = Simulasi::where('user_id', $user->id)
            ->where('status', 'berlangsung')
            ->latest()
            ->first();

        if ($simulasiAktif && $action === 'resume') {
            return response()->json([
                'message' => 'Melanjutkan simulasi yang sudah ada',
                'data' => SimulationSummary::format($simulasiAktif, $profil),
            ]);
        }

        if ($simulasiAktif && $action === 'restart') {
            $simulasiAktif->update(['status' => 'selesai']);
        }

        $simulasi = Simulasi::create([
            'user_id' => $user->id,
            'anggaran_awal' => 0,
            'anggaran_sisa' => 0,
            'status' => 'berlangsung',
        ]);

        return response()->json([
            'message' => $action === 'restart'
                ? 'Sesi baru dimulai dari awal.'
                : ($simulasiTerakhir ? 'Sesi baru dimulai. Riwayat sesi sebelumnya tetap tersimpan di laporan.' : 'Simulasi dimulai!'),
            'data' => SimulationSummary::format($simulasi, $profil),
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $simulasi = Simulasi::with('transaksi.produk')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json(
            SimulationSummary::format($simulasi, $request->user()->profil)
        );
    }

    public function update(Request $request, $id)
    {
        $simulasi = Simulasi::where('user_id', $request->user()->id)->findOrFail($id);

        if ($simulasi->status !== 'berlangsung') {
            return response()->json([
                'message' => 'Sesi ini sudah selesai dan tersimpan di laporan.',
                'data' => SimulationSummary::format($simulasi->fresh(), $request->user()->profil),
            ]);
        }

        $simulasi->update(['status' => 'selesai']);

        $score = SimulationScoring::calculate($simulasi->fresh('transaksi'));

        HasilSkor::updateOrCreate([
            'user_id' => $simulasi->user_id,
        ], [
            'skor_literasi_akhir' => $score['skor'],
            'total_overspending' => $score['total_overspending'],
        ]);

        return response()->json([
            'message' => 'Simulasi telah diselesaikan.',
            'data' => SimulationSummary::format($simulasi->fresh(), $request->user()->profil),
        ]);
    }

    public function report(Request $request)
    {
        $profil = $request->user()->profil;
        $riwayat = Simulasi::with('transaksi.produk')
            ->where('user_id', $request->user()->id)
            ->where('status', 'selesai')
            ->latest('updated_at')
            ->get()
            ->map(fn (Simulasi $simulasi) => SimulationSummary::format($simulasi, $profil))
            ->values();

        $now = Carbon::now(self::REPORT_TIMEZONE);
        $mingguMulai = $now->copy()->startOfWeek();
        $mingguSelesai = $now->copy()->endOfWeek();
        $bulanMulai = $now->copy()->startOfMonth();
        $bulanSelesai = $now->copy()->endOfMonth();

        return response()->json([
            'ringkasan' => [
                'total_sesi' => $riwayat->count(),
                'minggu_ini' => $this->summarizePeriod($riwayat, 'Minggu ini', $mingguMulai, $mingguSelesai),
                'bulan_ini' => $this->summarizePeriod($riwayat, 'Bulan ini', $bulanMulai, $bulanSelesai),
                'keseluruhan' => $this->summarizeCollection($riwayat, 'Semua waktu', null, null),
            ],
            'riwayat' => $riwayat,
        ]);
    }

    private function summarizePeriod(Collection $riwayat, string $label, Carbon $mulai, Carbon $selesai): array
    {
        $sesiDalamPeriode = $riwayat->filter(function (array $item) use ($mulai, $selesai) {
            if (empty($item['diselesaikan_pada'])) {
                return false;
            }

            $tanggalSelesai = Carbon::parse($item['diselesaikan_pada'])->timezone(self::REPORT_TIMEZONE);

            return $tanggalSelesai->betweenIncluded($mulai, $selesai);
        })->values();

        return $this->summarizeCollection($sesiDalamPeriode, $label, $mulai, $selesai);
    }

    private function summarizeCollection(Collection $riwayat, string $label, ?Carbon $mulai, ?Carbon $selesai): array
    {
        return [
            'label' => $label,
            'periode_mulai' => $mulai?->toIso8601String(),
            'periode_selesai' => $selesai?->toIso8601String(),
            'jumlah_sesi' => $riwayat->count(),
            'jumlah_transaksi' => (int) $riwayat->sum('jumlah_transaksi'),
            'total_budget' => round((float) $riwayat->sum(fn (array $item) => (float) ($item['anggaran_awal'] ?? 0)), 2),
            'total_pemasukan' => round((float) $riwayat->sum(fn (array $item) => (float) ($item['total_pemasukan'] ?? 0)), 2),
            'total_pengeluaran' => round((float) $riwayat->sum(fn (array $item) => (float) ($item['total_pengeluaran'] ?? 0)), 2),
            'total_sisa' => round((float) $riwayat->sum(fn (array $item) => (float) ($item['anggaran_sisa'] ?? 0)), 2),
        ];
    }

    public function destroy(Simulasi $simulasi)
    {
        $simulasi->delete();
        return response()->json(['message' => 'Data simulasi dihapus']);
    }
}
