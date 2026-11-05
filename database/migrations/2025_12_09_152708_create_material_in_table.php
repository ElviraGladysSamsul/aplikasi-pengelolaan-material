<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_in', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel materials
            $table->foreignId('material_id')
                ->constrained('materials')
                ->onDelete('cascade');

            $table->integer('qty');                     // jumlah barang masuk
            $table->string('source');                   // vendor / gudang / telkom akses
            $table->string('invoice_number')->nullable(); // DO / surat jalan

            // Relasi ke user (siapa yang input)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamp('received_at')->useCurrent(); // waktu barang diterima

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_in');
    }
};
