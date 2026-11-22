<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $guarded = ['id'];

    /**
     * Relasi ke tabel shipments (Satu carrier memiliki banyak shipments)
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}