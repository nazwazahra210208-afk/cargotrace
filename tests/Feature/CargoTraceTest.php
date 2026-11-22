<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CargoTraceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials_and_fails_with_invalid(): void
    {
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors();

        $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ])->assertRedirect('/admin/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_role_authorization_blocks_unauthorized_access(): void
    {
        $customer = User::create([
            'name' => 'Customer Test',
            'email' => 'customer@test.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->get('/admin/dashboard')
            ->assertStatus(403);
    }

    public function test_operator_can_create_shipment_and_record_tracking_event(): void
    {
        $operator = User::create([
            'name' => 'Operator Test',
            'email' => 'operator@test.com',
            'password' => Hash::make('password123'),
            'role' => 'operator',
        ]);

        $customer = User::create([
            'name' => 'Customer Test',
            'email' => 'customer@test.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $carrier = Carrier::create([
            'name' => 'Maersk Line',
            'code' => 'MSK',
        ]);

        $origin = Location::create([
            'name' => 'Port of Tanjung Priok',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'latitude' => -6.1045,
            'longitude' => 106.8803,
        ]);

        $destination = Location::create([
            'name' => 'Port of Singapore',
            'city' => 'Singapore',
            'country' => 'Singapore',
            'latitude' => 1.29027,
            'longitude' => 103.851959,
        ]);

        $this->actingAs($operator)->post('/shipments', [
            'shipment_number' => 'CRG-TEST-001',
            'user_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'cargo_type' => 'Electronics Container',
            'weight' => 1500,
            'volume' => 24.5,
            'expected_departure_date' => now()->format('Y-m-d'),
            'expected_arrival_date' => now()->addDays(5)->format('Y-m-d'),
        ])->assertRedirect('/shipments');

        $shipment = Shipment::where('shipment_number', 'CRG-TEST-001')->firstOrFail();
        $this->assertEquals('Created', $shipment->status);

        $this->actingAs($operator)
            ->from('/track/' . $shipment->shipment_number)
            ->post('/shipments/' . $shipment->id . '/tracking-events', [
                'title' => 'Vessel Departed',
                'location_name' => 'Port of Tanjung Priok',
                'description' => 'Container loaded and departed on schedule.',
            ])
            ->assertRedirect('/track/' . $shipment->shipment_number);

        $this->assertGreaterThan(0, TrackingEvent::where('shipment_id', $shipment->id)->count());
    }

    public function test_delivered_shipment_cannot_revert_to_previous_status(): void
    {
        $operator = User::create([
            'name' => 'Operator Test',
            'email' => 'operator@test.com',
            'password' => Hash::make('password123'),
            'role' => 'operator',
        ]);

        $customer = User::create([
            'name' => 'Customer Test',
            'email' => 'customer@test.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $carrier = Carrier::create(['name' => 'MSC', 'code' => 'MSC']);
        $origin = Location::create(['name' => 'Origin Port', 'city' => 'Jakarta', 'country' => 'ID', 'latitude' => -6.1, 'longitude' => 106.8]);
        $dest = Location::create(['name' => 'Dest Port', 'city' => 'Singapore', 'country' => 'SG', 'latitude' => 1.29, 'longitude' => 103.85]);

        $shipment = Shipment::create([
            'shipment_number' => 'CRG-DELIV-001',
            'user_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $dest->id,
            'cargo_type' => 'Textiles',
            'weight' => 800,
            'volume' => 12,
            'expected_departure_date' => now()->subDays(4)->format('Y-m-d'),
            'expected_arrival_date' => now()->subDay()->format('Y-m-d'),
            'status' => 'Delivered',
        ]);

        $this->actingAs($operator)->post('/shipments/' . $shipment->id . '/status', [
            'status' => 'In Transit',
        ])->assertSessionHasErrors('status');

        $this->assertEquals('Delivered', $shipment->fresh()->status);
    }

    public function test_rest_api_authentication_and_shipment_authorization(): void
    {
        $customerA = User::create([
            'name' => 'Customer A',
            'email' => 'a@test.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $customerB = User::create([
            'name' => 'Customer B',
            'email' => 'b@test.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $carrier = Carrier::create(['name' => 'CMA CGM', 'code' => 'CMA']);
        $origin = Location::create(['name' => 'Origin', 'city' => 'Jakarta', 'country' => 'ID', 'latitude' => -6.1, 'longitude' => 106.8]);
        $dest = Location::create(['name' => 'Dest', 'city' => 'Busan', 'country' => 'KR', 'latitude' => 35.1, 'longitude' => 129.0]);

        $shipmentA = Shipment::create([
            'shipment_number' => 'CRG-API-001',
            'user_id' => $customerA->id,
            'carrier_id' => $carrier->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $dest->id,
            'cargo_type' => 'Machinery',
            'weight' => 2000,
            'volume' => 30,
            'expected_departure_date' => now()->format('Y-m-d'),
            'expected_arrival_date' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'In Transit',
        ]);

        $this->getJson('/api/shipments')->assertStatus(401);

        $loginRes = $this->postJson('/api/login', [
            'email' => 'b@test.com',
            'password' => 'password123',
        ])->assertStatus(200);

        $tokenB = $loginRes->json('token');

        $this->withHeader('Authorization', 'Bearer ' . $tokenB)
            ->getJson('/api/shipments/' . $shipmentA->id)
            ->assertStatus(403);
    }
}