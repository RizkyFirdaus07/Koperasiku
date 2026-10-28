# KoperasiKu

Sistem informasi koperasi simpan pinjam (Laravel 12 + AdminLTE 3).

Modul yang sudah ada: login & role, dashboard, CRUD anggota, transaksi simpanan
(dengan verifikasi/penolakan dan rekap), manajemen user (admin).
Tabel `pinjaman` dan `angsuran` sudah ada di migration, tetapi belum ada
halamannya.

## Menjalankan

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite      # DB_CONNECTION=sqlite (bawaan)
php artisan migrate --seed
php artisan serve
```

Buka http://127.0.0.1:8000. Halaman memakai AdminLTE dari CDN, jadi `npm run dev` tidak wajib.

## Akun contoh (hasil seeder)

| Role      | Email                    | Password |
|-----------|--------------------------|----------|
| admin     | admin@koperasi.test      | password |
| pengurus  | pengurus@koperasi.test   | password |
| bendahara | bendahara@koperasi.test  | password |

**Ganti password ini sebelum dipakai sungguhan** (menu Manajemen User).

## Hak akses

| Fitur                              | admin | pengurus | bendahara | anggota |
|------------------------------------|:-----:|:--------:|:---------:|:-------:|
| Dashboard                          |  ✔   |    ✔    |     ✔    |    ✔   |
| Lihat anggota                      |  ✔   |    ✔    |     ✔    |         |
| Tambah/ubah/hapus anggota          |  ✔   |    ✔    |           |         |
| Simpanan: catat, edit, batalkan    |  ✔   |    ✔    |     ✔    |         |
| Simpanan: verifikasi / tolak       |  ✔   |    ✔    |     ✔    |         |
| Manajemen user                     |  ✔   |          |           |         |

## Aturan penting

- Simpanan baru berstatus `menunggu`. Hanya yang `menunggu` yang boleh diedit/dibatalkan.
- "Hapus" simpanan hanya mengubah status menjadi `dibatalkan` (jejak tetap ada).
- Anggota yang punya simpanan/pinjaman tidak bisa dihapus (ubah ke nonaktif).
- Rekap hanya menghitung simpanan `terverifikasi`.

## Route

Resource memakai bentuk jamak sesuai menu AdminLTE: `anggotas.*`, `simpanans.*`, `users.*`.
Cek dengan `php artisan route:list`.

## Tes

```bash
php artisan test
```
