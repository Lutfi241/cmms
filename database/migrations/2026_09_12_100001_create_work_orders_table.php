<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_code')->unique(); // format: WO-0001

            $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();

            // requester_id: siapa yang mengajukan (nullable, karena bisa dibuat admin atas nama sistem)
            $table->foreignId('requester_id')->nullable()->constrained('users')->nullOnDelete();

            // technician_id: siapa yang mengerjakan, diisi saat status jadi in_progress
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'completed'])
                  ->default('pending');

            $table->text('description');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
