<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkins', function (Blueprint $table) {
            $table->foreignId('successful_ticket_id')->nullable()->unique()->constrained('digital_tickets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('checkins', function (Blueprint $table) {
            $table->dropForeign(['successful_ticket_id']);
            $table->dropUnique(['successful_ticket_id']);
            $table->dropColumn('successful_ticket_id');
        });
    }
};
