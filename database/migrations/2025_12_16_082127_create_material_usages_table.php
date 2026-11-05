<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('material_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained('users');

            $table->string('mitra');
            $table->string('no_wo')->unique();
            $table->string('jenis_psb');
            $table->string('barcode_dc')->nullable();

            $table->string('sn_ont')->nullable();
            $table->string('sn_stb')->nullable();
            $table->string('no_reservasi')->nullable();
            $table->string('valins_id')->nullable();
            $table->string('power_rx_ont')->nullable();

            $table->decimal('odp_lat', 10, 7)->nullable();
            $table->decimal('odp_long', 10, 7)->nullable();
            $table->decimal('pelanggan_lat', 10, 7)->nullable();
            $table->decimal('pelanggan_long', 10, 7)->nullable();

            $table->text('catatan_mitra')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_usages');
    }
};
