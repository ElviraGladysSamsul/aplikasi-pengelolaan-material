<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('material_returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();

            // nama anggota tim yg menyerahkan (opsional)
            $table->string('reported_by_team')->nullable();

            // admin gudang yg input
            $table->foreignId('returned_by')->constrained('users');

            $table->integer('qty');
            $table->text('notes')->nullable();
            $table->timestamp('returned_at')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_returns');
    }
};
