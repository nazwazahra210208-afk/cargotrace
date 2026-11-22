<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\AuditLog;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ShipmentController extends Controller
{
    /**
     * Helper internal untuk mencatat aktivitas ke tabel audit_logs
     */
    private function logAudit(string $action, string $targetEntity, ?string $details = null): void
    {
        if (Auth::check() && Schema::hasTable('audit_logs')) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'target_entity' => $targetEntity,
                'details' => $details,
            ]);
        }
    }

    /**
     * Menampilkan daftar seluruh pengiriman (untuk Operator dengan dukungan filter status)
     */
    public function index(Request $request)
    {
        $query = Shipment::with(['carrier', 'originLocation', 'destinationLocation', 'user']);

        if ($request->has('status') && !empty($request->status)) {
            $statusParam = trim(str_replace('+', ' ', $request->status));
            
            if (strcasecmp($statusParam, 'In Transit') === 0 || strcasecmp($statusParam, 'IN_TRANSIT') === 0) {
                $query->where(function($q) {
                    $q->where('status', 'In Transit')
                      ->orWhere('status', 'IN_TRANSIT')
                      ->orWhere('status', 'in_transit');
                });
            } elseif (strcasecmp($statusParam, 'Delivered') === 0 || strcasecmp($statusParam, 'DELIVERED') === 0) {
                $query->where(function($q) {
                    $q->where('status', 'Delivered')
                      ->orWhere('status', 'DELIVERED')
                      ->orWhere('status', 'delivered');
                });
            } else {
                $query->where('status', $statusParam);
            }
        }

        $shipments = $query->latest()->get();
        
        return view('shipments.index', compact('shipments'));
    }

    /**
     * Menampilkan daftar seluruh pengiriman untuk PANEL ADMIN (dengan dukungan filter status)
     */
    public function adminIndex(Request $request)
    {
        $query = Shipment::with(['carrier', 'originLocation', 'destinationLocation', 'user']);

        if ($request->has('status') && !empty($request->status)) {
            $statusParam = trim(str_replace('+', ' ', $request->status));
            
            if (strcasecmp($statusParam, 'In Transit') === 0 || strcasecmp($statusParam, 'IN_TRANSIT') === 0) {
                $query->where(function($q) {
                    $q->where('status', 'In Transit')
                      ->orWhere('status', 'IN_TRANSIT')
                      ->orWhere('status', 'in_transit');
                });
            } elseif (strcasecmp($statusParam, 'Delivered') === 0 || strcasecmp($statusParam, 'DELIVERED') === 0) {
                $query->where(function($q) {
                    $q->where('status', 'Delivered')
                      ->orWhere('status', 'DELIVERED')
                      ->orWhere('status', 'delivered');
                });
            } else {
                $query->where('status', $statusParam);
            }
        }

        $shipments = $query->latest()->get();
        
        return view('admin.shipments.index', compact('shipments'));
    }

    /**
     * Menampilkan detail resi pengiriman berdasarkan nomor resi
     */
    public function track($shipmentNumber, WeatherService $weatherService)
    {
        $shipment = Shipment::with([
            'carrier',
            'originLocation',
            'destinationLocation',
            'trackingEvents.creator',
            'exceptions'
        ])->where('shipment_number', $shipmentNumber)->firstOrFail();

        $weather = null;
        if ($shipment->destinationLocation) {
            $weather = $weatherService->getWeather(
                $shipment->destinationLocation->latitude,
                $shipment->destinationLocation->longitude
            );
        }

        return view('shipments.track', compact('shipment', 'weather'));
    }

    /**
     * Menangani proses pencarian resi dari form publik
     */
    public function search(Request $request)
    {
        $request->validate([
            'shipment_number' => 'required|string',
        ]);

        $shipmentNumber = strtoupper(trim($request->input('shipment_number')));
        $shipment = Shipment::where('shipment_number', $shipmentNumber)->first();

        if (!$shipment) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nomor resi ' . $shipmentNumber . ' tidak ditemukan.');
        }

        return redirect('/track/' . $shipment->shipment_number);
    }

    /**
     * Menampilkan form untuk membuat pengiriman baru (Operator)
     */
    public function create()
    {
        $carriers = \App\Models\Carrier::all();
        $locations = \App\Models\Location::all();
        $customers = \App\Models\User::where('role', 'customer')->get();

        return view('shipments.create', compact('carriers', 'locations', 'customers'));
    }

    /**
     * Menampilkan form untuk membuat pengiriman baru khusus ADMIN
     */
    public function adminCreate()
    {
        $carriers = \App\Models\Carrier::all();
        $locations = \App\Models\Location::all();
        $customers = \App\Models\User::where('role', 'customer')->get();

        return view('admin.shipments.create', compact('carriers', 'locations', 'customers'));
    }

    /**
     * Menyimpan data pengiriman baru ke database (Operator)
     */
    public function store(Request $request)
    {
        $request->validate([
            'shipment_number' => 'required|string|unique:shipments,shipment_number',
            'user_id' => 'required|exists:users,id',
            'carrier_id' => 'required|exists:carriers,id',
            'origin_location_id' => 'required|exists:locations,id',
            'destination_location_id' => 'required|exists:locations,id|different:origin_location_id',
            'cargo_type' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.1',
            'volume' => 'required|numeric|min:0.01',
            'expected_departure_date' => 'required|date',
            'expected_arrival_date' => 'required|date|after_or_equal:expected_departure_date',
        ]);

        $shipment = Shipment::create([
            'shipment_number' => strtoupper(trim($request->shipment_number)),
            'user_id' => $request->user_id,
            'carrier_id' => $request->carrier_id,
            'origin_location_id' => $request->origin_location_id,
            'destination_location_id' => $request->destination_location_id,
            'cargo_type' => $request->cargo_type,
            'weight' => $request->weight,
            'volume' => $request->volume,
            'expected_departure_date' => $request->expected_departure_date,
            'expected_arrival_date' => $request->expected_arrival_date,
            'status' => 'In Transit',
        ]);

        $this->logAudit('CREATE', 'Shipment', 'Operator membuat pengiriman baru ' . $shipment->shipment_number);

        return redirect('/shipments')->with('success', 'Pengiriman baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui status pengiriman oleh Logistics Operator
     */
    public function operatorUpdateStatus(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Created,In Transit,Delivered',
        ]);

        $oldStatus = $shipment->status;
        $shipment->update([
            'status' => $request->status,
        ]);

        $this->logAudit('UPDATE', 'Shipment', 'Status ' . $shipment->shipment_number . ' diubah dari ' . $oldStatus . ' menjadi ' . $request->status);

        return redirect()->back()->with('success', 'Status pengiriman berhasil diperbarui menjadi ' . $request->status . '.');
    }

    /**
     * Menambahkan catatan Tracking Event baru oleh Logistics Operator
     */
    public function storeTrackingEvent(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'location_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $columns = Schema::getColumnListing('tracking_events');
        $data = [
            'shipment_id' => $shipment->id,
        ];

        if (in_array('created_by', $columns)) {
            $data['created_by'] = Auth::id();
        }
        if (in_array('title', $columns)) {
            $data['title'] = $request->title;
        }
        if (in_array('status', $columns)) {
            $data['status'] = $request->title;
        }
        if (in_array('event_name', $columns)) {
            $data['event_name'] = $request->title;
        }
        if (in_array('location_name', $columns)) {
            $data['location_name'] = $request->location_name;
        }
        if (in_array('location', $columns)) {
            $data['location'] = $request->location_name;
        }
        if (in_array('description', $columns)) {
            $data['description'] = $request->description;
        }
        if (in_array('notes', $columns)) {
            $data['notes'] = $request->description;
        }
        if (in_array('event_time', $columns)) {
            $data['event_time'] = now();
        }
        if (in_array('occurred_at', $columns)) {
            $data['occurred_at'] = now();
        }

        TrackingEvent::create($data);

        $this->logAudit('CREATE', 'TrackingEvent', 'Menambah tracking event "' . $request->title . '" pada ' . $shipment->shipment_number);

        return redirect()->back()->with('success', 'Tracking event baru berhasil ditambahkan.');
    }

    /**
     * Menyimpan data pengiriman baru khusus ADMIN
     */
    public function adminStore(Request $request)
    {
        $request->validate([
            'shipment_number' => 'required|string|unique:shipments,shipment_number',
            'user_id' => 'required|exists:users,id',
            'carrier_id' => 'required|exists:carriers,id',
            'origin_location_id' => 'required|exists:locations,id',
            'destination_location_id' => 'required|exists:locations,id|different:origin_location_id',
            'cargo_type' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.1',
            'volume' => 'required|numeric|min:0.01',
            'expected_departure_date' => 'required|date',
            'expected_arrival_date' => 'required|date|after_or_equal:expected_departure_date',
        ]);

        $shipment = Shipment::create([
            'shipment_number' => strtoupper(trim($request->shipment_number)),
            'user_id' => $request->user_id,
            'carrier_id' => $request->carrier_id,
            'origin_location_id' => $request->origin_location_id,
            'destination_location_id' => $request->destination_location_id,
            'cargo_type' => $request->cargo_type,
            'weight' => $request->weight,
            'volume' => $request->volume,
            'expected_departure_date' => $request->expected_departure_date,
            'expected_arrival_date' => $request->expected_arrival_date,
            'status' => 'In Transit',
        ]);

        $this->logAudit('CREATE', 'Shipment', 'Admin membuat pengiriman baru ' . $shipment->shipment_number);

        return redirect()->route('admin.shipments.index')->with('success', 'Data pengiriman berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail pengiriman untuk admin (View)
     */
    public function show($id)
    {
        $shipment = Shipment::with(['carrier', 'originLocation', 'destinationLocation', 'user', 'trackingEvents', 'exceptions'])->findOrFail($id);
        return view('admin.shipments.show', compact('shipment'));
    }

    /**
     * Menampilkan form edit pengiriman untuk admin
     */
    public function edit($id)
    {
        $shipment = Shipment::findOrFail($id);
        $carriers = \App\Models\Carrier::all();
        $locations = \App\Models\Location::all();
        
        return view('admin.shipments.edit', compact('shipment', 'carriers', 'locations'));
    }

    /**
     * Memperbarui data pengiriman oleh admin
     */
    public function update(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        $request->validate([
            'status' => 'required|string',
            'cargo_type' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.1',
        ]);

        $shipment->update([
            'cargo_type' => $request->cargo_type,
            'weight' => $request->weight,
            'status' => $request->status,
        ]);

        $this->logAudit('UPDATE', 'Shipment', 'Admin memperbarui data pengiriman ' . $shipment->shipment_number);

        return redirect()->route('admin.shipments.index')->with('success', 'Data pengiriman berhasil diperbarui.');
    }

    /**
     * Menghapus data pengiriman oleh admin (Delete)
     */
    public function destroy($id)
    {
        $shipment = Shipment::findOrFail($id);
        $number = $shipment->shipment_number;
        $shipment->delete();

        $this->logAudit('DELETE', 'Shipment', 'Admin menghapus pengiriman ' . $number);

        return redirect()->route('admin.shipments.index')->with('success', 'Data pengiriman berhasil dihapus.');
    }
}