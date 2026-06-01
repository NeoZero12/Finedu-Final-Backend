<?php

namespace App\Http\Controllers;

use App\Models\HasilSkor;
use App\Models\ModulPembelajaran;
use App\Models\ModulProgress;
use App\Models\Produk;
use App\Models\Profil;
use App\Models\Simulasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilSkorController extends Controller
{
    public function analisisEksperimen(Request $request)
    {
        $group = $request->query('group');
        $userQuery = User::where('role', 'mahasiswa');
        $profilQuery = Profil::query();
        $hasilSkorQuery = HasilSkor::query()->join('users', 'users.id', '=', 'hasil_skors.user_id');
        $simulasiQuery = Simulasi::query()->join('users', 'users.id', '=', 'simulasis.user_id');

        if (in_array($group, ['A', 'B', 'C', 'D', 'E'], true)) {
            $userQuery->whereHas('profil', fn ($q) => $q->where('kelompok_eksperimen', $group));
            $profilQuery->where('kelompok_eksperimen', $group);
            $hasilSkorQuery->join('profils', 'profils.user_id', '=', 'users.id')->where('profils.kelompok_eksperimen', $group);
            $simulasiQuery->join('profils', 'profils.user_id', '=', 'users.id')->where('profils.kelompok_eksperimen', $group);
        }

        return response()->json([
            'ringkasan' => [
                'total_mahasiswa' => $userQuery->count(),
                'total_admin' => User::where('role', 'admin')->count(),
                'total_modul' => ModulPembelajaran::count(),
                'total_produk' => Produk::count(),
                'simulasi_aktif' => (clone $simulasiQuery)->where('status', 'berlangsung')->count(),
                'rata_skor_literasi' => round((float) $hasilSkorQuery->avg('skor_literasi_akhir'), 1),
                'rata_overspending' => round((float) $hasilSkorQuery->avg('total_overspending'), 2),
                'responden_terverifikasi' => (clone $profilQuery)->where('status_verifikasi', true)->count(),
                'nudge_on' => (clone $profilQuery)->where('nudge_aktif', true)->count(),
                'nudge_off' => (clone $profilQuery)->where('nudge_aktif', false)->count(),
            ],
            'pengguna_terbaru' => User::with('profil')
                ->when(in_array($group, ['A', 'B', 'C', 'D', 'E'], true), fn ($q) => $q->whereHas('profil', fn ($p) => $p->where('kelompok_eksperimen', $group)))
                ->latest()
                ->take(5)
                ->get(),
            'komposisi_produk' => Produk::select('kategori', DB::raw('count(*) as total'))
                ->groupBy('kategori')
                ->get(),
            'komposisi_eksperimen' => $profilQuery->select('kelompok_eksperimen', DB::raw('count(*) as total'))
                ->whereNotNull('kelompok_eksperimen')
                ->groupBy('kelompok_eksperimen')
                ->get(),
            'ringkasan_kelompok' => Profil::query()
                ->select(
                    'kelompok_eksperimen',
                    DB::raw('count(*) as total_responden'),
                    DB::raw('sum(case when nudge_aktif = 1 then 1 else 0 end) as total_nudge_on'),
                    DB::raw('sum(case when tipe_budget = "ketat" then 1 else 0 end) as total_budget_ketat')
                )
                ->when(in_array($group, ['A', 'B','C', 'D', 'E'], true), fn ($q) => $q->where('kelompok_eksperimen', $group))
                ->whereNotNull('kelompok_eksperimen')
                ->groupBy('kelompok_eksperimen')
                ->get(),
            'progress_modul' => [
                'selesai' => ModulProgress::where('selesai', true)
                    ->when(in_array($group, ['A', 'B', 'C', 'D', 'E'], true), fn ($q) => $q->whereHas('user.profil', fn ($p) => $p->where('kelompok_eksperimen', $group)))
                    ->count(),
                'belum' => max(
                    0,
                    ($userQuery->count() * max(ModulPembelajaran::count(), 1))
                    - ModulProgress::where('selesai', true)
                        ->when(in_array($group, ['A', 'B', 'C', 'D', 'E'], true), fn ($q) => $q->whereHas('user.profil', fn ($p) => $p->where('kelompok_eksperimen', $group)))
                        ->count()
                ),
            ],
        ]);
    }

    public function export(Request $request)
    {
        $group = $request->query('group');

        $rows = User::with(['profil', 'hasilSkors', 'simulasis.transaksi'])
            ->where('role', 'mahasiswa')
            ->when(in_array($group, ['A', 'B', 'C', 'D', 'E'], true), fn ($q) => $q->whereHas('profil', fn ($p) => $p->where('kelompok_eksperimen', $group)))
            ->get()
            ->map(function ($user) {
                $latestScore = $user->hasilSkors->sortByDesc('created_at')->first();
                $latestSimulation = $user->simulasis->sortByDesc('created_at')->first();

                return [
                    'Nama' => $user->name,
                    'Email' => $user->email,
                    'Kelompok_Eksperimen' => $user->profil?->kelompok_eksperimen,
                    'Budget' => $user->profil?->tipe_budget,
                    'Nudge_Aktif' => $user->profil?->nudge_aktif ? 'Ya' : 'Tidak',
                    'Skor_Literasi' => $latestScore?->skor_literasi_akhir ?? 0,
                    'Total_Overspending' => $latestScore?->total_overspending ?? 0,
                    'Jumlah_Transaksi' => $latestSimulation?->transaksi?->count() ?? 0,
                    'Status_Simulasi_Terakhir' => $latestSimulation?->status ?? '-',
                ];
            });

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="experiment-results.csv"',
        ];

        return response()->stream(function () use ($rows) {
            $output = fopen('php://output', 'w');
            fwrite($output, chr(239).chr(187).chr(191));

            if ($rows->isNotEmpty()) {
                fputcsv($output, array_keys($rows->first()));
                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
            }

            fclose($output);
        }, 200, $headers);
    }
}
