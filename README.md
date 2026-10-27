# KoperasiKu — Modul Anggota, Simpanan, dan AdminLTE

Berkas di sini adalah **overlay** untuk proyek Laravel 11/12 baru (struktur folder sama
dengan proyek Laravel). Salin isinya ke proyek Anda dan timpa bila diminta.

## Langkah pemasangan (di terminal VS Code)

```bash
# 1. Buat proyek + login (Breeze menyediakan route login/logout dan routes/auth.php)
composer create-project laravel/laravel koperasiku
cd koperasiku
composer require laravel/breeze --dev
php artisan breeze:install blade

# 2. Atur .env (DB_CONNECTION, DB_DATABASE, dst.), lalu salin semua berkas dari folder ini
#    ke dalam proyek (timpa routes/web.php, layouts/app.blade.php, DatabaseSeeder.php).

# 3. Tambahkan 'role' ke $fillable di app/Models/User.php:
#    protected $fillable = ['name', 'email', 'password', 'role'];

# 4. Jalankan
php artisan migrate --seed
php artisan optimize:clear
php artisan route:list --path=anggotas
php artisan route:list --path=simpanans
php artisan serve
```

Akun contoh (password: `password`):

| Email                    | Role      |
|--------------------------|-----------|
| admin@koperasi.test      | admin     |
| bendahara@koperasi.test  | bendahara |
| petugas@koperasi.test    | petugas   |

## Penyesuaian terhadap modul asli

- **Nama route** memakai `anggotas.*` dan `simpanans.*` mengikuti layout AdminLTE
  (modul Anggota/Simpanan memakai `anggota.*`/`simpanan.*`; keduanya tidak bisa dipakai bersamaan).
- **View dikonversi ke Bootstrap 4** karena AdminLTE 3 memakai Bootstrap 4
  (`form-select`→`form-control`, `text-bg-*`→`badge-*`, `btn-close`→`close`, dst.).
- **Pagination** memakai `links('pagination::bootstrap-4')`.
- **Alert sukses/error** hanya di layout, tidak diulang di tiap view.
- **Verifikasi/tolak** dijaga `RoleMiddleware` (admin, pengurus, bendahara) sesuai catatan modul.
  Middleware dipanggil lewat nama class, jadi tidak perlu mengubah `bootstrap/app.php`.
- **Anggota::destroy** melewati pengecekan pinjaman selama model `Pinjaman` belum ada.
- **SimpananController::index**: query paginate memakai `clone` agar `ORDER BY` tidak masuk ke
  query rekap (bisa error di MySQL `ONLY_FULL_GROUP_BY`).
- **SimpananController::update**: kondisi `orWhere` pada validasi anggota dikelompokkan agar
  tidak meloloskan ID sembarangan.
- File `anggota/create.blade.php` (di modul tertulis `index.blade.php` pada langkah E).
- Ditambahkan `UserController` minimal (daftar user + ubah role) agar menu Manajemen User berfungsi,
  dan migration kolom `role` pada tabel `users`.

## Catatan

- Halaman profil bawaan Breeze (`<x-app-layout>`) tidak kompatibel dengan layout AdminLTE ini.
  Hapus menu/route profil Breeze atau abaikan.
- Jika `php artisan make:controller ... --resource` dipakai, ingat opsinya `--resource` (dua tanda minus).
- Transaksi terverifikasi tidak bisa diedit/dibatalkan langsung. Koreksi dilakukan lewat
  transaksi pembalik (belum ada di modul ini).
