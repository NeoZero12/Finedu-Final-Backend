<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminRespondentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $group = $request->query('group');

        $query = User::with('profil')
            ->where('role', 'mahasiswa')
            ->latest();

        if (in_array($group, ['A', 'B', 'C', 'D', 'E'], true)) {
            $query->whereHas('profil', fn ($q) => $q->where('kelompok_eksperimen', $group));
        }

        if ($search) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('profil', fn ($q) => $q->where('nim', 'like', "%{$search}%"));
            });
        }

        return response()->json($query->get());
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'mahasiswa', 404);

        $validated = $request->validate([
            'is_active' => ['nullable', 'boolean'],
            'status_verifikasi' => ['nullable', 'boolean'],
            'kelompok_eksperimen' => ['nullable', Rule::in(['A', 'B', 'C', 'D', 'E'])],
        ]);

        if (array_key_exists('is_active', $validated)) {
            $user->update(['is_active' => $validated['is_active']]);
        }

        $profilePayload = [];

        if (array_key_exists('status_verifikasi', $validated)) {
            $profilePayload['status_verifikasi'] = $validated['status_verifikasi'];
        }

        if (array_key_exists('kelompok_eksperimen', $validated)) {
            $profilePayload['kelompok_eksperimen'] = $validated['kelompok_eksperimen'];
        }

        if ($profilePayload !== []) {
            $user->profil()->updateOrCreate(
                ['user_id' => $user->id],
                $profilePayload
            );
        }

        return response()->json([
            'message' => 'Data responden berhasil diperbarui.',
            'data' => $user->fresh()->load('profil'),
        ]);
    }

    public function export()
    {
        $group = request()->query('group');

        $rows = User::with('profil')
            ->where('role', 'mahasiswa')
            ->when(in_array($group, ['A', 'B', 'C', 'D', 'E'], true), fn ($q) => $q->whereHas('profil', fn ($p) => $p->where('kelompok_eksperimen', $group)))
            ->get()
            ->map(function ($user) {
                return [
                    'Nama' => $user->name,
                    'Email' => $user->email,
                    'Status_Akun' => $user->is_active ? 'Aktif' : 'Nonaktif',
                    'Status_Verifikasi' => $user->profil?->status_verifikasi ? 'Terverifikasi' : 'Belum',
                    'Usia' => $user->profil?->usia,
                    'Jenis_Kelamin' => $user->profil?->jenis_kelamin,
                    'Universitas' => $user->profil?->universitas,
                    'NIM' => $user->profil?->nim,
                    'Kelompok_Eksperimen' => $user->profil?->kelompok_eksperimen,
                    'Budget' => $user->profil?->tipe_budget,
                    'Nudge_Aktif' => $user->profil?->nudge_aktif ? 'Ya' : 'Tidak',
                    'Skor_Literasi' => $user->profil?->skor_literasi ?? 0,
                    'Kode_Sertifikat' => $user->profil?->kode_sertifikat,
                ];
            });

        return $this->csvResponse('respondents-export.csv', $rows);
    }

    private function csvResponse(string $filename, $rows)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($rows) {
            $output = fopen('php://output', 'w');
            fwrite($output, chr(239).chr(187).chr(191));

            if ($rows->isNotEmpty()) {
                fputcsv($output, array_keys($rows->first()));
                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
