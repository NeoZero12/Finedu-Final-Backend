<?php

namespace App\Http\Controllers;

use App\Models\DigitalNudge;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DigitalNudgeController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            DigitalNudge::where('user_id', $request->user()->id)
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe_nudge' => ['required', Rule::in(['notifikasi', 'warna_merah', 'goal_reminder'])],
            'diabaikan' => ['nullable', 'boolean'],
        ]);

        $nudge = DigitalNudge::create([
            'user_id' => $request->user()->id,
            'tipe_nudge' => $validated['tipe_nudge'],
            'diabaikan' => $validated['diabaikan'] ?? false,
        ]);

        return response()->json([
            'message' => 'Respons nudge berhasil dicatat.',
            'data' => $nudge,
        ], 201);
    }

    public function export(Request $request): StreamedResponse
    {
        $group = $request->query('group');

        $rows = DigitalNudge::with(['user.profil'])
            ->when(
                in_array($group, ['A', 'B', 'C', 'D', 'E'], true),
                fn ($query) => $query->whereHas('user.profil', fn ($profil) => $profil->where('kelompok_eksperimen', $group))
            )
            ->latest()
            ->get()
            ->map(function (DigitalNudge $nudge) {
                return [
                    'Nama' => $nudge->user?->name,
                    'Email' => $nudge->user?->email,
                    'NIM' => $nudge->user?->profil?->nim,
                    'Kelompok_Eksperimen' => $nudge->user?->profil?->kelompok_eksperimen,
                    'Budget' => $nudge->user?->profil?->tipe_budget,
                    'Nudge_Aktif' => $nudge->user?->profil?->nudge_aktif ? 'Ya' : 'Tidak',
                    'Tipe_Nudge' => $nudge->tipe_nudge,
                    'Diabaikan' => $nudge->diabaikan ? 'Ya' : 'Tidak',
                    'Dibuat_Pada' => optional($nudge->created_at)?->format('Y-m-d H:i:s'),
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
        }, 'digital-nudge-finedu.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
