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
    Schema::create('shipments', function (Blueprint $table) {
        $table->id();
        $table->string('shipment_number')->unique(); // Nomor Resi Unik
        $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Customer pemilik
        $table->foreignId('carrier_id')->constrained()->cascadeOnDelete(); // Perusahaan kurir
        $table->foreignId('origin_location_id')->constrained('locations')->cascadeOnDelete(); // Lokasi Asal
        $table->foreignId('destination_location_id')->constrained('locations')->cascadeOnDelete(); // Lokasi Tujuan
        $table->string('cargo_type');       // Jenis barang
        $table->decimal('weight', 8, 2);    // Berat barang
        $table->decimal('volume', 8, 2);    // Volume barang
        $table->date('expected_departure_date'); // Estimasi tanggal berangkat
        $table->date('expected_arrival_date');   // Estimasi tanggal tiba
        $table->string('status')->default('CREATED'); // Status: CREATED, BOOKED, IN_TRANSIT, DELIVERED
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
