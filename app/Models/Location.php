<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $guarded = ['id'];

    /**
     * Relasi ke tabel shipments di mana lokasi ini menjadi titik asal (origin)
     */
    public function originShipments()
    {
        return $this->hasMany(Shipment::class, 'origin_location_id');
    }

    /**
     * Relasi ke tabel shipments di mana lokasi ini menjadi titik tujuan (destination)
     */
    public function destinationShipments()
    {
        return $this->hasMany(Shipment::class, 'destination_location_id');
    }
}