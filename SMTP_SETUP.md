# Setup SMTP FinEdu+

Dokumen ini menyiapkan pengiriman OTP ke email asli tanpa mengubah struktur utama website.

## Opsi 1: Gmail SMTP

Yang dibutuhkan:
- 1 akun Gmail pengirim
- Verifikasi 2 langkah aktif
- App Password Gmail 16 digit

Isi `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=alamatgmailanda@gmail.com
MAIL_PASSWORD=app-password-16-digit
MAIL_FROM_ADDRESS="alamatgmailanda@gmail.com"
MAIL_FROM_NAME="FinEdu+"
```

Catatan:
- Jangan pakai password Gmail biasa.
- Yang dipakai adalah App Password dari akun Google.

## Opsi 2: SMTP Hosting / cPanel

Yang dibutuhkan:
- Email aktif di hosting, misalnya `noreply@domainanda.com`
- Host SMTP dari provider
- Port SMTP
- Username dan password email

Isi `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=mail.domainanda.com
MAIL_PORT=587
MAIL_USERNAME=noreply@domainanda.com
MAIL_PASSWORD=password-email
MAIL_FROM_ADDRESS="noreply@domainanda.com"
MAIL_FROM_NAME="FinEdu+"
```

Jika provider memakai SSL 465:

```env
MAIL_SCHEME=ssl
MAIL_PORT=465
```

## Setting OTP Kampus

Pastikan bagian ini tetap ada:

```env
FINEDU_UNIVERSITY_NAME="Universitas Brawijaya"
FINEDU_ALLOWED_EMAIL_DOMAIN=student.ub.ac.id
FINEDU_OTP_EXPIRES_MINUTES=10
```

## Setelah ubah `.env`

Jalankan:

```bash
php artisan optimize:clear
php artisan config:cache
```

## Tes kirim email

Untuk mengetes SMTP tanpa registrasi penuh:

```bash
php artisan mail:test-otp alamat_tujuan@example.com
```

Jika berhasil, email tes OTP akan masuk ke inbox tujuan.

## Jika email tidak masuk

Cek hal berikut:
- `MAIL_MAILER` sudah `smtp`
- `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD` benar
- `MAIL_FROM_ADDRESS` sesuai akun SMTP
- Firewall hosting tidak memblokir koneksi SMTP
- Folder `storage/` writable
- Lihat error detail di `storage/logs/laravel.log`
