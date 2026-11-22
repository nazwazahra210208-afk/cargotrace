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
    Schema::create('tracking_events', function (Blueprint $table) {
        $table->id();
        $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
        $table->string('status');               // Status saat event dicatat
        $table->string('location_description'); // Keterangan lokasi fisik
        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 10, 7)->nullable();
        $table->text('description');            // Catatan/deskripsi detail
        $table->foreignId('created_by')->constrained('users')->cascadeOnDelete(); // Operator yang mencatat
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};
