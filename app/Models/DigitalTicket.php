<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalTicket extends Model
{
    use HasFactory;

    protected $fillable = ['registration_id', 'ticket_code', 'qr_token_hash', 'status', 'issued_at', 'used_at'];

    protected $hidden = ['qr_token_hash'];

    protected function casts(): array
    {
        return ['issued_at' => 'datetime', 'used_at' => 'datetime'];
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }
}
