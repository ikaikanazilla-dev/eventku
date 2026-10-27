<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use HasFactory;

    protected $fillable = ['digital_ticket_id', 'successful_ticket_id', 'checked_in_by', 'checked_in_at', 'method', 'result', 'notes'];

    protected function casts(): array
    {
        return ['checked_in_at' => 'datetime'];
    }

    public function ticket()
    {
        return $this->belongsTo(DigitalTicket::class, 'digital_ticket_id');
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }
}
