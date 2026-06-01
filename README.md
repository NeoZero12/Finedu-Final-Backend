# Backend FinEdu+

Backend ini berisi API Laravel untuk autentikasi, profil, modul belajar, kuesioner, simulasi belanja, laporan, dan dashboard admin.

## Catatan singkat

- Database lokal memakai `finedu_db`.
- Akun baru tidak langsung punya progress, skor, atau simulasi.
- Progress modul dibuat saat pengguna menekan selesai dibaca.
- Nilai kuesioner disimpan per modul dan bisa diulang maksimal dua kali.
- Data awal admin, mahasiswa contoh, modul, dan produk simulasi dibuat dari seeder.

## Jalankan

```bash
php artisan serve
```
