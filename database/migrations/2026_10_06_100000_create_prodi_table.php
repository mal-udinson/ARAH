<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan walau tabel prodi sudah pernah dibuat sebagian
        if (! Schema::hasTable('prodi')) {
            Schema::create('prodi', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('kampus_id')->nullable()->index();
                $t->string('nama');
                $t->timestamps();
            });

            return;
        }

        if (! Schema::hasColumn('prodi', 'kampus_id')) {
            Schema::table('prodi', fn (Blueprint $t) => $t->unsignedBigInteger('kampus_id')->nullable()->index());
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('prodi');
    }
};