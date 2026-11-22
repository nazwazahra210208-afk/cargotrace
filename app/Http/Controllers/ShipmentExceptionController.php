<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentException;
use Illuminate\Http\Request;

class ShipmentExceptionController extends Controller
{
    /**
     * Menyimpan data kendala (exception) baru untuk suatu pengiriman
     */
    public function store(Request $request, $shipmentId)
    {
        $request->validate([
            'exception_type' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
        ]);

        $shipment = Shipment::findOrFail($shipmentId);

        ShipmentException::create([
            'shipment_id' => $shipment->id,
            'exception_type' => $request->exception_type,
            'description' => $request->description,
            'severity' => $request->severity,
            'resolved' => false,
        ]);

        // Ubah juga status pengiriman menjadi 'EXCEPTION' atau 'DELAYED' jika diperlukan
        $shipment->update(['status' => 'EXCEPTION']);

        return redirect()->back()->with('success', 'Kendala pengiriman berhasil dicatat.');
    }
}