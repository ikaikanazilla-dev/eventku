<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained()->restrictOnDelete();
            $table->string('ticket_code')->unique();
            $table->string('qr_token_hash')->unique();
            $table->enum('status', ['valid', 'used', 'cancelled'])->default('valid')->index();
            $table->dateTime('issued_at');
            $table->dateTime('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_tickets');
    }
};
