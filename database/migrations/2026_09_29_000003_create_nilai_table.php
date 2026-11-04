<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('nilai', function(Blueprint $t){ $t->id(); $t->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete(); $t->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete(); $t->decimal('nilai_angka',5,2); $t->timestamps(); }); }
 public function down(): void { Schema::dropIfExists('nilai'); }
};
