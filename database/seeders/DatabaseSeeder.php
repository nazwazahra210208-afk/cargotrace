<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Location;
use App\Models\Carrier;
use App\Models\Shipment;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Administrator
        $admin = User::updateOrCreate(
            ['email' => 'admin@cargotrace.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]
        );

        // 2. Akun Logistics Operator
        $operator = User::updateOrCreate(
            ['email' => 'operator@cargotrace.com'],
            [
                'name' => 'Logistics Operator',
                'password' => Hash::make('password123'),
                'role' => 'operator'
            ]
        );

        // 3. Daftar Banyak Akun Customer Sekaligus Langsung dari Database
        $customersData = [
            ['name' => 'Luxury Customer', 'email' => 'customer@cargotrace.com'],
            ['name' => 'PT Maju Sejahtera Abadi', 'email' => 'maju@cargotrace.com'],
            ['name' => 'Global Maritime Express', 'email' => 'global@cargotrace.com'],
            ['name' => 'Nusantara Trading Co.', 'email' => 'nusantara@cargotrace.com'],
        ];

        $createdCustomers = [];
        foreach ($customersData as $cust) {
            $createdCustomers[] = User::updateOrCreate(
                ['email' => $cust['email']],
                [
                    'name' => $cust['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'customer'
                ]
            );
        }

        // 4. Master Data Pelabuhan / Lokasi Hub di Seluruh Dunia
        $locations = [
            ['name' => 'Pelabuhan Tanjung Priok', 'city' => 'Jakarta', 'country' => 'Indonesia', 'latitude' => -6.1054, 'longitude' => 106.8856],
            ['name' => 'Pelabuhan Tanjung Perak', 'city' => 'Surabaya', 'country' => 'Indonesia', 'latitude' => -7.2000, 'longitude' => 112.7333],
            ['name' => 'Port of Singapore', 'city' => 'Singapore', 'country' => 'Singapore', 'latitude' => 1.290270, 'longitude' => 103.851959],
            ['name' => 'Port of Shanghai', 'city' => 'Shanghai', 'country' => 'China', 'latitude' => 31.2304, 'longitude' => 121.4737],
            ['name' => 'Port of Rotterdam', 'city' => 'Rotterdam', 'country' => 'Netherlands', 'latitude' => 51.9244, 'longitude' => 4.4777],
            ['name' => 'Port of Los Angeles', 'city' => 'Los Angeles', 'country' => 'United States', 'latitude' => 33.7430, 'longitude' => -118.2673],
            ['name' => 'Port of Dubai (Jebel Ali)', 'city' => 'Dubai', 'country' => 'United Arab Emirates', 'latitude' => 25.0112, 'longitude' => 55.0601],
            ['name' => 'Port of Busan', 'city' => 'Busan', 'country' => 'South Korea', 'latitude' => 35.1796, 'longitude' => 129.0756],
            ['name' => 'Port of Hamburg', 'city' => 'Hamburg', 'country' => 'Germany', 'latitude' => 53.5511, 'longitude' => 9.9937],
            ['name' => 'Port of Tokyo', 'city' => 'Tokyo', 'country' => 'Japan', 'latitude' => 35.6762, 'longitude' => 139.6503],
        ];

        $createdLocations = [];
        foreach ($locations as $loc) {
            $createdLocations[] = Location::firstOrCreate(['name' => $loc['name']], $loc);
        }

        // 5. Master Data Carrier / Perusahaan Pelayaran Global
        $carriers = [
            ['name' => 'Maersk Line', 'code' => 'MSK'],
            ['name' => 'Mediterranean Shipping Company (MSC)', 'code' => 'MSC'],
            ['name' => 'CMA CGM', 'code' => 'CMG'],
            ['name' => 'COSCO Shipping', 'code' => 'COS'],
            ['name' => 'Hapag-Lloyd', 'code' => 'HPL'],
            ['name' => 'Evergreen Marine', 'code' => 'EMC'],
            ['name' => 'PT Pelni (Persero)', 'code' => 'PLN'],
            ['name' => 'PT Samudera Indonesia', 'code' => 'SMD'],
        ];

        $createdCarriers = [];
        foreach ($carriers as $car) {
            $createdCarriers[] = Carrier::firstOrCreate(['code' => $car['code']], $car);
        }

        // 6. Data Contoh Pengiriman (Shipments)
        if (count($createdLocations) >= 2 && count($createdCarriers) > 0 && count($createdCustomers) > 0) {
            $shipments = [
                [
                    'shipment_number' => 'CRG-2026-001',
                    'user_id' => $createdCustomers[0]->id,
                    'carrier_id' => $createdCarriers[0]->id,
                    'origin_location_id' => $createdLocations[0]->id,
                    'destination_location_id' => $createdLocations[2]->id,
                    'cargo_type' => 'Suku Cadang Mesin Industri',
                    'weight' => 1250.00,
                    'volume' => 15.50,
                    'expected_departure_date' => now()->subDays(2),
                    'expected_arrival_date' => now()->addDays(3),
                    'status' => 'In Transit',
                ],
                [
                    'shipment_number' => 'CRG-2026-002',
                    'user_id' => $createdCustomers[1]->id,
                    'carrier_id' => $createdCarriers[1]->id,
                    'origin_location_id' => $createdLocations[3]->id,
                    'destination_location_id' => $createdLocations[1]->id,
                    'cargo_type' => 'Komponen Elektronik & Gadget',
                    'weight' => 850.50,
                    'volume' => 8.20,
                    'expected_departure_date' => now()->subDays(5),
                    'expected_arrival_date' => now()->subDays(1),
                    'status' => 'Delivered',
                ],
            ];

            foreach ($shipments as $shp) {
                Shipment::firstOrCreate(['shipment_number' => $shp['shipment_number']], $shp);
            }
        }
    }
}