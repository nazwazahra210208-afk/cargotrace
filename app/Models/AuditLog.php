<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $guarded = ['id'];

    /**
     * Relasi ke tabel users (Aktor/user yang melakukan aktivitas log)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}