<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('mata_kuliah', function(Blueprint $t){ $t->id(); $t->string('kode')->unique(); $t->string('nama'); $t->string('kategori'); $t->unsignedTinyInteger('sks')->default(3); $t->string('prasyarat')->nullable(); $t->timestamps(); }); }
 public function down(): void { Schema::dropIfExists('mata_kuliah'); }
};
