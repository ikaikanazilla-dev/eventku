<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class EventPosterSeeder extends Seeder
{
    public function run(): void
    {
        $posters = [
            'indonesia-tech-summit-2026' => 'events/indonesia-tech-summit.jpg',
            'ui-ux-masterclass-2026' => 'events/ui-ux-masterclass.jpg',
            'ai-untuk-bisnis-indonesia-2026' => 'events/tech-ai-business.jpg',
            'strategi-konten-digital-umkm-2026' => 'events/digital-marketing-umkm.jpg',
            'founder-playbook-nusantara-2026' => 'events/startup-founder.jpg',
            'product-design-lab-ui-ux-2026' => 'events/ui-ux-design.jpg',
            'career-switch-digital-2026' => 'events/career-digital.jpg',
            'jakarta-street-photography-walk-2026' => 'events/street-photography.jpg',
            'rasa-nusantara-weekend-2026' => 'events/festival-kuliner.jpg',
            'creative-community-night-2026' => 'events/creative-community.jpg',
            'kelas-mengajar-inklusif-2026' => 'events/pendidikan-inklusif.jpg',
            'financial-planning-profesional-muda-2026' => 'events/financial-planning.jpg',
        ];

        $legacyPosters = [
            'indonesia-tech-summit-2026' => null,
            'ui-ux-masterclass-2026' => null,
            'ai-untuk-bisnis-indonesia-2026' => 'events/ai-bisnis-indonesia.svg',
            'strategi-konten-digital-umkm-2026' => 'events/konten-digital-umkm.svg',
            'founder-playbook-nusantara-2026' => 'events/founder-playbook.svg',
            'product-design-lab-ui-ux-2026' => 'events/product-design-lab.svg',
            'career-switch-digital-2026' => 'events/career-switch-digital.svg',
            'jakarta-street-photography-walk-2026' => 'events/street-photography-walk.svg',
            'rasa-nusantara-weekend-2026' => 'events/rasa-nusantara-weekend.svg',
            'creative-community-night-2026' => 'events/creative-community-night.svg',
            'kelas-mengajar-inklusif-2026' => 'events/kelas-mengajar-inklusif.svg',
            'financial-planning-profesional-muda-2026' => 'events/financial-planning.svg',
        ];

        foreach ($posters as $posterPath) {
            if (! Storage::disk('public')->exists($posterPath)) {
                throw new RuntimeException("Poster event tidak ditemukan: {$posterPath}");
            }
        }

        $updated = 0;
        $skipped = 0;
        $missing = 0;

        DB::transaction(function () use ($legacyPosters, $posters, &$missing, &$skipped, &$updated): void {
            foreach ($posters as $slug => $posterPath) {
                $event = Event::query()->where('slug', $slug)->first();

                if ($event === null) {
                    $missing++;

                    continue;
                }

                if ($event->poster_path === $posterPath) {
                    $skipped++;

                    continue;
                }

                if ($event->poster_path !== $legacyPosters[$slug]) {
                    $skipped++;

                    continue;
                }

                $event->update(['poster_path' => $posterPath]);
                $updated++;
            }
        });

        $this->command?->info("Poster event selesai: {$updated} diperbarui, {$skipped} dilewati, {$missing} slug tidak ditemukan.");
    }
}
