<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('asesmen_minat', function(Blueprint $t){ $t->id(); $t->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete(); $t->string('kategori'); $t->unsignedTinyInteger('skor'); $t->timestamps(); }); }
 public function down(): void { Schema::dropIfExists('asesmen_minat'); }
};
