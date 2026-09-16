<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained('buildings')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->timestamps();

            // FIX BUG: cegah duplikasi floor dengan nama & kode sama DI DALAM building yang sama
            $table->unique(['building_id', 'name', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floors');
    }
};
