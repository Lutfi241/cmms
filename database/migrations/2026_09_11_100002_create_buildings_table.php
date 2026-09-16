<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->timestamps();

            // FIX BUG: cegah duplikasi building dengan nama & kode sama DI DALAM site yang sama
            $table->unique(['site_id', 'name', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
