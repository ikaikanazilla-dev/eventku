# Sumber Gambar Eventku

Semua foto di bawah ini diunduh ke `storage/app/public/events/` pada 26 Oktober 2026, dipotong ke ukuran 1600×900, dan disimpan sebagai JPEG lokal. Website tidak melakukan hotlink ke URL sumber.

Lisensi sumber: [Unsplash License](https://unsplash.com/license). Lisensi Unsplash mengizinkan penggunaan gratis untuk kebutuhan komersial dan non-komersial; atribusi tidak diwajibkan, tetapi tautan sumber dipertahankan sebagai bentuk penghargaan kepada kontributor.

| File lokal | Event/area | Sumber asli |
| --- | --- | --- |
| `indonesia-tech-summit.jpg` | Indonesia Tech Summit 2026 | https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `ui-ux-masterclass.jpg` | UI/UX Masterclass 2026 | https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `tech-ai-business.jpg` | AI untuk Bisnis Indonesia | https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `digital-marketing-umkm.jpg` | Strategi Konten Digital UMKM | https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `startup-founder.jpg` | Founder Playbook Nusantara | https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `ui-ux-design.jpg` | Product Design Lab | https://images.unsplash.com/photo-1558655146-d09347e92766?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `career-digital.jpg` | Career Switch ke Industri Digital | https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `street-photography.jpg` | Jakarta Street Photography Walk | https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `festival-kuliner.jpg` | Rasa Nusantara Weekend | https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `creative-community.jpg` | Creative Community Night | https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `pendidikan-inklusif.jpg` | Kelas Mengajar Inklusif | https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `financial-planning.jpg` | Financial Planning Profesional Muda | https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |
| `hero-conference.jpg` | Hero homepage | https://images.unsplash.com/photo-1531058020387-3be344556be6?auto=format&fit=crop&w=1600&h=900&q=82&fm=jpg |

## Verifikasi lokal

- MIME semua file: `image/jpeg`.
- Dimensi semua file: `1600×900`.
- File poster disimpan pada disk Laravel `public` dan diakses melalui `/storage/events/...`.
- `public/storage` adalah link ke `storage/app/public`; jalankan `php artisan storage:link` jika link belum tersedia pada mesin baru.
- Pemetaan slug event dilakukan oleh `Database\Seeders\EventPosterSeeder`, yang hanya memperbarui event yang sudah ada dan masih memakai poster demo lama atau `NULL`.
