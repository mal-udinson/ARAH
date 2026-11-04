<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->foreignId('kampus_id')
                ->nullable()
                ->after('user_id')
                ->constrained('kampus')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['kampus_id']);
            $table->dropColumn('kampus_id');
        });
    }
};