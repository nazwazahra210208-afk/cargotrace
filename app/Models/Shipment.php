<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $guarded = ['id'];

    /**
     * Relasi ke tabel users (Customer pemilik pengiriman)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke tabel carriers (Perusahaan kurir pengangkut)
     */
    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * Relasi ke tabel locations sebagai titik asal pengiriman (origin)
     */
    public function originLocation()
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    /**
     * Relasi ke tabel locations sebagai titik tujuan pengiriman (destination)
     */
    public function destinationLocation()
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    /**
     * Relasi ke tabel tracking_events (Riwayat pergerakan/status paket)
     */
    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class);
    }

    /**
     * Relasi ke tabel shipment_exceptions (Kendala/masalah pada pengiriman)
     */
    public function exceptions()
    {
        return $this->hasMany(ShipmentException::class);
    }
}