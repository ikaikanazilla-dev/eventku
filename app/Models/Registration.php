<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'event_id', 'ticket_type_id', 'registration_code', 'status', 'registered_at', 'cancelled_at'];

    protected function casts(): array
    {
        return ['registered_at' => 'datetime', 'cancelled_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function digitalTicket()
    {
        return $this->hasOne(DigitalTicket::class);
    }
}
