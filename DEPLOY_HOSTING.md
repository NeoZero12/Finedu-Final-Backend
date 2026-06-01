# Deploy Hosting FinEdu+

Panduan ini menyiapkan FinEdu+ agar fitur OTP email untuk daftar dan lupa password berjalan di hosting tanpa mengubah struktur utama website.

## 1. Environment yang wajib diisi

Salin `.env.example` menjadi `.env`, lalu sesuaikan minimal bagian berikut:

```env
APP_NAME="FinEdu+"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD=password_database

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinganda.com
MAIL_PORT=587
MAIL_USERNAME=noreply@domainanda.com
MAIL_PASSWORD=password-email-anda
MAIL_FROM_ADDRESS="noreply@domainanda.com"
MAIL_FROM_NAME="${APP_NAME}"

FINEDU_UNIVERSITY_NAME="Universitas Brawijaya"
FINEDU_ALLOWED_EMAIL_DOMAIN=student.ub.ac.id
FINEDU_OTP_EXPIRES_MINUTES=10
```

Catatan:
- Jika `MAIL_MAILER=log`, OTP tidak masuk inbox. OTP hanya tercatat di log Laravel.
- Pastikan email `MAIL_FROM_ADDRESS` benar-benar aktif dan diizinkan oleh SMTP hosting.
- Contoh setup Gmail SMTP dan SMTP hosting biasa tersedia di [SMTP_SETUP.md](/C:/laragon/www/finedu-/SMTP_SETUP.md).

## 2. Command setelah upload ke hosting

Jalankan:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 3. Folder yang harus writable

Pastikan folder berikut bisa ditulis:

- `storage/`
- `bootstrap/cache/`

## 4. Checklist OTP email

- Registrasi hanya menerima email `@student.ub.ac.id`
- OTP daftar dikirim ke email mahasiswa
- OTP lupa password dikirim ke email mahasiswa
- Reset password membutuhkan OTP yang valid
- Masa aktif OTP mengikuti `FINEDU_OTP_EXPIRES_MINUTES`

## 5. Uji setelah deploy

Lakukan pengujian berikut:

1. Coba daftar dengan email non-UB, harus ditolak.
2. Coba daftar dengan email `@student.ub.ac.id`, kirim OTP, lalu selesaikan registrasi.
3. Coba lupa password, kirim OTP, lalu reset password.
4. Pastikan email OTP benar-benar masuk inbox, bukan hanya log server.

## 6. Tes SMTP langsung

Setelah `.env` diisi, Anda juga bisa tes SMTP langsung tanpa registrasi:

```bash
php artisan mail:test-otp alamat_tujuan@example.com
```
