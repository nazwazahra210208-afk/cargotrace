<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ShipmentExceptionController extends Controller
{
    /**
     * Menyimpan data kendala (exception) baru untuk suatu pengiriman
     */
    public function store(Request $request,$shipmentId)
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

        if (Auth::check() && Schema::hasTable('audit_logs')) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'CREATE',
                'target_entity' => 'ShipmentException',
                'details' => 'Kendala "' . $request->exception_type . '" (' . $request->severity . ') dicatat pada ' .$shipment->shipment_number,
            ]);
        }

        return redirect()->back()->with('success', 'Kendala pengiriman berhasil dicatat.');
    }
}