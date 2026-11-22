<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pengiriman - CargoTrace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="container mx-auto p-6 max-w-3xl">
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h1 class="text-2xl font-bold text-blue-600 mb-2">CargoTrace - Detail Pelacakan</h1>
            <p class="text-gray-600 mb-4">Nomor Resi: <span class="font-semibold text-black">{{ $shipment->shipment_number }}</span></p>
            
            <div class="grid grid-cols-2 gap-4 border-t pt-4 text-sm">
                <div>
                    <p class="text-gray-500">Jenis Barang:</p>
                    <p class="font-semibold">{{ $shipment->cargo_type }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Status Terkini:</p>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-bold text-xs">{{ $shipment->status }}</span>
                </div>
                <div>
                    <p class="text-gray-500">Asal:</p>
                    <p class="font-semibold">{{ $shipment->originLocation->name ?? '-' }} ({{ $shipment->originLocation->city ?? '' }})</p>
                </div>
                <div>
                    <p class="text-gray-500">Tujuan:</p>
                    <p class="font-semibold">{{ $shipment->destinationLocation->name ?? '-' }} ({{ $shipment->destinationLocation->city ?? '' }})</p>
                </div>
            </div>
        </div>

        <!-- Bagian Peta Interaktif (Leaflet.js) -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Peta Rute Perjalanan</h2>
            <div id="map" class="w-full h-80 rounded-lg z-0"></div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Riwayat Perjalanan (Tracking Events)</h2>
            @if($shipment->trackingEvents->isEmpty())
                <p class="text-gray-500 text-sm">Belum ada riwayat pergerakan untuk resi ini.</p>
            @else
                <ul class="border-l-2 border-blue-500 pl-4 space-y-4">
                    @foreach($shipment->trackingEvents as $event)
                        <li>
                            <p class="text-xs text-gray-400">{{ $event->created_at }}</p>
                            <p class="font-semibold text-blue-600">{{ $event->status }} - {{ $event->location_description }}</p>
                            <p class="text-sm text-gray-600">{{ $event->description }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Bagian Informasi Kendala (Shipment Exceptions) -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-bold text-red-600 mb-4">Kendala Pengiriman (Exceptions)</h2>
            
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($shipment->exceptions->isEmpty())
                <p class="text-gray-500 text-sm">Tidak ada kendala atau hambatan yang tercatat pada pengiriman ini.</p>
            @else
                <div class="space-y-3 mb-6">
                    @foreach($shipment->exceptions as $ex)
                        <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded text-sm">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-red-700">{{ $ex->exception_type }}</span>
                                <span class="px-2 py-0.5 bg-red-200 text-red-800 rounded text-xs font-semibold">{{ $ex->severity }}</span>
                            </div>
                            <p class="text-gray-700">{{ $ex->description }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $ex->created_at }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- FORM KENDALA: HANYA MUNCUL JIKA USER LOGIN DAN ROLE-NYA OPERATOR/ADMIN -->
            @auth
                @if(auth()->user()->role === 'operator' || auth()->user()->role === 'admin')
                    <div class="mt-6 border-t pt-4">
                        <h3 class="text-md font-semibold text-gray-800 mb-3">Tambah Catatan Kendala Baru</h3>
                        <form action="/shipments/{{ $shipment->id }}/exceptions" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <input type="text" name="exception_type" placeholder="Jenis Kendala (Contoh: Weather Delay)" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-red-500">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="severity" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                    <option value="LOW">LOW</option>
                                    <option value="MEDIUM">MEDIUM</option>
                                    <option value="HIGH">HIGH</option>
                                    <option value="CRITICAL">CRITICAL</option>
                                </select>
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm transition">
                                    Simpan Kendala
                                </button>
                            </div>
                            <div>
                                <textarea name="description" placeholder="Deskripsi detail kendala..." rows="2" required
                                          class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-red-500"></textarea>
                            </div>
                        </form>
                    </div>
                @endif
            @endauth

        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const originLat = {{ $shipment->originLocation->latitude ?? -6.2088 }};
        const originLng = {{ $shipment->originLocation->longitude ?? 106.8456 }};
        const destLat = {{ $shipment->destinationLocation->latitude ?? -7.2575 }};
        const destLng = {{ $shipment->destinationLocation->longitude ?? 112.7521 }};

        const map = L.map('map').setView([originLat, originLng], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const originMarker = L.marker([originLat, originLng]).addTo(map)
            .bindPopup("<b>Asal (Origin):</b><br>{{ $shipment->originLocation->name ?? 'Lokasi Asal' }}");

        const destMarker = L.marker([destLat, destLng]).addTo(map)
            .bindPopup("<b>Tujuan (Destination):</b><br>{{ $shipment->destinationLocation->name ?? 'Lokasi Tujuan' }}");

        const latlngs = [
            [originLat, originLng],
            [destLat, destLng]
        ];
        const polyline = L.polyline(latlngs, {color: 'blue', weight: 4, dashArray: '5, 5'}).addTo(map);

        map.fitBounds(polyline.getBounds(), {padding: [50, 50]});
    </script>
</body>
</html>