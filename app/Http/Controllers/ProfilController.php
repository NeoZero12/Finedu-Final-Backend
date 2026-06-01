<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfilController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user()->load('profil'));
    }

    public function update(Request $request)
    {
        // Email jadi identitas akun, jadi hanya nama dan detail profil yang boleh diganti.
        if ($request->filled('email') && $request->input('email') !== $request->user()->email) {
            throw ValidationException::withMessages([
                'email' => ['Email akun tidak dapat diganti setelah terdaftar.'],
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'tingkat_literasi' => ['nullable', Rule::in(['rendah', 'menengah', 'tinggi'])],
            'tipe_budget' => ['nullable', Rule::in(['ketat', 'longgar'])],
            'avatar' => ['nullable', 'url', 'max:2048'],
            'banner' => ['nullable', 'url', 'max:2048'],
            'nudge_aktif' => ['nullable', 'boolean'],
            'usia' => ['nullable', 'integer', 'min:17', 'max:30'],
            'jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
            'universitas' => ['nullable', 'string', 'max:255'],
            'nim' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $currentProfil = $user->profil;
        $userPayload = [
            'name' => $validated['name'],
        ];

        if (! empty($validated['password'])) {
            // Password baru disimpan hanya jika pengguna mengisi kolomnya.
            $userPayload['password'] = Hash::make($validated['password']);
        }

        $user->update($userPayload);

        $user->profil()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'tingkat_literasi' => $validated['tingkat_literasi'] ?? $currentProfil?->tingkat_literasi ?? 'rendah',
                'tipe_budget' => $validated['tipe_budget'] ?? $currentProfil?->tipe_budget ?? 'ketat',
                'avatar' => $validated['avatar'] ?? $currentProfil?->avatar,
                'banner' => $validated['banner'] ?? $currentProfil?->banner,
                'nudge_aktif' => $validated['nudge_aktif'] ?? $currentProfil?->nudge_aktif ?? true,
                'usia' => $validated['usia'] ?? $currentProfil?->usia,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? $currentProfil?->jenis_kelamin,
                'universitas' => $validated['universitas'] ?? $currentProfil?->universitas,
                'nim' => $validated['nim'] ?? $currentProfil?->nim,
                'status_verifikasi' => $currentProfil?->status_verifikasi ?? false,
            ]
        );

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data' => $user->fresh()->load('profil'),
        ]);
    }
}
