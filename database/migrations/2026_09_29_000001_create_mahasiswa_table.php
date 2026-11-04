<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('mahasiswa', function(Blueprint $t){ $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete(); $t->string('nim')->unique(); $t->string('nama'); $t->unsignedTinyInteger('semester')->default(1); $t->decimal('ipk',3,2)->default(0); $t->timestamps(); }); }
 public function down(): void { Schema::dropIfExists('mahasiswa'); }
};
