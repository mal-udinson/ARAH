<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen_chat_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mahasiswa_id')
                ->constrained('mahasiswa')
                ->cascadeOnDelete();

            $table->foreignId('dosen_id')
                ->constrained('dosen')
                ->cascadeOnDelete();

            $table->foreignId('sender_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('message');

            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index([
                'mahasiswa_id',
                'dosen_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen_chat_messages');
    }
};