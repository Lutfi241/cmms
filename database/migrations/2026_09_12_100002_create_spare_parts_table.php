<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('part_number');
            $table->string('unit')->default('pcs'); // satuan: pcs, box, meter, dll.
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('minimum_stock')->default(0); // untuk peringatan stok menipis
            $table->timestamps();

            // FIX BUG: cegah duplikasi spare part dengan nama & nomor part yang sama
            $table->unique(['name', 'part_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spare_parts');
    }
};
