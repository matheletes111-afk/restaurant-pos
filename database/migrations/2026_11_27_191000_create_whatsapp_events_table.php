<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_events', function (Blueprint $table) {
            $table->id();
            $table->string('wa_message_id')->unique()->nullable();
            $table->string('event_type');
            $table->string('sender_phone')->nullable();
            $table->json('payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_events');
    }
};
