<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'name', 'description', 'quota', 'registration_limit_per_user'];

    protected function casts(): array
    {
        return ['quota' => 'integer', 'registration_limit_per_user' => 'integer'];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function remainingQuota(): int
    {
        return max(0, $this->quota - $this->registrations()->where('status', 'registered')->count());
    }
}
