# UTS Praktikum Pemrograman Web
## Tipe D — Sistem Data Kegiatan Mahasiswa

### Identitas Mahasiswa
- Nama: Alya Noviyani
- NIM: 2401020042
- Mata Kuliah: Praktikum Pemrograman Web
- Kode Mata Kuliah: INF11197
- Tipe Soal: D — Sistem Data Kegiatan Mahasiswa

## 1. Deskripsi Aplikasi

Sistem Data Kegiatan Mahasiswa merupakan aplikasi berbasis web yang dibuat menggunakan Laravel, PHP, MySQL, PDO, dan Blade.

Aplikasi ini digunakan untuk mengelola data kegiatan mahasiswa berdasarkan jenis kegiatan. Pengguna dapat menambahkan, menampilkan, mengubah, menghapus, mencari, dan memfilter data kegiatan.

## 2. Teknologi yang Digunakan

- Laravel
- PHP
- MySQL atau MariaDB
- PDO (PHP Data Objects)
- Blade Template
- Git dan GitHub

Tampilan aplikasi menggunakan HTML sederhana tanpa CSS, JavaScript, Bootstrap, atau Tailwind sesuai ketentuan soal UTS.

## 3. Fitur Aplikasi

1. **Read:** Menampilkan daftar kegiatan mahasiswa beserta jenis kegiatannya.
2. **Create:** Menambahkan kegiatan baru melalui form.
3. **Update:** Mengubah data kegiatan berdasarkan ID.
4. **Delete:** Menghapus kegiatan berdasarkan ID.
5. **Pencarian:** Mencari berdasarkan kode kegiatan, nama kegiatan, atau email PIC.
6. **Filter:** Menampilkan kegiatan berdasarkan jenis kegiatan.
7. **Validasi:** Memeriksa input dan menolak kode kegiatan atau email PIC yang duplikat.
8. **Status Kuota:** Menampilkan status `Besar` untuk kuota minimal 75 dan `Terbatas` untuk kuota di bawah 75.
9. **Error Handling:** Menangani kesalahan database menggunakan try-catch dan report().
10. **Keamanan:** Menggunakan PDO prepared statement, validasi server-side, dan CSRF protection.

## 4. Struktur Database

Nama database: `kegiatan_mahasiswa_2401020042`

Database terdiri dari dua tabel yang berelasi satu-ke-banyak.

### Tabel jenis_kegiatan

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED | Primary Key, Auto Increment |
| nama_jenis | VARCHAR(100) | NOT NULL, UNIQUE |

### Tabel kegiatan

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED | Primary Key, Auto Increment |
| kode_kegiatan | CHAR(10) | NOT NULL, UNIQUE |
| nama_kegiatan | VARCHAR(100) | NOT NULL |
| email_pic | VARCHAR(100) | NOT NULL, UNIQUE |
| kuota | SMALLINT UNSIGNED | CHECK 10–200 |
| jenis_kegiatan_id | BIGINT UNSIGNED | Foreign Key |
| created_at | TIMESTAMP | Waktu pembuatan |
| updated_at | TIMESTAMP | Waktu perubahan |

Relasi database:

`jenis_kegiatan (1) → (N) kegiatan`

Foreign key menggunakan `ON UPDATE CASCADE` dan `ON DELETE RESTRICT`.

## 5. Data Awal

| Kode | Nama Kegiatan | Email PIC | Kuota | Jenis |
|---|---|---|---|---|
| KG00000001 | Pelatihan Web | web@kampus.ac.id | 40 | Akademik |
| KG00000002 | Seminar Karier | karier@kampus.ac.id | 100 | Akademik |
| KG00000003 | Bakti Pesisir | pesisir@kampus.ac.id | 60 | Sosial |

## 6. Daftar Route

| Method | URL | Fungsi |
|---|---|---|
| GET | /kegiatan | Menampilkan data, pencarian, dan filter |
| POST | /kegiatan | Menambahkan kegiatan |
| GET | /kegiatan/{id}/edit | Menampilkan form edit |
| PUT | /kegiatan/{id} | Menyimpan perubahan |
| DELETE | /kegiatan/{id} | Menghapus kegiatan |

## 7. Cara Instalasi dan Menjalankan Aplikasi

1. Clone repository GitHub.
2. Masuk ke folder proyek.
3. Jalankan `composer install`.
4. Salin `.env.example` menjadi `.env`.
5. Atur koneksi MySQL pada `.env`.
6. Jalankan `php artisan key:generate`.
7. Buat database dan impor file `database/kegiatan.sql`.
8. Jalankan `php artisan config:clear`.
9. Jalankan `php artisan serve`.
10. Buka `http://127.0.0.1:8000/kegiatan`.

Contoh konfigurasi database pada `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kegiatan_mahasiswa_2401020042
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=file
```

Sesuaikan username dan password dengan konfigurasi MySQL lokal.

## 8. Validasi Input

- Kode kegiatan wajib diisi, tepat 10 karakter alfanumerik, dan unik.
- Nama kegiatan wajib diisi dengan panjang 3–100 karakter.
- Email PIC wajib menggunakan format email yang valid dan unik.
- Kuota wajib berupa bilangan bulat antara 10–200.
- Jenis kegiatan wajib dipilih dan tersedia dalam database.
- Input teks diproses menggunakan trim dan strip_tags.
- Form menggunakan @csrf, old(), dan pesan kesalahan validasi.

## 9. Keamanan Aplikasi

Aplikasi menggunakan PDO prepared statement dengan prepare() dan execute() untuk query yang menerima input pengguna.

Proses Update menggunakan metode PUT dan Delete menggunakan metode DELETE melalui method spoofing Laravel.

Kesalahan koneksi atau query ditangani menggunakan try-catch. Detail kesalahan dicatat menggunakan report(), sedangkan pengguna menerima pesan yang aman.

File `.env` tidak diunggah ke GitHub karena dapat berisi informasi rahasia.

## 10. Pengujian Aplikasi

Pengujian yang diwajibkan dalam soal UTS:

| No. | Pengujian | Hasil yang Diharapkan |
|---|---|---|
| 1 | Read | Tiga data awal tampil |
| 2 | Create valid | Data berhasil ditambahkan |
| 3 | Create tidak valid | Pesan kesalahan muncul dan input lama dipertahankan |
| 4 | Data duplikat | Kode atau email duplikat ditolak |
| 5 | Update | Data berhasil diubah |
| 6 | Konflik Update | Nilai unik milik data lain ditolak |
| 7 | Delete | Data berhasil dihapus |
| 8 | Pencarian | Pencarian bekerja pada tiga kolom |
| 9 | Filter | Data ditampilkan berdasarkan jenis kegiatan |
| 10 | Error Handling | Pesan aman tampil dan kesalahan dicatat |

Hasil aktual dan tangkapan layar pengujian didokumentasikan dalam laporan UTS.

## 11. Git dan GitHub

Proyek menggunakan Git untuk mencatat perubahan kode dan GitHub sebagai tempat penyimpanan repository.

Repository mencakup:
- Source code Laravel
- File SQL database
- File `.env.example`
- Dokumentasi README
- Minimal lima commit terstruktur
- Minimal satu GitHub Issue yang mendokumentasikan masalah, pemeriksaan, perbaikan, dan hasil uji ulang

## 12. Kesimpulan

Aplikasi Sistem Data Kegiatan Mahasiswa dikembangkan untuk menerapkan materi Praktikum Pemrograman Web Modul 1–7, meliputi pengolahan form, validasi, database relasional, PDO prepared statement, CRUD, pencarian, filter, error handling, dan version control menggunakan Git serta GitHub.