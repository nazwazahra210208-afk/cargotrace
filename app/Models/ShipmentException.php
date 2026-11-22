<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentException extends Model
{
    protected $guarded = ['id'];

    /**
     * Relasi ke tabel shipments (Kendala ini terjadi pada pengiriman yang mana)
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
