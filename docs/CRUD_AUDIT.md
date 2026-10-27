# Matriks Audit CRUD Eventku

Tanggal audit: 19 Oktober 2026

| Entitas / role | Create | Read | Update | Delete | Authorization dan validasi | Test aktual |
|---|---|---|---|---|---|---|
| Event / organizer | `POST organizer.event.store` membuat event `draft` + ticket type dalam transaction | `organizer.events`, `organizer.event.show` hanya event milik organizer | `PUT organizer.event.update`; policy menolak event `published`/`completed`; poster lama dihapus setelah update DB berhasil | Tidak tersedia dan tidak diizinkan: FK memakai `restrictOnDelete` agar histori tiket/registrasi/check-in tetap aman | `role:organizer`, `StoreEventRequest`, ownership policy, validasi tanggal/kategori/file | Create, invalid payload, edit, update, poster valid/invalid, ownership |
| Review event / admin | Tidak membuat event | `admin.events`, `admin.event.show` | Approve/reject hanya status `pending`; alasan reject tervalidasi | Tidak tersedia; histori audit dipertahankan | `role:admin` + `EventPolicy::approve` | Detail admin, approve, guard non-pending |
| Kategori / admin | `POST admin.category.store` | `GET admin.categories` | Tidak ada route update dalam scope saat ini | Tidak ada route delete; kategori yang direferensikan event dilindungi FK | `role:admin`, validasi nama/deskripsi | List/create kategori |
| Pengguna / admin | Registrasi publik membuat participant | `GET admin.users` | Toggle status dan ubah role; admin tidak dapat mengubah dirinya sendiri | Tidak tersedia untuk mencegah penghapusan akun/audit | `role:admin`, self-protection, validasi role | Ubah role/status |
| Registrasi & tiket / participant | `POST participant.event.register` membuat registration + digital ticket dalam transaction | Dashboard, daftar tiket, detail tiket, PDF, QR | Tidak ada update manual; status tiket berubah melalui check-in | Tidak ada delete/cancel pada versi bisnis ini; histori tetap tersimpan | Role, ownership policy, unique user-event, lock kuota, QR hash | Registration, duplicate, quota, QR, PDF, ownership |
| Check-in / organizer | `POST organizer.checkin.store` mencatat success/rejected | Monitor registrasi dan laporan | Status tiket menjadi `used` hanya sekali | Tidak tersedia; log check-in immutable | Event ownership policy, token QR, waktu event, transaction lock, unique successful ticket | Success, double check-in, owner |

Catatan delete: tidak adanya route delete untuk event, kategori, pengguna, registrasi, tiket, dan check-in adalah keputusan integritas data pada skema saat ini, bukan tombol UI yang disembunyikan. Penghapusan event akan bertentangan dengan foreign key `restrictOnDelete` dan kebutuhan audit historis.
