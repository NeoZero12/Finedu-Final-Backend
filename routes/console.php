<?php

use App\Mail\OtpCodeMail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test-otp {email}', function (string $email) {
    Mail::to($email)->send(
        new OtpCodeMail(
            '123456',
            'Tes SMTP FinEdu+',
            (int) config('finedu.otp_expires_minutes', 10),
        )
    );

    $this->info("Email tes OTP berhasil diproses untuk {$email}.");
})->purpose('Kirim email tes OTP untuk memverifikasi SMTP FinEdu+');
