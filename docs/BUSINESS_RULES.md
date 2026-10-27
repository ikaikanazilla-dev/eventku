# Aturan Bisnis

- Semua ticket gratis; hanya event `published` dalam periode registrasi yang dapat didaftarkan.
- Event organizer dimulai sebagai `draft`, lalu `pending`, dan hanya admin yang dapat mengubahnya menjadi `published`.
- Penolakan menyimpan alasan. Organizer tidak bisa mengubah event milik organizer lain.
- Satu peserta hanya memiliki satu baris registrasi per event (`unique user_id + event_id`); pembatalan menyimpan histori dan tidak menyediakan re-register pada versi ini.
- Kuota dikunci dalam transaction; pendaftaran yang melewati kuota ditolak tanpa angka negatif.
- Registrasi sukses selalu menerbitkan satu `digital_ticket` dengan kode acak dan QR hash.
- Check-in sukses hanya dapat terjadi sekali karena status tiket dikunci dan diubah ke `used`, ditambah unique nullable `successful_ticket_id`; percobaan gagal tetap dicatat sebagai `rejected`.
- Akses tiket memakai policy ownership; role UI bukan satu-satunya kontrol keamanan.

