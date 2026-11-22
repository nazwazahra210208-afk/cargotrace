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
    Schema::create('shipment_exceptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
        $table->string('exception_type'); // Contoh: CUSTOMS_HOLD, WEATHER_DELAY, dll
        $table->string('severity');       // LOW, MEDIUM, HIGH
        $table->string('status')->default('OPEN'); // OPEN, INVESTIGATING, RESOLVED
        $table->timestamp('detected_at')->nullable();
        $table->timestamp('resolved_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_exceptions');
    }
};
