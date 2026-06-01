<?php

namespace App\Models;

use App\Support\DefaultLearningModules;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    // Nama tabel di database (pastikan jamak 'profils' atau sesuaikan)
    protected $table = 'profils';

    protected $fillable = [
        'user_id',
        'tingkat_literasi',
        'tipe_budget',
        'avatar',
        'banner',
        'nudge_aktif',
        'informed_consent',
        'status_verifikasi',
        'usia',
        'jenis_kelamin',
        'universitas',
        'nim',
        'kelompok_eksperimen',
        'kode_sertifikat',
    ];

    protected $appends = [
        'skor_literasi',
        'sertifikat_tersedia',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSkorLiterasiAttribute(): int
    {
        return (int) ($this->user?->hasilSkors()->latest()->value('skor_literasi_akhir') ?? 0);
    }

    public function certificateProgress(): array
    {
        $totalModul = ModulPembelajaran::count();

        if (! $this->user || $totalModul === 0) {
            return [
                'completed_modules' => 0,
                'total_modules' => $totalModul,
                'completed_questionnaires' => 0,
                'total_questionnaires' => $totalModul,
            ];
        }

        $modules = ModulPembelajaran::with([
            'kuesioners' => fn ($query) => $query->where('user_id', $this->user->id),
        ])->get();

        $questionnaireProgress = $modules->map(function ($modul) {
            $questionnaireTotal = count(DefaultLearningModules::questionnaireFor($modul->judul_modul));

            return [
                'questionnaire_total' => $questionnaireTotal,
                'questionnaire_answered' => $modul->kuesioners->count(),
            ];
        });

        $totalQuestionnaires = $questionnaireProgress
            ->filter(fn (array $item) => $item['questionnaire_total'] > 0)
            ->count();

        $completedQuestionnaires = $questionnaireProgress
            ->filter(fn (array $item) => $item['questionnaire_total'] > 0 && $item['questionnaire_answered'] >= $item['questionnaire_total'])
            ->count();

        return [
            'completed_modules' => $this->user->modulProgresses()->where('selesai', true)->count(),
            'total_modules' => $totalModul,
            'completed_questionnaires' => $completedQuestionnaires,
            'total_questionnaires' => $totalQuestionnaires,
        ];
    }

    public function getSertifikatTersediaAttribute(): bool
    {
        $progress = $this->certificateProgress();

        return $progress['total_modules'] > 0
            && $progress['completed_modules'] >= $progress['total_modules']
            && $progress['completed_questionnaires'] >= $progress['total_questionnaires'];
    }
}
