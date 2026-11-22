<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shipment;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\TrackingEvent;
use App\Models\ShipmentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CargoApiController extends Controller
{
    /**
     * Mengambil user terautentikasi dari Bearer Token
     */
    private function authenticateUser(Request $request): ?User
    {
        $token =$request->bearerToken();
        if (empty($token)) {
            return null;
        }
        return User::where('api_token', hash('sha256', $token))->first();
    }

    /**
     * POST /api/register
     */
    public function register(Request $request)
    {
        $validated =$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $plainToken = Str::random(60);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'api_token' => hash('sha256', $plainToken),
        ]);

        return response()->json([
            'message' => 'Registration successful.',
            'token' => $plainToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 201);
    }

    /**
     * POST /api/login
     */
    public function login(Request $request)
    {
        $validated =$request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email',$validated['email'])->first();

        if (!$user || !Hash::check($validated['password'],$user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $plainToken = Str::random(60);$user->forceFill([
            'api_token' => hash('sha256', $plainToken),
        ])->save();

        return response()->json([
            'message' => 'Login successful.',
            'token' => $plainToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 200);
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user->forceFill(['api_token' => null])->save();

        return response()->json(['message' => 'Logged out successfully.'], 200);
    }

    /**
     * GET /api/user
     */
    public function user(Request $request)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 200);
    }

    /**
     * GET /api/shipments (Filter + Pagination, Customer hanya melihat miliknya)
     */
    public function shipmentsIndex(Request $request)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $query = Shipment::with(['carrier', 'originLocation', 'destinationLocation']);

        if ($user->role === 'customer') {
            $query->where('user_id',$user->id);
        }

        if ($request->filled('status')) {
            $query->where('status',$request->status);
        }
        if ($request->filled('shipment_number')) {
            $query->where('shipment_number', 'like', '\%' .$request->shipment_number . '%');
        }

        $shipments =$query->latest()->paginate(10);

        return response()->json($shipments, 200);
    }

    /**
     * POST /api/shipments (Hanya Admin & Operator)
     */
    public function shipmentsStore(Request $request)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if (!in_array($user->role, ['admin', 'operator'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated =$request->validate([
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
            'shipment_number' => strtoupper(trim($validated['shipment_number'])),
            'user_id' => $validated['user_id'],
            'carrier_id' => $validated['carrier_id'],
            'origin_location_id' => $validated['origin_location_id'],
            'destination_location_id' => $validated['destination_location_id'],
            'cargo_type' => $validated['cargo_type'],
            'weight' => $validated['weight'],
            'volume' => $validated['volume'],
            'expected_departure_date' => $validated['expected_departure_date'],
            'expected_arrival_date' => $validated['expected_arrival_date'],
            'status' => 'Created',
        ]);

        return response()->json([
            'message' => 'Shipment created.',
            'data' => $shipment,
        ], 201);
    }

    /**
     * GET /api/shipments/{id}
     */
    public function shipmentsShow(Request $request,$id)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $shipment = Shipment::with(['carrier', 'originLocation', 'destinationLocation', 'trackingEvents', 'exceptions'])->find($id);
        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found.'], 404);
        }

        if ($user->role === 'customer' && $shipment->user_id !==$user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json(['data' => $shipment], 200);
    }

    /**
     * PUT/PATCH /api/shipments/{id}
     */
    public function shipmentsUpdate(Request $request,$id)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if (!in_array($user->role, ['admin', 'operator'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found.'], 404);
        }

        $validated =$request->validate([
            'status' => 'sometimes|in:Created,In Transit,Delivered',
            'cargo_type' => 'sometimes|string|max:255',
            'weight' => 'sometimes|numeric|min:0.1',
        ]);

        if (isset($validated['status'])) {
            if (strcasecmp($shipment->status, 'Delivered') === 0 && strcasecmp($validated['status'], 'Delivered') !== 0) {
                return response()->json([
                    'message' => 'Invalid status transition: DELIVERED is terminal.',
                ], 422);
            }
            if (strcasecmp($shipment->status, 'In Transit') === 0 && strcasecmp($validated['status'], 'Created') === 0) {
                return response()->json([
                    'message' => 'Invalid status transition: cannot revert In Transit to Created.',
                ], 422);
            }
        }

        $shipment->update($validated);

        return response()->json([
            'message' => 'Shipment updated.',
            'data' => $shipment,
        ], 200);
    }

    /**
     * DELETE /api/shipments/{id}
     */
    public function shipmentsDestroy(Request $request,$id)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found.'], 404);
        }

        $shipment->delete();

        return response()->json(['message' => 'Shipment deleted.'], 200);
    }

    /**
     * GET /api/shipments/{id}/tracking
     */
    public function trackingIndex(Request $request,$id)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $shipment = Shipment::with('trackingEvents')->find($id);
        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found.'], 404);
        }
        if ($user->role === 'customer' && $shipment->user_id !==$user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json(['data' => $shipment->trackingEvents], 200);
    }

    /**
     * POST /api/shipments/{id}/tracking
     */
    public function trackingStore(Request $request,$id)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if (!in_array($user->role, ['admin', 'operator'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found.'], 404);
        }

        $validated =$request->validate([
            'title' => 'required|string|max:255',
            'location_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $columns = Schema::getColumnListing('tracking_events');
        $data = ['shipment_id' =>$shipment->id];

        if (in_array('created_by', $columns)) $data['created_by'] =$user->id;
        if (in_array('title', $columns)) $data['title'] =$validated['title'];
        if (in_array('status', $columns)) $data['status'] =$validated['title'];
        if (in_array('event_name', $columns)) $data['event_name'] =$validated['title'];
        if (in_array('location_name', $columns)) $data['location_name'] =$validated['location_name'] ?? null;
        if (in_array('location', $columns)) $data['location'] =$validated['location_name'] ?? null;
        if (in_array('description', $columns)) $data['description'] =$validated['description'] ?? null;
        if (in_array('notes', $columns)) $data['notes'] =$validated['description'] ?? null;
        if (in_array('event_time', $columns))$data['event_time'] = now();
        if (in_array('occurred_at', $columns))$data['occurred_at'] = now();

        $event = TrackingEvent::create($data);

        return response()->json([
            'message' => 'Tracking event recorded.',
            'data' => $event,
        ], 201);
    }

    /**
     * GET /api/carriers
     */
    public function carriersIndex(Request $request)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json(['data' => Carrier::all()], 200);
    }

    /**
     * POST /api/carriers
     */
    public function carriersStore(Request $request)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated =$request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:carriers,code',
        ]);

        $carrier = Carrier::create($validated);

        return response()->json(['message' => 'Carrier created.', 'data' => $carrier], 201);
    }

    /**
     * GET /api/locations
     */
    public function locationsIndex(Request $request)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json(['data' => Location::all()], 200);
    }

    /**
     * POST /api/locations
     */
    public function locationsStore(Request $request)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated =$request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location = Location::create($validated);

        return response()->json(['message' => 'Location created.', 'data' => $location], 201);
    }

    /**
     * GET /api/shipments/{id}/exceptions
     */
    public function exceptionsIndex(Request $request,$id)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $shipment = Shipment::with('exceptions')->find($id);
        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found.'], 404);
        }
        if ($user->role === 'customer' && $shipment->user_id !==$user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json(['data' => $shipment->exceptions], 200);
    }

    /**
     * POST /api/shipments/{id}/exceptions
     */
    public function exceptionsStore(Request $request,$id)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if (!in_array($user->role, ['admin', 'operator'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found.'], 404);
        }

        $validated =$request->validate([
            'exception_type' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
        ]);

        $exception = ShipmentException::create([
            'shipment_id' => $shipment->id,
            'exception_type' => $validated['exception_type'],
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'resolved' => false,
        ]);

        return response()->json(['message' => 'Exception recorded.', 'data' => $exception], 201);
    }

    /**
     * PATCH /api/exceptions/{id}
     */
    public function exceptionsResolve(Request $request,$id)
    {
        $user = $this->authenticateUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if (!in_array($user->role, ['admin', 'operator'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $exception = ShipmentException::find($id);
        if (!$exception) {
            return response()->json(['message' => 'Exception not found.'], 404);
        }

        $exception->update([
            'resolved' => $request->boolean('resolved', true),
        ]);

        return response()->json(['message' => 'Exception updated.', 'data' => $exception], 200);
    }
}