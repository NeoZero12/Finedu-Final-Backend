<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $role = $request->query('role');

        return response()->json(
            User::with('profil')
                ->when(in_array($role, ['admin', 'mahasiswa'], true), fn ($query) => $query->where('role', $role))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('profil', fn ($profil) => $profil
                                ->where('nim', 'like', "%{$search}%")
                                ->orWhere('universitas', 'like', "%{$search}%"));
                    });
                })
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $this->syncProfile($user, $validated);

        return response()->json([
            'message' => 'Admin berhasil ditambahkan.',
            'data' => $user->fresh()->load('profil'),
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'admin', 404);

        $validated = $this->validatePayload($request, $user);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => 'admin',
            'is_active' => $validated['is_active'] ?? true,
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);
        $this->syncProfile($user, $validated);

        return response()->json([
            'message' => 'Admin berhasil diperbarui.',
            'data' => $user->fresh()->load('profil'),
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($user->role === 'admin', 404);

        if ($request->user()->is($user)) {
            return response()->json(['message' => 'Admin tidak dapat menghapus akun yang sedang dipakai.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'Admin berhasil dihapus.']);
    }

    private function validatePayload(Request $request, ?User $user = null): array
    {
        $passwordRules = $user
            ? ['nullable', 'string', 'min:8']
            : ['required', 'string', 'min:8'];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => $passwordRules,
            'is_active' => ['nullable', 'boolean'],
            'tingkat_literasi' => ['nullable', Rule::in(['rendah', 'menengah', 'tinggi'])],
            'tipe_budget' => ['nullable', Rule::in(['ketat', 'longgar'])],
            'avatar' => ['nullable', 'url', 'max:2048'],
            'banner' => ['nullable', 'url', 'max:2048'],
            'nudge_aktif' => ['nullable', 'boolean'],
            'status_verifikasi' => ['nullable', 'boolean'],
            'usia' => ['nullable', 'integer', 'min:17', 'max:60'],
            'jenis_kelamin' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
            'universitas' => ['nullable', 'string', 'max:255'],
            'nim' => ['nullable', 'string', 'max:50'],
            'kelompok_eksperimen' => ['nullable', Rule::in(['A', 'B', 'C', 'D', 'E'])],
        ]);
    }

    private function syncProfile(User $user, array $validated): void
    {
        $currentProfil = $user->profil;

        $user->profil()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'tingkat_literasi' => $validated['tingkat_literasi'] ?? $currentProfil?->tingkat_literasi ?? 'rendah',
                'tipe_budget' => $validated['tipe_budget'] ?? $currentProfil?->tipe_budget ?? 'ketat',
                'avatar' => $validated['avatar'] ?? $currentProfil?->avatar,
                'banner' => $validated['banner'] ?? $currentProfil?->banner,
                'nudge_aktif' => $validated['nudge_aktif'] ?? $currentProfil?->nudge_aktif ?? false,
                'status_verifikasi' => array_key_exists('status_verifikasi', $validated)
                    ? $validated['status_verifikasi']
                    : ($currentProfil?->status_verifikasi ?? false),
                'usia' => $validated['usia'] ?? $currentProfil?->usia,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? $currentProfil?->jenis_kelamin,
                'universitas' => $validated['universitas'] ?? $currentProfil?->universitas,
                'nim' => $validated['nim'] ?? $currentProfil?->nim,
                'kelompok_eksperimen' => $validated['kelompok_eksperimen'] ?? $currentProfil?->kelompok_eksperimen,
            ]
        );
    }
}
