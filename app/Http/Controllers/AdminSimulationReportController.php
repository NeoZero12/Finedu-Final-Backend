<?php

namespace App\Http\Controllers;

use App\Models\HasilSkor;
use App\Models\Simulasi;
use App\Support\SimulationScoring;
use App\Support\SimulationSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AdminSimulationReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = $this->filteredReports($request);

        return response()->json([
            'ringkasan' => $this->buildSummary($reports),
            'riwayat' => $reports->values(),
        ]);
    }

    public function export(Request $request)
    {
        $rows = $this->filteredReports($request)->map(function (array $report) {
            return [
                'ID_Laporan' => $report['id'],
                'Nama' => $report['user']['name'],
                'Email' => $report['user']['email'],
                'NIM' => $report['user']['nim'],
                'Universitas' => $report['user']['universitas'],
                'Kelompok' => $report['user']['kelompok_eksperimen'],
                'Verifikasi' => $report['user']['status_verifikasi'] ? 'Terverifikasi' : 'Belum',
                'Status_Akun' => $report['user']['is_active'] ? 'Aktif' : 'Nonaktif',
                'Skor_Literasi' => $report['user']['skor_literasi'],
                'Status_Laporan' => $report['status_laporan'],
                'Tanggal_Selesai' => ! empty($report['diselesaikan_pada'])
                    ? date('Y-m-d H:i:s', strtotime($report['diselesaikan_pada']))
                    : null,
                'Total_Budget' => $report['anggaran_awal'],
                'Total_Pemasukan' => $report['total_pemasukan'],
                'Total_Pengeluaran' => $report['total_pengeluaran'],
                'Sisa_Akhir' => $report['anggaran_sisa'],
                'Jumlah_Transaksi' => $report['jumlah_transaksi'],
            ];
        });

        return response()->streamDownload(function () use ($rows) {
            $output = fopen('php://output', 'w');
            fwrite($output, chr(239).chr(187).chr(191));

            if ($rows->isNotEmpty()) {
                fputcsv($output, array_keys($rows->first()));

                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
            }

            fclose($output);
        }, 'laporan-simulasi-user.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function destroy(Simulasi $simulasi)
    {
        abort_unless($simulasi->status === 'selesai', 422);
        abort_unless($simulasi->user?->role === 'mahasiswa', 404);

        $userId = $simulasi->user_id;
        $simulasi->delete();
        $this->refreshLatestScore($userId);

        return response()->json([
            'message' => 'Laporan simulasi berhasil dihapus.',
        ]);
    }

    private function filteredReports(Request $request): Collection
    {
        $group = $request->query('group');
        $status = $request->query('status');
        $search = trim((string) $request->query('search', ''));

        return Simulasi::with(['user.profil', 'transaksi.produk'])
            ->where('status', 'selesai')
            ->whereHas('user', fn ($query) => $query->where('role', 'mahasiswa'))
            ->when(
                in_array($group, ['A', 'B', 'C', 'D', 'E'], true),
                fn ($query) => $query->whereHas('user.profil', fn ($profil) => $profil->where('kelompok_eksperimen', $group))
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where(function ($builder) use ($search) {
                        $builder->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('profil', fn ($profil) => $profil->where('nim', 'like', "%{$search}%"));
                    });
                });
            })
            ->latest('updated_at')
            ->get()
            ->map(function (Simulasi $simulasi) {
                $summary = SimulationSummary::format($simulasi, $simulasi->user?->profil);
                $isControlled = (float) $summary['anggaran_sisa'] >= 0
                    && ! str_starts_with((string) $summary['budget_indicator'], 'overspending');

                return [
                    ...$summary,
                    'status_laporan' => $isControlled ? 'Terkendali' : 'Perlu evaluasi',
                    'user' => [
                        'id' => $simulasi->user?->id,
                        'name' => $simulasi->user?->name,
                        'email' => $simulasi->user?->email,
                        'is_active' => (bool) $simulasi->user?->is_active,
                        'nim' => $simulasi->user?->profil?->nim,
                        'universitas' => $simulasi->user?->profil?->universitas,
                        'kelompok_eksperimen' => $simulasi->user?->profil?->kelompok_eksperimen,
                        'status_verifikasi' => (bool) $simulasi->user?->profil?->status_verifikasi,
                        'skor_literasi' => $simulasi->user?->profil?->skor_literasi ?? 0,
                    ],
                ];
            })
            ->when(
                in_array($status, ['terkendali', 'perlu-evaluasi'], true),
                function (Collection $collection) use ($status) {
                    return $collection->filter(function (array $report) use ($status) {
                        return $status === 'terkendali'
                            ? $report['status_laporan'] === 'Terkendali'
                            : $report['status_laporan'] === 'Perlu evaluasi';
                    })->values();
                }
            );
    }

    private function buildSummary(Collection $reports): array
    {
        return [
            'total_laporan' => $reports->count(),
            'total_mahasiswa' => $reports->pluck('user.id')->filter()->unique()->count(),
            'total_transaksi' => (int) $reports->sum('jumlah_transaksi'),
            'total_budget' => round((float) $reports->sum('anggaran_awal'), 2),
            'total_pemasukan' => round((float) $reports->sum('total_pemasukan'), 2),
            'total_pengeluaran' => round((float) $reports->sum('total_pengeluaran'), 2),
            'total_sisa' => round((float) $reports->sum('anggaran_sisa'), 2),
            'sesi_terkendali' => $reports->where('status_laporan', 'Terkendali')->count(),
            'sesi_perlu_evaluasi' => $reports->where('status_laporan', 'Perlu evaluasi')->count(),
        ];
    }

    private function refreshLatestScore(int $userId): void
    {
        $latestSimulation = Simulasi::with('transaksi')
            ->where('user_id', $userId)
            ->where('status', 'selesai')
            ->latest('updated_at')
            ->latest('id')
            ->first();

        if (! $latestSimulation) {
            HasilSkor::where('user_id', $userId)->delete();

            return;
        }

        $score = SimulationScoring::calculate($latestSimulation);

        HasilSkor::updateOrCreate([
            'user_id' => $userId,
        ], [
            'skor_literasi_akhir' => $score['skor'],
            'total_overspending' => $score['total_overspending'],
        ]);
    }
}
