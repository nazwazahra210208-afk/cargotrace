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
    Schema::create('locations', function (Blueprint $table) {
        $table->id();
        $table->string('name');          // Nama tempat/gudang/pelabuhan
        $table->string('city');          // Kota
        $table->string('country');       // Negara
        $table->decimal('latitude', 10, 7)->nullable();   // Titik koordinat peta
        $table->decimal('longitude', 10, 7)->nullable();  // Titik koordinat peta
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
