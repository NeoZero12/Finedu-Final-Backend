<?php

namespace App\Http\Controllers;

use App\Models\Kuesioner;
use App\Models\ModulPembelajaran;
use App\Support\DefaultLearningModules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KuesionerController extends Controller
{
    // Percobaan pertama ditambah dua kali ulang.
    private const MAX_ATTEMPTS = 3;

    public function index(Request $request)
    {
        return response()->json(
            Kuesioner::with('modul')
                ->where('user_id', $request->user()->id)
                ->latest()
                ->get()
                ->groupBy('modul_pembelajaran_id')
                ->values()
                ->map(function ($items) {
                    $first = $items->first();

                    return [
                        'modul_id' => $first?->modul_pembelajaran_id,
                        'judul_modul' => $first?->modul?->judul_modul,
                        'jumlah_soal' => $items->count(),
                        'jumlah_benar' => $items->where('benar', true)->count(),
                        'jawaban' => $items->values(),
                    ];
                })
        );
    }

    public function showByModule(Request $request, ModulPembelajaran $modul)
    {
        DefaultLearningModules::sync();

        // Pertanyaan diambil dari daftar modul default agar admin tidak perlu input ulang satu per satu.
        $questions = DefaultLearningModules::questionnaireFor($modul->judul_modul);
        $questionCount = count($questions);
        $answers = Kuesioner::query()
            ->where('user_id', $request->user()->id)
            ->where('modul_pembelajaran_id', $modul->id)
            ->orderBy('nomor_soal')
            ->get();
        $answers = $answers->count() === $questionCount ? $answers : collect();
        $summary = $this->buildQuizSummary($answers, $questionCount);
        $attemptCount = (int) Kuesioner::query()
            ->where('user_id', $request->user()->id)
            ->where('modul_pembelajaran_id', $modul->id)
            ->max('attempt_count');

        return response()->json([
            'modul_id' => $modul->id,
            'judul_modul' => $modul->judul_modul,
            'total_questions' => $questionCount,
            'is_unlocked' => $request->user()->modulProgresses()
                ->where('modul_pembelajaran_id', $modul->id)
                ->where('selesai', true)
                ->exists(),
            'is_completed' => $answers->count() === $questionCount && $questionCount > 0,
            'attempt_count' => $attemptCount,
            'max_attempts' => self::MAX_ATTEMPTS,
            // Angka ini yang ditampilkan frontend sebagai sisa pengulangan.
            'remaining_retries' => max(0, self::MAX_ATTEMPTS - max(1, $attemptCount)),
            ...$summary,
            'questions' => $this->buildQuestionPayloads($questions, $answers),
            'answers' => $answers->values()->map(fn (Kuesioner $answer) => [
                'nomor_soal' => $answer->nomor_soal,
                'jawaban_skala' => $answer->jawaban_skala,
                'benar' => $answer->benar,
            ]),
        ]);
    }

    public function storeByModule(Request $request, ModulPembelajaran $modul)
    {
        DefaultLearningModules::sync();

        $questions = array_values(DefaultLearningModules::questionnaireFor($modul->judul_modul));
        $questionCount = count($questions);
        if ($questions === []) {
            return response()->json(['message' => 'Kuesioner modul belum tersedia.'], 404);
        }

        $isUnlocked = $request->user()->modulProgresses()
            ->where('modul_pembelajaran_id', $modul->id)
            ->where('selesai', true)
            ->exists();

        if (! $isUnlocked) {
            return response()->json(['message' => 'Selesaikan jurnal modul terlebih dahulu sebelum mengerjakan kuesioner.'], 422);
        }

        $validated = $request->validate([
            'answers' => ['required', 'array', 'size:' . $questionCount],
            'answers.*.nomor_soal' => ['required', 'integer', 'min:1', 'max:' . $questionCount],
            'answers.*.jawaban_skala' => ['required', 'integer', 'min:1', 'max:4'],
        ]);

        $currentAttempt = (int) Kuesioner::query()
            ->where('user_id', $request->user()->id)
            ->where('modul_pembelajaran_id', $modul->id)
            ->max('attempt_count');
        $nextAttempt = $currentAttempt + 1;

        // Kalau sudah tiga kali, jawaban lama tetap bisa dilihat tapi tidak bisa dikirim ulang.
        if ($currentAttempt >= self::MAX_ATTEMPTS) {
            return response()->json([
                'message' => 'Batas pengerjaan kuesioner sudah habis. Kuesioner hanya bisa dikerjakan 1 kali dan diulang maksimal 2 kali.',
                'attempt_count' => $currentAttempt,
                'max_attempts' => self::MAX_ATTEMPTS,
                'remaining_retries' => 0,
            ], 422);
        }

        DB::transaction(function () use ($request, $modul, $validated, $questions, $nextAttempt) {
            // Jawaban modul diganti per percobaan supaya rekap selalu membaca hasil terbaru.
            Kuesioner::query()
                ->where('user_id', $request->user()->id)
                ->where('modul_pembelajaran_id', $modul->id)
                ->delete();

            foreach ($validated['answers'] as $answer) {
                $index = $answer['nomor_soal'] - 1;
                $question = $questions[$index] ?? null;

                if (! $question) {
                    continue;
                }

                $selected = (int) $answer['jawaban_skala'];
                $correct = (int) $question['jawaban_benar'];

                Kuesioner::create([
                    'user_id' => $request->user()->id,
                    'modul_pembelajaran_id' => $modul->id,
                    'nomor_soal' => $answer['nomor_soal'],
                    'pertanyaan' => $question['pertanyaan'],
                    'opsi_jawaban' => $question['opsi'],
                    'jawaban_benar' => $correct,
                    'jawaban_skala' => $selected,
                    'benar' => $selected === $correct,
                    'attempt_count' => $nextAttempt,
                ]);
            }
        });

        $savedAnswers = Kuesioner::query()
            ->where('user_id', $request->user()->id)
            ->where('modul_pembelajaran_id', $modul->id)
            ->get();
        $summary = $this->buildQuizSummary($savedAnswers, $questionCount);

        return response()->json([
            'message' => $summary['completion_message'] ?? 'Kuesioner modul berhasil disimpan.',
            'attempt_count' => $nextAttempt,
            'max_attempts' => self::MAX_ATTEMPTS,
            'remaining_retries' => max(0, self::MAX_ATTEMPTS - $nextAttempt),
            ...$summary,
            'data' => $savedAnswers,
        ], 201);
    }

    public function export(Request $request): StreamedResponse
    {
        $group = $request->query('group');

        $rows = Kuesioner::with(['user.profil', 'modul'])
            ->when(
                in_array($group, ['A', 'B', 'C', 'D', 'E'], true),
                fn ($query) => $query->whereHas('user.profil', fn ($profil) => $profil->where('kelompok_eksperimen', $group))
            )
            ->latest()
            ->get()
            ->map(function (Kuesioner $kuesioner) {
                return [
                    'Nama' => $kuesioner->user?->name,
                    'Email' => $kuesioner->user?->email,
                    'NIM' => $kuesioner->user?->profil?->nim,
                    'Kelompok_Eksperimen' => $kuesioner->user?->profil?->kelompok_eksperimen,
                    'Budget' => $kuesioner->user?->profil?->tipe_budget,
                    'Nudge_Aktif' => $kuesioner->user?->profil?->nudge_aktif ? 'Ya' : 'Tidak',
                    'Modul' => $kuesioner->modul?->judul_modul,
                    'Nomor_Soal' => $kuesioner->nomor_soal,
                    'Pertanyaan' => $kuesioner->pertanyaan,
                    'Pilihan_Pengguna' => $kuesioner->jawaban_skala,
                    'Jawaban_Benar' => $kuesioner->jawaban_benar,
                    'Status' => $kuesioner->benar ? 'Benar' : 'Salah',
                    'Jawaban_Skala' => $kuesioner->jawaban_skala,
                    'Dibuat_Pada' => optional($kuesioner->created_at)?->format('Y-m-d H:i:s'),
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
        }, 'kuesioner-finedu.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildQuestionPayloads(array $questions, Collection $answers): Collection
    {
        $answersByNumber = $answers->keyBy('nomor_soal');

        return collect($questions)->values()->map(function (array $question, int $index) use ($answersByNumber) {
            $number = $question['nomor_soal'] ?? ($index + 1);
            $answer = $answersByNumber->get($number);
            $selectedAnswer = $answer?->jawaban_skala;
            $correctAnswer = (int) ($question['jawaban_benar'] ?? 1);
            $correctText = $question['opsi'][$correctAnswer - 1] ?? null;
            $selectedText = $selectedAnswer ? ($question['opsi'][$selectedAnswer - 1] ?? null) : null;
            $wrongReason = null;

            if ($answer && ! $answer->benar) {
                $wrongReason = trim(sprintf(
                    'Jawaban "%s" belum tepat. %s',
                    $selectedText,
                    $question['penjelasan_salah'] ?? 'Pilihan tersebut belum sesuai dengan inti materi pada soal ini.'
                ));
            }

            return [
                'nomor_soal' => $number,
                'pertanyaan' => $question['pertanyaan'],
                'opsi' => $question['opsi'],
                'jawaban_benar' => $correctAnswer,
                'jawaban_benar_teks' => $correctText,
                'jawaban_pengguna' => $selectedAnswer,
                'jawaban_pengguna_teks' => $selectedText,
                'is_correct' => $answer ? (bool) $answer->benar : null,
                'penjelasan_salah' => $wrongReason,
                'penjelasan_benar' => $question['penjelasan_benar'] ?? null,
            ];
        });
    }

    private function buildQuizSummary(Collection $answers, int $questionCount): array
    {
        if ($questionCount === 0 || $answers->isEmpty()) {
            return [
                'score' => null,
                'correct_answers' => 0,
                'grade' => null,
                'grade_range' => null,
                'grade_title' => null,
                'performance_note' => null,
                'motivation_message' => null,
                'completion_message' => null,
            ];
        }

        $correctAnswers = $answers->where('benar', true)->count();
        $score = (int) round(($correctAnswers / max($questionCount, 1)) * 100);
        $gradeData = $this->gradeFromScore($score);

        return [
            'score' => $score,
            'correct_answers' => $correctAnswers,
            'grade' => $gradeData['grade'],
            'grade_range' => $gradeData['range'],
            'grade_title' => $gradeData['title'],
            'performance_note' => $gradeData['note'],
            'motivation_message' => $gradeData['motivation'],
            'completion_message' => $gradeData['completion'],
        ];
    }

    private function gradeFromScore(int $score): array
    {
        if ($score === 100) {
            return [
                'grade' => 'A',
                'range' => '100',
                'title' => 'Sempurna',
                'note' => 'Semua konsep utama pada modul ini sudah Anda kuasai dengan sangat baik.',
                'motivation' => 'Luar biasa, semua jawaban Anda tepat. Pertahankan ketelitian dan ritme belajar seperti ini.',
                'completion' => 'Skor sempurna! Luar biasa, Anda menuntaskan kuesioner ini dengan hasil terbaik.',
            ];
        }

        $bands = [
            ['min' => 85, 'grade' => 'A', 'range' => '85-99', 'title' => 'Sangat Baik', 'note' => 'Pemahaman Anda kuat dan hanya ada sedikit detail yang masih bisa dipertajam.', 'motivation' => 'Hasil Anda sangat kuat. Sedikit penguatan lagi akan membuat pemahaman Anda semakin matang.', 'completion' => 'Kerja bagus! Hasil kuesioner Anda berada pada peringkat A.'],
            ['min' => 80, 'grade' => 'B+', 'range' => '80-84', 'title' => 'Baik Sekali', 'note' => 'Anda sudah berada di atas rata-rata dan memahami sebagian besar inti materi.', 'motivation' => 'Bagus sekali. Tinggal rapikan beberapa bagian kecil untuk naik ke peringkat A.', 'completion' => 'Kerja bagus! Hasil kuesioner Anda berada pada peringkat B+.'],
            ['min' => 75, 'grade' => 'B', 'range' => '75-79', 'title' => 'Baik', 'note' => 'Dasar konsep sudah kuat dan Anda sudah memahami arah materi dengan baik.', 'motivation' => 'Hasil Anda solid. Baca ulang bagian yang masih ragu agar pemahaman makin tajam.', 'completion' => 'Kuesioner selesai dengan peringkat B. Tetap lanjut, hasil Anda sudah baik.'],
            ['min' => 70, 'grade' => 'C+', 'range' => '70-74', 'title' => 'Cukup Baik', 'note' => 'Pemahaman inti sudah mulai terbentuk, tetapi masih ada beberapa konsep yang tertukar.', 'motivation' => 'Anda sudah berada di jalur yang benar. Fokus pada pembahasan yang masih sering tertukar.', 'completion' => 'Kuesioner selesai dengan peringkat C+. Masih ada ruang besar untuk naik lebih tinggi.'],
            ['min' => 60, 'grade' => 'C', 'range' => '60-69', 'title' => 'Cukup', 'note' => 'Anda sudah memiliki dasar, namun perlu menguatkan lagi poin-poin utama modul.', 'motivation' => 'Dasarnya sudah ada. Baca ulang ringkasan materi dan cocokkan dengan feedback setiap soal.', 'completion' => 'Kuesioner selesai dengan peringkat C. Tetap semangat, fondasinya sudah mulai terbentuk.'],
            ['min' => 50, 'grade' => 'D', 'range' => '50-59', 'title' => 'Perlu Penguatan', 'note' => 'Masih ada banyak konsep yang perlu ditinjau kembali sebelum lanjut ke materi berikutnya.', 'motivation' => 'Jangan patah semangat. Pelajari lagi bagian yang keliru satu per satu, lalu coba ulangi.', 'completion' => 'Kuesioner selesai dengan peringkat D. Tenang, ini masih bisa ditingkatkan.'],
            ['min' => 40, 'grade' => 'E', 'range' => '40-49', 'title' => 'Perlu Belajar Ulang', 'note' => 'Anda membutuhkan penguatan materi yang lebih menyeluruh pada modul ini.', 'motivation' => 'Masih perlu banyak penguatan, tetapi Anda sudah punya titik awal untuk memperbaikinya.', 'completion' => 'Kuesioner selesai dengan peringkat E. Luangkan waktu membaca ulang materi inti modul ini.'],
            ['min' => 0, 'grade' => 'F', 'range' => '0-39', 'title' => 'Perlu Pendampingan', 'note' => 'Pemahaman materi belum terbentuk dengan baik dan perlu dibangun kembali dari bagian dasar.', 'motivation' => 'Belum apa-apa. Mulai lagi pelan-pelan dari ringkasan materi, lalu coba ulangi dengan lebih tenang.', 'completion' => 'Kuesioner selesai dengan peringkat F. Jadikan hasil ini sebagai awal untuk belajar lebih terarah.'],
        ];

        foreach ($bands as $band) {
            if ($score >= $band['min']) {
                return [
                    'grade' => $band['grade'],
                    'range' => $band['range'],
                    'title' => $band['title'],
                    'note' => $band['note'],
                    'motivation' => $band['motivation'],
                    'completion' => $band['completion'],
                ];
            }
        }

        return [
            'grade' => 'F',
            'range' => '0-39',
            'title' => 'Perlu Pendampingan',
            'note' => 'Pemahaman materi masih perlu dibangun kembali dari dasar.',
            'motivation' => 'Mulai lagi dari ringkasan materi, lalu cek feedback pada setiap jawaban.',
            'completion' => 'Kuesioner selesai. Coba pelajari lagi materi ini dan ulangi saat sudah siap.',
        ];
    }
}
