<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('chat_history', function(Blueprint $t){ $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete(); $t->string('role'); $t->text('message'); $t->timestamps(); }); }
 public function down(): void { Schema::dropIfExists('chat_history'); }
};
