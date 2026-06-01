<?php

use App\Models\HasilSkor;
use App\Models\Simulasi;
use App\Support\SimulationScoring;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->shiftBalances(-1);
    }

    public function down(): void
    {
        $this->shiftBalances(1);
    }

    private function shiftBalances(int $direction): void
    {
        DB::transaction(function () use ($direction) {
            Simulasi::query()
                ->with('user.profil')
                ->chunkById(100, function ($simulasis) use ($direction) {
                    foreach ($simulasis as $simulasi) {
                        $baseline = $simulasi->user?->profil?->tipe_budget === 'longgar' ? 2000000 : 500000;

                        DB::table('simulasis')
                            ->where('id', $simulasi->id)
                            ->update([
                                'anggaran_awal' => max(0, (float) $simulasi->anggaran_awal + ($baseline * $direction)),
                                'anggaran_sisa' => (float) $simulasi->anggaran_sisa + ($baseline * $direction),
                            ]);
                    }
                });

            $userIds = Simulasi::query()
                ->where('status', 'selesai')
                ->distinct()
                ->pluck('user_id');

            foreach ($userIds as $userId) {
                $latestSimulation = Simulasi::query()
                    ->with('transaksi')
                    ->where('user_id', $userId)
                    ->where('status', 'selesai')
                    ->latest('updated_at')
                    ->latest('id')
                    ->first();

                if (! $latestSimulation) {
                    HasilSkor::where('user_id', $userId)->delete();

                    continue;
                }

                $score = SimulationScoring::calculate($latestSimulation);

                HasilSkor::updateOrCreate([
                    'user_id' => $userId,
                ], [
                    'skor_literasi_akhir' => $score['skor'],
                    'total_overspending' => $score['total_overspending'],
                ]);
            }
        });
    }
};
