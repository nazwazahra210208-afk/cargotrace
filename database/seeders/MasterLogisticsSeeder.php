<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Carrier;

class MasterLogisticsSeeder extends Seeder
{
    public function run()
    {
        // 1. Daftar Pelabuhan Utama di Seluruh Dunia & Indonesia
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

        foreach ($locations as $loc) {
            Location::firstOrCreate(['name' => $loc['name']], $loc);
        }

        // 2. Daftar Perusahaan Pelayaran / Carrier Global & Nasional
        $carriers = [
            ['name' => 'Maersk Line', 'code' => 'MSK', 'contact' => '+45 3363 3363'],
            ['name' => 'Mediterranean Shipping Company (MSC)', 'code' => 'MSC', 'contact' => '+41 22 703 8888'],
            ['name' => 'CMA CGM', 'code' => 'CMG', 'contact' => '+33 4 88 91 90 00'],
            ['name' => 'COSCO Shipping', 'code' => 'COS', 'contact' => '+86 21 6596 6666'],
            ['name' => 'Hapag-Lloyd', 'code' => 'HPL', 'contact' => '+49 40 3001 0'],
            ['name' => 'Evergreen Marine', 'code' => 'EMC', 'contact' => '+886 2 2505 7777'],
            ['name' => 'PT Pelni (Persero)', 'code' => 'PLN', 'contact' => '+62 21 162'],
            ['name' => 'PT Samudera Indonesia', 'code' => 'SMD', 'contact' => '+62 21 541 3000'],
        ];

        foreach ($carriers as $car) {
            Carrier::firstOrCreate(['code' => $car['code']], $car);
        }
    }
}