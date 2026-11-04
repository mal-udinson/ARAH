<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nara_conversations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title')->default('Percakapan baru');
            $t->timestamps();
        });

        Schema::create('nara_messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('conversation_id')->constrained('nara_conversations')->cascadeOnDelete();
            $t->string('role', 20); // user | assistant
            $t->text('message');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nara_messages');
        Schema::dropIfExists('nara_conversations');
    }
};