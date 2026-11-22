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
    Schema::create('carriers', function (Blueprint $table) {
        $table->id();
        $table->string('name');             // Nama perusahaan kurir
        $table->string('code')->unique();   // Kode unik kurir (misal: JNE, DHL)
        $table->string('contact_number')->nullable(); // Nomor telepon
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};
