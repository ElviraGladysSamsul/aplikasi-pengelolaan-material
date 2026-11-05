<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('material_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade'); // penerima material
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // admin/gudang yang menyerahkan
            $table->timestamp('distributed_at')->useCurrent(); // tanggal distribusi
            $table->string('notes')->nullable(); // catatan opsional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_outs');
    }
};
