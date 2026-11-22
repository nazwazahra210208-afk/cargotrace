<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    protected $guarded = ['id'];

    /**
     * Relasi ke tabel shipments (Event ini milik pengiriman yang mana)
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Relasi ke tabel users (Operator yang mencatat event tracking ini)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}