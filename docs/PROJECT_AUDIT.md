# Audit Proyek Eventku

Tanggal audit: 19 Oktober 2026

## Struktur awal

Pada awal pengerjaan, direktori proyek hanya berisi folder `desain/`. Belum ada instalasi Laravel, konfigurasi Composer/NPM, source code aplikasi, migration, route, atau test suite.

## Referensi desain yang ditemukan

- `desain/eventku_digital_platform/DESIGN.md` — design system Eventku: token warna, tipografi Inter, grid responsif, elevation, komponen, dan aturan tiket.
- `desain/eventku_beranda_temukan_event/code.html` dan `screen.png` — halaman beranda/event discovery desktop.
- `desain/detail_event_indonesia_tech_summit_2025/code.html` dan `screen.png` — halaman detail event dengan hero, agenda, pembicara, venue, dan panel pendaftaran.
- `desain/dashboard_penyelenggara_eventku/code.html` dan `screen.png` — dashboard penyelenggara dengan sidebar, statistik, grafik, tabel event, dan check-in.
- `desain/tiket_saya_digital_pass_qr_code/code.html` dan `screen.png` — halaman tiket digital dengan kartu tiket, QR, status, dan daftar tiket.

## Teknologi terdeteksi

- PHP 8.4.25 (`C:\Users\Desik\.config\herd\bin\php84\php.exe`)
- Composer 2.10.3
- Node.js 26.4.0
- npm 11.17.0
- Laravel belum terpasang pada saat audit
- MySQL/XAMPP belum diverifikasi koneksinya pada saat audit

## Fitur awal

Belum ada fitur aplikasi. Seluruh fitur Eventku akan dibangun berdasarkan brief utama dan referensi desain di atas.

## Rencana implementasi

1. Bootstrap Laravel 13 dan Vite/Tailwind.
2. Membuat schema, model, factory, seeder, policy, dan service untuk user, event, tiket, registrasi, check-in, dan audit log.
3. Membangun autentikasi session serta pembatasan akses role admin, organizer, dan participant.
4. Membuat halaman publik discovery/detail, dashboard role, CRUD event, tiket digital, QR check-in, dan laporan.
5. Menyamakan design system dengan referensi: Inter, navy/electric blue, surface lavender, card border halus, responsive layout.
6. Menambahkan test untuk alur inti dan dokumentasi operasional.

