<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_materials', function (Blueprint $table) {
            $table->id();

            // Material yang rusak
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');

            // Sumber kerusakan: warehouse / team
            $table->enum('source', ['warehouse', 'team']);

            // Jika rusak berasal dari tim
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');

            // Jumlah bahan yang rusak
            $table->integer('qty');

            // Penyebab kerusakan
            $table->string('cause');

            // User yang melaporkan
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');

            // status kerusakan
            $table->enum('status', ['waiting', 'discarded', 'repaired'])->default('waiting');

            $table->text('notes')->nullable();

            $table->timestamp('reported_at')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_materials');
    }
};
