<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP FinEdu+</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f3ff;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:560px;margin:0 auto;padding:32px 20px;">
        <div style="background:#ffffff;border-radius:20px;padding:32px;border:1px solid #ede9fe;">
            <p style="margin:0 0 8px;font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#8b5cf6;">FinEdu+</p>
            <h1 style="margin:0 0 16px;font-size:24px;line-height:1.3;color:#1e1b4b;">Kode OTP {{ $label }}</h1>
            <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#475569;">
                Gunakan kode OTP berikut untuk melanjutkan proses {{ strtolower($label) }}.
            </p>

            <div style="margin:0 0 20px;padding:18px 20px;border-radius:16px;background:#f5f3ff;border:1px dashed #c4b5fd;text-align:center;">
                <span style="font-size:30px;font-weight:700;letter-spacing:0.35em;color:#312e81;">{{ $code }}</span>
            </div>

            <p style="margin:0 0 10px;font-size:14px;line-height:1.7;color:#475569;">
                Kode ini berlaku selama <strong>{{ $expiresInMinutes }} menit</strong>.
            </p>
            <p style="margin:0;font-size:13px;line-height:1.7;color:#64748b;">
                Jika Anda tidak meminta OTP ini, abaikan email ini. Jangan bagikan kode ke siapa pun.
            </p>
        </div>
    </div>
</body>
</html>
