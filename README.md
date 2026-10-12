# CBT Sekolah — CodeIgniter 4 (Paket Pengembangan Lengkap)

Paket aplikasi Computer Based Test berbasis **CodeIgniter 4 + MySQL/MariaDB**, dengan branding awal **MIN 1 Tanggamus** dan tagline *Madrasah Maju, Bermutu, dan Mendunia*. Identitas sekolah dapat diganti melalui konfigurasi/tampilan.

## Modul dalam paket
- Login session dan role Admin, Guru, Siswa.
- Dashboard sesuai peran.
- Manajemen pengguna: tambah, ubah peran/nama, aktif/nonaktif, reset password.
- Data kelas/rombel dan mata pelajaran.
- Manajemen ujian, jadwal, durasi, status publikasi, token/PIN ujian.
- Bank soal pilihan ganda dan esai.
- Snapshot urutan soal dan opsi per percobaan untuk mengacak soal/opsi dengan penilaian tetap akurat.
- Timer dan validasi batas waktu server.
- Autosave jawaban, submit ujian, nilai otomatis pilihan ganda, penilaian esai.
- Monitoring status percobaan dan rekap hasil.
- Ekspor rekap nilai ke CSV.
- Log aktivitas dasar dan layout responsif.

## Persyaratan
- PHP 8.1+ (sesuaikan dengan versi CodeIgniter 4 yang digunakan)
- Composer
- MySQL/MariaDB
- Ekstensi PHP `intl`, `mbstring`, `mysqli`/`pdo_mysql`, `json`, `curl`

## Instalasi
1. Buat proyek induk CI4:
   ```bash
   composer create-project codeigniter4/appstarter cbt-sekolah
   ```
2. Gabungkan folder `app/` dari ZIP ini ke proyek tersebut.
3. Buat database, lalu impor `database/cbt_schema.sql`.
4. Salin `env` bawaan CI4 menjadi `.env`, lalu isi konfigurasi:
   ```ini
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost:8080/'
   database.default.hostname = localhost
   database.default.database = cbt_sekolah
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   database.default.port = 3306
   ```
5. Buat hash password:
   ```bash
   php -r "echo password_hash('GantiPassword123!', PASSWORD_DEFAULT), PHP_EOL;"
   ```
   Masukkan hash itu ke SQL untuk akun admin awal. Jangan simpan password teks biasa.
6. Jalankan `php spark serve`, lalu buka `http://localhost:8080/login`.

## Membuat akun admin pertama
Jalankan SQL berikut setelah mengganti `TEMPEL_HASH_DI_SINI` dengan hash hasil langkah 5:
```sql
INSERT INTO users (username,password_hash,full_name,role,is_active,created_at,updated_at)
VALUES ('admin','TEMPEL_HASH_DI_SINI','Administrator CBT','admin',1,NOW(),NOW());
```

## Catatan penerapan
- Uji di staging sebelum dipakai untuk ujian resmi. Paket ini belum diuji pada server hosting pengguna.
- Ubah `CI_ENVIRONMENT` menjadi `production` setelah konfigurasi, gunakan HTTPS, password kuat, backup terjadwal, dan batasi akses database.
- Aktifkan CSRF filter dan konfigurasi session cookies secure di deployment HTTPS.
- Anti-kecurangan browser tidak dapat menjamin peserta tidak memakai perangkat kedua, kamera, atau tangkapan layar. Monitoring dan log hanya membantu pengawasan.
- Sebelum ujian berisiko tinggi, lakukan pengujian beban, uji jaringan terputus, backup/restore, serta audit keamanan.
