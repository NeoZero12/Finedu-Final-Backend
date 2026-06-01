<?php

namespace App\Http\Controllers;

use App\Models\ModulPembelajaran;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('profil');
        $progress = $user->profil?->certificateProgress() ?? [
            'completed_modules' => 0,
            'total_modules' => ModulPembelajaran::count(),
            'completed_questionnaires' => 0,
            'total_questionnaires' => ModulPembelajaran::count(),
        ];

        return response()->json([
            'eligible' => $user->profil?->sertifikat_tersedia ?? false,
            'certificate_code' => $user->profil?->kode_sertifikat,
            'user_name' => $user->name,
            'score' => $user->profil?->skor_literasi ?? 0,
            'completed_modules' => $progress['completed_modules'],
            'total_modules' => $progress['total_modules'],
            'completed_questionnaires' => $progress['completed_questionnaires'],
            'total_questionnaires' => $progress['total_questionnaires'],
        ]);
    }
}
