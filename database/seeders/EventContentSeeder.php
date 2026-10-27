<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class EventContentSeeder extends Seeder
{
    /**
     * Seed the curated public event catalogue without touching existing events.
     */
    public function run(): void
    {
        $admin = User::query()
            ->where('role', 'admin')
            ->where('status', 'active')
            ->firstOrFail();
        $organizer = User::query()
            ->where('role', 'organizer')
            ->where('status', 'active')
            ->firstOrFail();

        $categories = collect([
            ['name' => 'Teknologi & AI', 'slug' => 'teknologi-ai'],
            ['name' => 'Pemasaran Digital', 'slug' => 'pemasaran-digital'],
            ['name' => 'Startup & Bisnis', 'slug' => 'startup-bisnis'],
            ['name' => 'Desain Kreatif', 'slug' => 'desain-kreatif'],
            ['name' => 'Karier & Profesional', 'slug' => 'karier-profesional'],
            ['name' => 'Fotografi & Visual', 'slug' => 'fotografi-visual'],
            ['name' => 'Kuliner', 'slug' => 'kuliner'],
            ['name' => 'Komunitas Kreatif', 'slug' => 'komunitas-kreatif'],
            ['name' => 'Pendidikan', 'slug' => 'pendidikan'],
            ['name' => 'Keuangan Pribadi', 'slug' => 'keuangan-pribadi'],
        ])->mapWithKeys(function (array $category) {
            return [$category['slug'] => Category::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name'], 'is_active' => true],
            )];
        });

        $baseDate = Carbon::now(config('app.timezone'))->startOfDay()->addDays(19);
        $events = [
            [
                'slug' => 'ai-untuk-bisnis-indonesia-2026',
                'category' => 'teknologi-ai',
                'name' => 'AI untuk Bisnis Indonesia: Dari Ide ke Implementasi',
                'description' => "Forum praktis untuk pemilik bisnis dan pemimpin tim yang ingin memahami penggunaan AI secara bertanggung jawab.\n\nPeserta akan mempelajari pemetaan proses kerja, pemilihan use case, keamanan data, serta cara mengukur dampak implementasi tanpa terjebak tren sesaat.",
                'location' => 'Auditorium Menara Karya, Kuningan, Jakarta',
                'days' => 19,
                'start' => '09:00',
                'end' => '16:30',
                'ticket' => 'Pass Konferensi AI',
                'quota' => 350,
                'poster' => 'events/tech-ai-business.jpg',
            ],
            [
                'slug' => 'strategi-konten-digital-umkm-2026',
                'category' => 'pemasaran-digital',
                'name' => 'Strategi Konten Digital untuk UMKM yang Tumbuh',
                'description' => "Lokakarya sehari untuk menyusun strategi konten yang konsisten, terukur, dan sesuai kapasitas tim kecil.\n\nMateri mencakup riset audiens, kalender editorial, copywriting singkat, produksi konten sederhana, dan evaluasi performa kanal digital.",
                'location' => 'Ruang Kolaborasi Lantai 3, Bandung',
                'days' => 26,
                'start' => '09:30',
                'end' => '15:30',
                'ticket' => 'Tiket Workshop Konten',
                'quota' => 90,
                'poster' => 'events/digital-marketing-umkm.jpg',
            ],
            [
                'slug' => 'founder-playbook-nusantara-2026',
                'category' => 'startup-bisnis',
                'name' => 'Founder Playbook Nusantara: Validasi, Operasi, dan Pertumbuhan',
                'description' => "Sesi berbagi pengalaman untuk calon founder dan pengelola usaha rintisan tahap awal.\n\nBahasan utama meliputi validasi masalah, wawancara pelanggan, pengelolaan arus kas, pembagian peran pendiri, dan pengambilan keputusan saat sumber daya terbatas.",
                'location' => 'Kedai Kopi Kolektif, Surabaya',
                'days' => 33,
                'start' => '13:00',
                'end' => '17:00',
                'ticket' => 'Pass Founder Meetup',
                'quota' => 120,
                'poster' => 'events/startup-founder.jpg',
            ],
            [
                'slug' => 'product-design-lab-ui-ux-2026',
                'category' => 'desain-kreatif',
                'name' => 'Product Design Lab: UI, UX, dan Design System',
                'description' => "Workshop hands-on bagi desainer dan product builder yang ingin merapikan proses desain produk digital.\n\nPeserta akan berlatih menyusun user flow, merancang komponen antarmuka, mendokumentasikan design system, dan melakukan review dengan sudut pandang aksesibilitas.",
                'location' => 'Studio Reka, Kemang, Jakarta',
                'days' => 40,
                'start' => '09:00',
                'end' => '17:00',
                'ticket' => 'Tiket Product Design Lab',
                'quota' => 75,
                'poster' => 'events/ui-ux-design.jpg',
            ],
            [
                'slug' => 'career-switch-digital-2026',
                'category' => 'karier-profesional',
                'name' => 'Career Switch ke Industri Digital: Peta Jalan 90 Hari',
                'description' => "Seminar interaktif untuk profesional yang sedang menyiapkan perpindahan karier ke bidang produk, data, pemasaran, atau engineering.\n\nAgenda membahas pemetaan kompetensi, portofolio yang relevan, strategi networking, persiapan wawancara, dan rencana belajar yang realistis.",
                'location' => 'Online melalui Eventku Live',
                'days' => 47,
                'start' => '19:00',
                'end' => '21:00',
                'ticket' => 'Pass Career Switch',
                'quota' => 500,
                'poster' => 'events/career-digital.jpg',
            ],
            [
                'slug' => 'jakarta-street-photography-walk-2026',
                'category' => 'fotografi-visual',
                'name' => 'Jakarta Street Photography Walk: Cahaya, Ritme, Cerita',
                'description' => "Jelajah fotografi santai untuk memotret kehidupan kota dengan pendekatan observasional dan tetap menghormati ruang personal.\n\nSesi dimulai dengan briefing komposisi dan etika memotret, dilanjutkan walking tour, lalu ditutup dengan review karya bersama.",
                'location' => 'Titik temu: Taman Literasi Blok M, Jakarta',
                'days' => 54,
                'start' => '06:30',
                'end' => '10:00',
                'ticket' => 'Pass Photo Walk',
                'quota' => 35,
                'poster' => 'events/street-photography.jpg',
            ],
            [
                'slug' => 'rasa-nusantara-weekend-2026',
                'category' => 'kuliner',
                'name' => 'Rasa Nusantara Weekend: Cerita di Balik Rempah',
                'description' => "Perjumpaan kuliner yang mengajak peserta mengenal teknik, bahan, dan cerita budaya di balik hidangan rumahan dari berbagai daerah.\n\nTersedia demo masak, sesi mencicipi terarah, dan obrolan bersama pelaku usaha pangan lokal.",
                'location' => 'Halaman Kreatif Kota Lama, Semarang',
                'days' => 61,
                'start' => '10:00',
                'end' => '15:00',
                'ticket' => 'Tiket Rasa Nusantara',
                'quota' => 180,
                'poster' => 'events/festival-kuliner.jpg',
            ],
            [
                'slug' => 'creative-community-night-2026',
                'category' => 'komunitas-kreatif',
                'name' => 'Creative Community Night: Karya Kecil, Dampak Besar',
                'description' => "Malam temu komunitas untuk kreator lintas bidang yang ingin bertukar proses, membangun kolaborasi, dan menemukan cara berkontribusi di lingkungan sekitar.\n\nAcara berisi presentasi singkat, open mic karya, meja jejaring, dan sesi ideasi kolaboratif.",
                'location' => 'Ruang Publik Kreatif, Yogyakarta',
                'days' => 68,
                'start' => '18:30',
                'end' => '21:30',
                'ticket' => 'Pass Creative Community',
                'quota' => 140,
                'poster' => 'events/creative-community.jpg',
            ],
            [
                'slug' => 'kelas-mengajar-inklusif-2026',
                'category' => 'pendidikan',
                'name' => 'Kelas Mengajar Inklusif: Merancang Ruang Belajar yang Aman',
                'description' => "Pelatihan untuk pengajar, relawan, dan pengelola komunitas belajar yang ingin membuat pengalaman belajar lebih ramah bagi beragam kebutuhan peserta.\n\nMateri mencakup komunikasi inklusif, desain aktivitas, pengelolaan kelas, dan refleksi berbasis pengalaman.",
                'location' => 'Perpustakaan Kota, Makassar',
                'days' => 75,
                'start' => '09:00',
                'end' => '13:00',
                'ticket' => 'Tiket Kelas Mengajar',
                'quota' => 100,
                'poster' => 'events/pendidikan-inklusif.jpg',
            ],
            [
                'slug' => 'financial-planning-profesional-muda-2026',
                'category' => 'keuangan-pribadi',
                'name' => 'Financial Planning untuk Profesional Muda',
                'description' => "Kelas dasar untuk menyusun kebiasaan keuangan yang sehat tanpa jargon rumit.\n\nPeserta akan membuat peta arus kas, dana darurat, prioritas proteksi, dan rencana tujuan keuangan yang dapat ditinjau secara berkala.",
                'location' => 'Online melalui Eventku Live',
                'days' => 82,
                'start' => '19:00',
                'end' => '21:00',
                'ticket' => 'Pass Financial Planning',
                'quota' => 300,
                'poster' => 'events/financial-planning.jpg',
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($events as $eventData) {
            $posterPath = $eventData['poster'];
            if (! Storage::disk('public')->exists($posterPath)) {
                throw new RuntimeException("Poster event tidak ditemukan: {$posterPath}");
            }

            if (Event::where('slug', $eventData['slug'])->exists()) {
                $skipped++;

                continue;
            }

            $startAt = $baseDate->copy()->addDays($eventData['days'] - 19)->setTimeFromTimeString($eventData['start']);
            $endAt = $startAt->copy()->setTimeFromTimeString($eventData['end']);
            $registrationStartAt = $startAt->copy()->subDays(30)->setTime(9, 0);
            $registrationEndAt = $startAt->copy()->subDays(2)->setTime(23, 59);

            DB::transaction(function () use ($admin, $organizer, $categories, $eventData, $posterPath, $startAt, $endAt, $registrationStartAt, $registrationEndAt): void {
                $event = Event::create([
                    'organizer_id' => $organizer->id,
                    'category_id' => $categories[$eventData['category']]->id,
                    'name' => $eventData['name'],
                    'slug' => $eventData['slug'],
                    'description' => $eventData['description'],
                    'location' => $eventData['location'],
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'registration_start_at' => $registrationStartAt,
                    'registration_end_at' => $registrationEndAt,
                    'poster_path' => $posterPath,
                    'status' => 'published',
                    'verified_by' => $admin->id,
                    'verified_at' => now(),
                ]);

                $event->ticketTypes()->create([
                    'name' => $eventData['ticket'],
                    'description' => 'Tiket akses gratis dari katalog Eventku.',
                    'quota' => $eventData['quota'],
                    'registration_limit_per_user' => 1,
                ]);
            });

            $created++;
        }

        $this->command?->info("Event content selesai: {$created} dibuat, {$skipped} dilewati karena slug sudah ada.");
    }
}
