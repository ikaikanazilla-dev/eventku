<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@eventku.test'], ['name' => 'Admin Eventku', 'password' => Hash::make('Admin12345!'), 'role' => 'admin', 'status' => 'active']);
        $organizer = User::updateOrCreate(['email' => 'organizer@eventku.test'], ['name' => 'Studio TechNusantara', 'password' => Hash::make('Organizer12345!'), 'role' => 'organizer', 'status' => 'active', 'phone' => '081234567890']);
        $participant = User::updateOrCreate(['email' => 'participant@eventku.test'], ['name' => 'Arya Pratama', 'password' => Hash::make('Participant12345!'), 'role' => 'participant', 'status' => 'active']);
        $categories = collect(['Teknologi & AI', 'Musik & Festival', 'Desain Kreatif', 'Startup & Bisnis'])->mapWithKeys(fn ($name) => [$name => Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'is_active' => true])]);
        $event = Event::firstOrCreate(['slug' => 'indonesia-tech-summit-2026'], ['organizer_id' => $organizer->id, 'category_id' => $categories['Teknologi & AI']->id, 'name' => 'Indonesia Tech Summit 2026: Artificial Intelligence & Future Tech', 'description' => 'Konferensi teknologi dan inovasi dengan pembicara terkemuka, agenda praktis, serta networking untuk profesional digital Indonesia.', 'location' => 'Jakarta Convention Center, Jakarta', 'start_at' => now()->addDays(28)->setTime(9, 0), 'end_at' => now()->addDays(28)->setTime(17, 0), 'registration_start_at' => now()->subDays(2), 'registration_end_at' => now()->addDays(25)->setTime(23, 59), 'poster_path' => 'events/indonesia-tech-summit.jpg', 'status' => 'published', 'verified_by' => $admin->id, 'verified_at' => now()]);
        $event->ticketTypes()->firstOrCreate(['name' => 'General Conference Pass'], ['quota' => 500, 'description' => 'Akses seluruh sesi konferensi.', 'registration_limit_per_user' => 1]);
        Event::firstOrCreate(['slug' => 'ui-ux-masterclass-2026'], ['organizer_id' => $organizer->id, 'category_id' => $categories['Desain Kreatif']->id, 'name' => 'UI/UX Masterclass & Design System Showcase', 'description' => 'Workshop intensif untuk membangun produk digital yang mudah digunakan.', 'location' => 'Online & Hybrid, Jakarta', 'start_at' => now()->addDays(40)->setTime(13, 0), 'end_at' => now()->addDays(40)->setTime(17, 0), 'registration_start_at' => now(), 'registration_end_at' => now()->addDays(38), 'poster_path' => 'events/ui-ux-masterclass.jpg', 'status' => 'published', 'verified_by' => $admin->id, 'verified_at' => now()])->ticketTypes()->firstOrCreate(['name' => 'General Pass'], ['quota' => 200]);
    }
}
