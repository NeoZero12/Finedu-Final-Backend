<?php

namespace App\Http\Controllers;

use App\Mail\OtpCodeMail;
use App\Models\Profil;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $universityName = config('finedu.university_name');
        $allowedDomain = config('finedu.allowed_email_domain');

        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'otp_code' => 'required|digits:6',
                'usia' => 'required|integer|min:17|max:30',
                'jenis_kelamin' => ['required', Rule::in(['laki-laki', 'perempuan'])],
                'universitas' => ['required', 'string', Rule::in([$universityName])],
                'nim' => 'required|string|max:50',
                'informed_consent' => 'accepted',
            ]);

            if (! $this->isUbStudentEmail($request->email)) {
                return response()->json([
                    'message' => [
                        'email' => ["Pendaftaran hanya dapat menggunakan email mahasiswa {$universityName} (@{$allowedDomain})."],
                    ],
                ], 422);
            }

            if (! $this->consumeOtp($request->email, $request->otp_code, 'register')) {
                return response()->json([
                    'message' => [
                        'otp_code' => ['Kode OTP pendaftaran tidak valid atau sudah kedaluwarsa.'],
                    ],
                ], 422);
            }

            return DB::transaction(function () use ($request, $universityName) {
                $budgetKetat = random_int(0, 1) === 0;

                $user = User::create([
                    'name' => $request->nama,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'mahasiswa',
                    'email_verified_at' => now(),
                ]);

                // Akun baru belum punya baris progress, skor, atau simulasi.
                // Data itu baru dibuat saat pengguna benar-benar mulai belajar.
                Profil::create([
                    'user_id' => $user->id,
                    'tingkat_literasi' => 'rendah',
                    'tipe_budget' => $budgetKetat ? 'ketat' : 'longgar',
                    'nudge_aktif' => $budgetKetat,
                    'informed_consent' => true,
                    'status_verifikasi' => false,
                    'usia' => $request->usia,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'universitas' => $universityName,
                    'nim' => $request->nim,
                    'kelompok_eksperimen' => null,
                    'kode_sertifikat' => 'FINEDU-'.Str::upper(Str::random(8)),
                ]);

                return response()->json([
                    'message' => 'Registrasi berhasil',
                    'token' => $user->createToken('auth_token')->plainTextToken,
                    'role' => $user->role,
                    'nama' => $user->name,
                ], 201);
            });
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server: '.$e->getMessage(),
            ], 500);
        }
    }

    public function sendRegisterOtp(Request $request)
    {
        $universityName = config('finedu.university_name');
        $allowedDomain = config('finedu.allowed_email_domain');
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        if (! $this->isUbStudentEmail($validated['email'])) {
            return response()->json([
                'message' => "Kode OTP hanya dapat dikirim ke email mahasiswa {$universityName} (@{$allowedDomain}).",
            ], 422);
        }

        $this->issueOtp($validated['email'], 'register');

        return response()->json([
            'message' => 'Kode OTP pendaftaran telah dikirim ke email mahasiswa Anda.',
        ]);
    }

    public function sendPasswordResetOtp(Request $request)
    {
        $universityName = config('finedu.university_name');
        $allowedDomain = config('finedu.allowed_email_domain');
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        if (! $this->isUbStudentEmail($validated['email'])) {
            return response()->json([
                'message' => "Reset password hanya tersedia untuk email mahasiswa {$universityName} (@{$allowedDomain}).",
            ], 422);
        }

        $this->issueOtp($validated['email'], 'reset_password');

        return response()->json([
            'message' => 'Kode OTP reset password telah dikirim ke email mahasiswa Anda.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $universityName = config('finedu.university_name');
        $allowedDomain = config('finedu.allowed_email_domain');
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ]);

        if (! $this->isUbStudentEmail($validated['email'])) {
            return response()->json([
                'message' => "Reset password hanya tersedia untuk email mahasiswa {$universityName} (@{$allowedDomain}).",
            ], 422);
        }

        if (! $this->consumeOtp($validated['email'], $validated['otp_code'], 'reset_password')) {
            return response()->json([
                'message' => 'Kode OTP reset password tidak valid atau sudah kedaluwarsa.',
            ], 422);
        }

        $user = User::where('email', $validated['email'])->firstOrFail();
        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password berhasil diperbarui. Silakan login dengan password baru.',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first()?->load('profil');

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Akun Anda sedang dinonaktifkan oleh admin.'], 403);
        }

        $user->tokens()->delete();

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $user->createToken('auth_token')->plainTextToken,
            'role' => $user->role,
            'nama' => $user->name,
            'profile_complete' => (bool) $user->profil?->status_verifikasi,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil logout',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(
            $request->user()->load('profil')
        );
    }

    private function isUbStudentEmail(string $email): bool
    {
        $allowedDomain = strtolower((string) config('finedu.allowed_email_domain', 'student.ub.ac.id'));

        return str_ends_with(strtolower(trim($email)), '@'.$allowedDomain);
    }

    private function issueOtp(string $email, string $purpose): void
    {
        $plainCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresInMinutes = (int) config('finedu.otp_expires_minutes', 10);

        DB::table('email_otps')->updateOrInsert(
            ['email' => $email, 'purpose' => $purpose],
            [
                'code' => Hash::make($plainCode),
                'expires_at' => Carbon::now()->addMinutes($expiresInMinutes),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $labels = [
            'register' => 'Pendaftaran Akun FinEdu+',
            'reset_password' => 'Reset Password FinEdu+',
        ];

        Mail::to($email)->send(
            new OtpCodeMail(
                $plainCode,
                $labels[$purpose] ?? 'OTP FinEdu+',
                $expiresInMinutes,
            )
        );
    }

    private function consumeOtp(string $email, string $otpCode, string $purpose): bool
    {
        $record = DB::table('email_otps')
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->first();

        if (! $record) {
            return false;
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            DB::table('email_otps')->where('id', $record->id)->delete();

            return false;
        }

        if (! Hash::check($otpCode, $record->code)) {
            return false;
        }

        DB::table('email_otps')->where('id', $record->id)->delete();

        return true;
    }
}
