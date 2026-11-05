<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('material_out_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_out_id')->constrained('material_outs')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->integer('quantity'); // jumlah material keluar
            $table->string('condition')->default('layak pakai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_out_details');
    }
};
