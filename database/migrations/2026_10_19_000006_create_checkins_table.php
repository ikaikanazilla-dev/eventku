<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('digital_ticket_id')->constrained()->restrictOnDelete();
            $table->foreignId('checked_in_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('checked_in_at');
            $table->enum('method', ['qr', 'manual']);
            $table->enum('result', ['success', 'rejected'])->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['digital_ticket_id', 'result']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkins');
    }
};
