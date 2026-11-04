<?php

use App\Models\Mahasiswa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = (new Mahasiswa)->getTable();

        // Hanya menambah kolom yang belum ada, jadi aman kalau sebagian sudah dibuat
        $need = [
            'kampus_id'     => ! Schema::hasColumn($table, 'kampus_id'),
            'prodi_id'      => ! Schema::hasColumn($table, 'prodi_id'),
            'jenis_kelamin' => ! Schema::hasColumn($table, 'jenis_kelamin'),
            'onboarded_at'  => ! Schema::hasColumn($table, 'onboarded_at'),
        ];

        Schema::table($table, function (Blueprint $t) use ($need) {
            if ($need['kampus_id'])     $t->unsignedBigInteger('kampus_id')->nullable();
            if ($need['prodi_id'])      $t->unsignedBigInteger('prodi_id')->nullable();
            if ($need['jenis_kelamin']) $t->string('jenis_kelamin', 20)->nullable();
            if ($need['onboarded_at'])  $t->timestamp('onboarded_at')->nullable();
        });

        // Mahasiswa lama yang sudah punya NIM asli dianggap sudah lengkap, tidak dipaksa mengisi form
        DB::table($table)->whereNull('onboarded_at')->where('nim', 'not like', 'TEMP-%')->update(['onboarded_at' => now()]);
    }

    public function down(): void
    {
        $table = (new Mahasiswa)->getTable();

        if (Schema::hasColumn($table, 'onboarded_at')) {
            Schema::table($table, fn (Blueprint $t) => $t->dropColumn('onboarded_at'));
        }
    }
};