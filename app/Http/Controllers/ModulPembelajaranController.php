<?php

namespace App\Http\Controllers;

use App\Models\ModulPembelajaran;
use App\Support\DefaultLearningModules;
use Illuminate\Http\Request;

class ModulPembelajaranController extends Controller
{
    public function index(Request $request)
    {
        DefaultLearningModules::sync();

        return response()->json(
            ModulPembelajaran::withCount('materis')
                ->with([
                    'materis',
                    'progresses' => fn ($query) => $query->where('user_id', $request->user()->id),
                    'kuesioners' => fn ($query) => $query->where('user_id', $request->user()->id),
                ])
                ->latest()
                ->get()
                ->map(function ($modul) {
                    $questionnaireTotal = count(DefaultLearningModules::questionnaireFor($modul->judul_modul));
                    $answerCount = $modul->kuesioners->count();
                    $modul->is_completed = $modul->progresses->contains('selesai', true);
                    $modul->questionnaire_total = $questionnaireTotal;
                    $modul->questionnaire_completed = $questionnaireTotal > 0 && $answerCount >= $questionnaireTotal;
                    $modul->questionnaire_score = $modul->questionnaire_completed
                        ? round(($modul->kuesioners->where('benar', true)->count() / $answerCount) * 100)
                        : null;
                    $modul->sumber_jurnal = DefaultLearningModules::sourceFor($modul->judul_modul);
                    unset($modul->progresses);
                    unset($modul->kuesioners);

                    return $modul;
                })
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_modul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
        ]);

        $modul = ModulPembelajaran::create($validated);

        return response()->json([
            'message' => 'Modul berhasil ditambahkan.',
            'data' => $modul,
        ], 201);
    }

    public function show(ModulPembelajaran $modul)
    {
        DefaultLearningModules::sync();

        $modul->load('materis');

        return response()->json([
            ...$modul->toArray(),
            'sumber_jurnal' => DefaultLearningModules::sourceFor($modul->judul_modul),
            'referensi_jurnal' => DefaultLearningModules::sourceFor($modul->judul_modul),
            'kuesioner' => collect(DefaultLearningModules::questionnaireFor($modul->judul_modul))
                ->values()
                ->map(fn (array $question) => [
                    'nomor_soal' => $question['nomor_soal'],
                    'pertanyaan' => $question['pertanyaan'],
                    'opsi' => $question['opsi'],
                ]),
        ]);
    }

    public function update(Request $request, ModulPembelajaran $modul)
    {
        $validated = $request->validate([
            'judul_modul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
        ]);

        $modul->update($validated);

        return response()->json([
            'message' => 'Modul berhasil diperbarui.',
            'data' => $modul->fresh(),
        ]);
    }

    public function destroy(ModulPembelajaran $modul)
    {
        $modul->delete();

        return response()->json([
            'message' => 'Modul berhasil dihapus.',
        ]);
    }
}
