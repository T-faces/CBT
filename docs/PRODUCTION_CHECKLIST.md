# Checklist sebelum ujian resmi

- [ ] Uji login untuk setiap peran dan pastikan pengguna nonaktif ditolak.
- [ ] Uji CSRF, session fixation, password policy, HTTPS, cookie secure, rate limiting.
- [ ] Uji pembuatan ujian, PIN, jadwal, kelas sasaran, batas percobaan.
- [ ] Uji opsi acak dengan kunci jawaban A/B/C/D; verifikasi skor setelah opsi ditampilkan dengan urutan berbeda.
- [ ] Uji autosave saat jaringan lambat/putus, reload, submit ganda, waktu habis.
- [ ] Uji peserta yang membuka dua tab/perangkat dan tentukan kebijakan sekolah.
- [ ] Uji penilaian esai dan rekap total nilai; saat ini rekap objektif belum menjumlahkan nilai esai ke satu total final.
- [ ] Uji ekspor CSV dan encoding Excel.
- [ ] Uji monitoring heartbeat; status adalah indikasi koneksi, bukan bukti kecurangan.
- [ ] Jalankan load test sesuai jumlah peserta, backup/restore database, dan audit akses data.
- [ ] Ubah environment ke production dan nonaktifkan Debug Toolbar pada production.
