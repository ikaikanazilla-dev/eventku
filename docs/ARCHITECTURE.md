# Arsitektur Eventku

Eventku menggunakan Laravel 13 monolith dengan Blade, Vite, Tailwind CSS v4, dan session authentication. Data bisnis berada pada Eloquent models dan seluruh operasi kritis dipisahkan ke service:

- `RegistrationService` memakai transaction dan `lockForUpdate` pada event/ticket type sebelum validasi kuota, membuat registrasi, dan menerbitkan tiket.
- `CheckInService` adalah jalur bersama QR/manual. Service mengunci tiket, memvalidasi event, status, token hash, waktu, kewenangan organizer, dan mencatat check-in.
- `ActivityLogger` menulis audit trail tanpa password atau token mentah.
- Middleware `role` dan `active` membatasi route; policies melindungi event, registrasi, dan tiket dari IDOR.

Upload poster memakai disk `public` dengan validasi MIME/ukuran. QR mengandung `ticket_code` acak (bukan ID database) dan backend memvalidasi hash token. PDF dibuat melalui TCPDF langsung (tanpa wrapper Laravel yang belum mendukung Illuminate 13) dan QR yang sama ditulis ulang dari kode tiket database.

