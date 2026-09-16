<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_id')->constrained('floors')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->timestamps();

            // FIX BUG: cegah duplikasi location area dengan nama & kode sama DI DALAM floor yang sama
            $table->unique(['floor_id', 'name', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_areas');
    }
};
