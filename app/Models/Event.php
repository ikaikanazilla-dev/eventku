<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['organizer_id', 'category_id', 'name', 'slug', 'description', 'location', 'start_at', 'end_at', 'registration_start_at', 'registration_end_at', 'poster_path', 'status', 'rejection_reason', 'verified_by', 'verified_at'];

    protected function casts(): array
    {
        return ['start_at' => 'datetime', 'end_at' => 'datetime', 'registration_start_at' => 'datetime', 'registration_end_at' => 'datetime', 'verified_at' => 'datetime'];
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function getPosterUrlAttribute(): string
    {
        if ($this->poster_path && Storage::disk('public')->exists($this->poster_path)) {
            return Storage::disk('public')->url($this->poster_path);
        }

        $category = Str::slug($this->category?->name ?? 'event');
        $fallbacks = [
            'teknologi-ai' => 'seminar-teknologi.svg',
            'teknologi' => 'seminar-teknologi.svg',
            'desain-kreatif' => 'workshop-ui-ux.svg',
            'musik-festival' => 'festival-kreatif.svg',
            'startup-bisnis' => 'business-conference.svg',
        ];

        return asset('images/events/'.($fallbacks[$category] ?? 'event-default.svg'));
    }

    public function isRegistrationOpen(): bool
    {
        return $this->status === 'published' && now()->between($this->registration_start_at, $this->registration_end_at);
    }

    public function isCheckInOpen(?Carbon $at = null): bool
    {
        $at ??= now();
        $checkInStartsAt = $this->start_at->copy()->subHours(6);

        return $this->status === 'published'
            && $at->greaterThanOrEqualTo($checkInStartsAt)
            && $at->lessThanOrEqualTo($this->end_at);
    }

    public function remainingQuota(): int
    {
        $registered = array_key_exists('registered_count', $this->attributes)
            ? (int) $this->attributes['registered_count']
            : $this->registrations()->where('status', 'registered')->count();

        return max(0, (int) $this->ticketTypes->sum('quota') - $registered);
    }

    public function scopePublic($query)
    {
        return $query->where('status', 'published')->where('start_at', '>=', now()->subDay());
    }
}
