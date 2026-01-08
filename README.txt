# Sistem Pembayaran SPP SMP Kartini

## Setup
1. Import `db_kartini.sql` ke phpMyAdmin.
2. Copy folder `spp_smpkartini` ke `htdocs` (XAMPP) atau `www` (WAMP).
3. Buat folder `uploads` dan beri permission write.
4. Sesuaikan `config/koneksi.php` jika diperlukan.

## Penggunaan
- **Admin**: login dengan akun di tabel `users` (level=admin).
- **Siswa**: login dengan akun di tabel `users` (level=siswa).
- Siswa upload bukti transfer di form bayar.
- Admin verifikasi di menu Verifikasi Pembayaran.
- Setelah approve status menjadi LUNAS.
