<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    public function getWeather($lat, $lon)
    {
        $apiKey = env('WEATHER_API_KEY');

        // Jika API key belum diatur, kembalikan data dummy/aman
        if (!$apiKey) {
            return [
                'condition' => 'Clear (Mock)',
                'temp' => '28°C',
                'description' => 'API Key belum dikonfigurasi.'
            ];
        }

        try {
            // Melakukan request HTTP menggunakan Laravel HTTP Client dengan timeout 5 detik
            $response = Http::timeout(5)->get("https://api.openweathermap.org/data/2.5/weather", [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $apiKey,
                'units' => 'metric'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'condition' => $data['weather'][0]['main'] ?? 'Unknown',
                    'temp' => ($data['main']['temp'] ?? 0) . '°C',
                    'description' => ucfirst($data['weather'][0]['description'] ?? '')
                ];
            }
        } catch (\Exception $e) {
            // Tangani error koneksi / timeout agar aplikasi tidak crash
            Log::error('Weather API Error: ' . $e->getMessage());
        }

        return [
                    'condition' => 'Service Unavailable',
                    'temp' => '-',
                    'description' => 'Gagal memuat data cuaca eksternal.'
               ];
    }
}