<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale', 'id') == 'en' ? 'Shipment Tracking - CargoTrace' : 'Pelacakan Pengiriman - CargoTrace' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet.js CSS & JS untuk Peta Dunia Interaktif -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <style>
        .light-glass {
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(191, 219, 254, 0.6);
        }
        #route-map {
            height: 360px;
            width: 100%;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen relative bg-cover bg-center" style="background-image: linear-gradient(to bottom, rgba(241, 245, 249, 0.88), rgba(226, 232, 240, 0.96)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1920&auto=format&fit=crop');">

    <!-- Navbar -->
    <nav class="light-glass border-b border-blue-200 sticky top-0 z-50 px-6 py-4 shadow-sm">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-900 text-white p-2 rounded-xl shadow">🚢</div>
                <h1 class="text-xl font-extrabold tracking-wide text-blue-950">
                    CargoTrace <span class="text-blue-700 font-medium">— {{ session('locale', 'id') == 'en' ? 'Shipment Tracking' : 'Pelacakan Resi' }}</span>
                </h1>
            </div>

            <div class="flex items-center space-x-3">
                <div class="flex space-x-2 text-sm bg-white/90 px-3.5 py-1.5 rounded-full border border-blue-200 shadow-inner font-semibold">
                    <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
                    <span class="text-blue-200">|</span>
                    <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
                </div>

                @auth
                    @php
                        $dashboardUrl = auth()->user()->role === 'admin'
                            ? route('admin.dashboard')
                            : url('/dashboard');
                    @endphp
                    <a href="{{ $dashboardUrl }}" class="bg-blue-950 hover:bg-blue-900 text-white px-4 py-2 rounded-full text-sm font-semibold transition shadow-md">
                        Dashboard
                    </a>
                @else
                    <a href="{{ url('/') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-4 py-2 rounded-full text-sm font-semibold transition shadow-md">
                        {{ session('locale', 'id') == 'en' ? 'Home' : 'Beranda' }}
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container mx-auto p-6 space-y-8 relative z-10">

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 px-5 py-4 rounded-2xl text-sm font-semibold shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-800 px-5 py-4 rounded-2xl text-sm space-y-1 font-semibold">
                @foreach($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        @php
            $statusRaw = strtoupper(str_replace(' ', '_', $shipment->status ?? ''));
            $isDelivered = str_contains($statusRaw, 'DELIVERED');
            $isInTransit = str_contains($statusRaw, 'TRANSIT');
            $badgeClass = match (true) {
                $isDelivered => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                $isInTransit => 'bg-blue-100 text-blue-900 border-blue-300',
                default => 'bg-amber-100 text-amber-900 border-amber-300',
            };

            $isDelayed = (bool) ($delayInfo['is_delayed'] ?? false);
            $delayHours = (int) ($delayInfo['delay_hours'] ?? 0);
            $delaySeverity = $delayInfo['severity'] ?? 'ON_TIME';
            $delayReason = $delayInfo['reason'] ?? '-';
        @endphp

        <!-- Header Ringkasan Pengiriman -->
        <div class="light-glass rounded-2xl p-8 shadow-lg border-l-8 border-blue-900 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-3 mb-2">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider bg-blue-950 text-blue-100 px-3 py-1 rounded-lg">
                        {{ $shipment->shipment_number }}
                    </span>
                    <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full border {{ $badgeClass }}">
                        {{ $shipment->status }}
                    </span>
                    @if($isDelayed)
                        <span class="text-xs font-extrabold uppercase tracking-wider px-3 py-1 rounded-full border bg-rose-100 text-rose-900 border-rose-300">
                            ⏰ DELAYED (+{{ $delayHours }}H • {{ $delaySeverity }})
                        </span>
                    @else
                        <span class="text-xs font-extrabold uppercase tracking-wider px-3 py-1 rounded-full border bg-emerald-100 text-emerald-900 border-emerald-300">
                            ✓ ON SCHEDULE
                        </span>
                    @endif
                </div>

                <h2 class="text-2xl md:text-3xl font-black text-blue-950">
                    {{ $shipment->originLocation->name ?? '-' }}
                    <span class="text-blue-600 mx-2">→</span>
                    {{ $shipment->destinationLocation->name ?? '-' }}
                </h2>

                <p class="text-slate-600 text-sm md:text-base font-medium mt-1">
                    {{ session('locale', 'id') == 'en' ? 'Carrier:' : 'Armada / Carrier:' }}
                    <strong class="text-blue-950">{{ $shipment->carrier->name ?? '-' }}</strong>
                    @if(!empty($shipment->carrier->code))
                        <span class="text-xs font-mono text-slate-500">({{ $shipment->carrier->code }})</span>
                    @endif
                </p>
            </div>

            <div class="bg-white/90 border border-blue-200 rounded-2xl p-4 min-w-[240px] shadow-sm space-y-1.5 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500 font-medium">{{ session('locale', 'id') == 'en' ? 'Departure:' : 'Keberangkatan:' }}</span>
                    <span class="font-bold text-blue-950">{{ $shipment->expected_departure_date ?? '-' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500 font-medium">ETA:</span>
                    <span class="font-bold text-blue-950">{{ $shipment->expected_arrival_date ?? '-' }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-500 font-medium">{{ session('locale', 'id') == 'en' ? 'Cargo:' : 'Muatan:' }}</span>
                    <span class="font-bold text-blue-950 text-right">{{ $shipment->cargo_type ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Banner Analisis ETA & Delay Detection (Business Rule Deterministik) -->
        <div class="light-glass rounded-2xl p-5 shadow-md border {{ $isDelayed ? 'border-rose-300 bg-rose-50/70' : 'border-emerald-300 bg-emerald-50/60' }} flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-base">{{ $isDelayed ? '🚨' : '⏱️' }}</span>
                    <h3 class="text-sm font-extrabold uppercase tracking-wider {{ $isDelayed ? 'text-rose-950' : 'text-emerald-950' }}">
                        {{ session('locale', 'id') == 'en' ? 'ETA & Delay Rule Evaluation' : 'Evaluasi Ketepatan Waktu & Aturan Delay' }}
                    </h3>
                    <span class="text-[11px] font-mono font-bold px-2.5 py-0.5 rounded-full {{ $isDelayed ? 'bg-rose-200 text-rose-950' : 'bg-emerald-200 text-emerald-950' }}">
                        {{ $isDelayed ? 'DELAYED (' . $delaySeverity . ' • ' . $delayHours . ' jam)' : 'ON TIME' }}
                    </span>
                </div>
                <p class="text-sm font-medium {{ $isDelayed ? 'text-rose-900' : 'text-emerald-900' }}">
                    {{ $delayReason }}
                </p>
            </div>
            <div class="text-xs font-mono text-slate-600 bg-white/80 border border-slate-200 px-3.5 py-2 rounded-xl">
                Rule: 0–6h LOW • >6–24h MEDIUM • >24h HIGH
            </div>
        </div>

        <!-- Panel Operasional Khusus Logistics Operator -->
        @if(auth()->check() && auth()->user()->role === 'operator')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Update Status Pengiriman (Dengan Penguncian Status Terminal Delivered) -->
                <div class="light-glass rounded-2xl p-6 shadow-md border border-blue-300 space-y-4">
                    <h3 class="text-base font-extrabold text-blue-950 flex items-center space-x-2">
                        <span>🔄</span>
                        <span>{{ session('locale', 'id') == 'en' ? 'Update Shipment Status' : 'Perbarui Status Pengiriman' }}</span>
                    </h3>

                    @if($isDelivered)
                        <div class="bg-emerald-50 border border-emerald-300 rounded-xl p-4 space-y-1.5">
                            <p class="text-xs font-extrabold uppercase text-emerald-900 flex items-center gap-1.5">
                                <span>🔒</span>
                                <span>{{ session('locale', 'id') == 'en' ? 'Terminal Status Locked' : 'Status Terminal Terkunci' }}</span>
                            </p>
                            <p class="text-xs text-emerald-800 leading-relaxed">
                                {{ session('locale', 'id') == 'en'
                                    ? 'This shipment has reached DELIVERED status and cannot be reverted to previous states.'
                                    : 'Pengiriman ini sudah berstatus DELIVERED (final) sehingga tidak dapat dikembalikan ke status sebelumnya.' }}
                            </p>
                        </div>
                    @else
                        <form action="{{ route('operator.shipments.status', $shipment->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <select name="status" class="w-full rounded-xl border border-blue-200 bg-white px-3.5 py-2.5 text-sm font-bold text-blue-950 focus:outline-none focus:ring-2 focus:ring-blue-900">
                                @if(!$isInTransit)
                                    <option value="Created" {{ $shipment->status == 'Created' ? 'selected' : '' }}>Created</option>
                                @endif
                                <option value="In Transit" {{ $shipment->status == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="Delivered" {{ $shipment->status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                            </select>
                            <button type="submit" class="w-full bg-blue-950 hover:bg-blue-900 text-white py-2.5 rounded-xl text-sm font-bold shadow transition">
                                {{ session('locale', 'id') == 'en' ? 'Save Status' : 'Simpan Status' }}
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Form Tambah Tracking Event -->
                <div class="lg:col-span-2 light-glass rounded-2xl p-6 shadow-md border border-blue-300 space-y-4">
                    <h3 class="text-base font-extrabold text-blue-950 flex items-center space-x-2">
                        <span>➕</span>
                        <span>{{ session('locale', 'id') == 'en' ? 'Add Tracking Event' : 'Tambah Catatan Pergerakan (Tracking Event)' }}</span>
                    </h3>
                    <form action="{{ route('operator.shipments.events', $shipment->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @csrf
                        <input type="text" name="title" required placeholder="{{ session('locale', 'id') == 'en' ? 'Event title (e.g. Departed Origin Port)' : 'Judul aktivitas (mis. Kapal Berangkat dari Pelabuhan)' }}"
                            class="rounded-xl border border-blue-200 bg-white px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">

                        <input type="text" name="location_name" placeholder="{{ session('locale', 'id') == 'en' ? 'Port / Location (optional)' : 'Lokasi / Pelabuhan (opsional)' }}"
                            class="rounded-xl border border-blue-200 bg-white px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">

                        <input type="text" name="description" placeholder="{{ session('locale', 'id') == 'en' ? 'Additional notes (optional)' : 'Keterangan tambahan (opsional)' }}"
                            class="md:col-span-2 rounded-xl border border-blue-200 bg-white px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">

                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow transition">
                                {{ session('locale', 'id') == 'en' ? 'Record Event' : 'Catat Pergerakan' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Form Catat Kendala / Exception oleh Operator -->
                <div class="lg:col-span-3 light-glass rounded-2xl p-6 shadow-md border border-amber-300 space-y-4">
                    <h3 class="text-base font-extrabold text-amber-950 flex items-center space-x-2">
                        <span>⚠️</span>
                        <span>{{ session('locale', 'id') == 'en' ? 'Report Operational Exception' : 'Catat Kendala Operasional (Exception)' }}</span>
                    </h3>
                    <form action="{{ url('/shipments/' . $shipment->id . '/exceptions') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @csrf
                        <input type="text" name="exception_type" required placeholder="{{ session('locale', 'id') == 'en' ? 'Exception type (e.g. WEATHER_DELAY / CUSTOMS_HOLD)' : 'Jenis kendala (mis. WEATHER_DELAY / CUSTOMS_HOLD)' }}"
                            class="rounded-xl border border-amber-200 bg-white px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-600">

                        <select name="severity" required class="rounded-xl border border-amber-200 bg-white px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-600">
                            <option value="LOW">LOW</option>
                            <option value="MEDIUM" selected>MEDIUM</option>
                            <option value="HIGH">HIGH</option>
                            <option value="CRITICAL">CRITICAL</option>
                        </select>

                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow transition">
                            {{ session('locale', 'id') == 'en' ? 'Submit Exception' : 'Simpan Kendala' }}
                        </button>

                        <input type="text" name="description" required placeholder="{{ session('locale', 'id') == 'en' ? 'Describe the issue and impact on shipment...' : 'Jelaskan detail kendala dan dampaknya terhadap pengiriman...' }}"
                            class="md:col-span-3 rounded-xl border border-amber-200 bg-white px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-600">
                    </form>
                </div>
            </div>
        @endif

        <!-- Detail Kargo & Prakiraan Cuaca Pelabuhan Tujuan -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Spesifikasi Kargo & Rute -->
            <div class="lg:col-span-2 light-glass rounded-2xl p-6 shadow-md border border-blue-200 space-y-4">
                <h3 class="text-lg font-extrabold text-blue-950 flex items-center space-x-2">
                    <span>📦</span>
                    <span>{{ session('locale', 'id') == 'en' ? 'Shipment & Route Specifications' : 'Spesifikasi Pengiriman & Rute' }}</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                    <div class="bg-blue-50/80 border border-blue-100 rounded-xl p-4">
                        <p class="text-[11px] uppercase font-bold text-blue-800">{{ session('locale', 'id') == 'en' ? 'Origin' : 'Pelabuhan Asal' }}</p>
                        <p class="text-sm font-extrabold text-blue-950 mt-1">{{ $shipment->originLocation->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $shipment->originLocation->city ?? '' }}{{ !empty($shipment->originLocation->country) ? ', ' . $shipment->originLocation->country : '' }}</p>
                    </div>

                    <div class="bg-blue-50/80 border border-blue-100 rounded-xl p-4">
                        <p class="text-[11px] uppercase font-bold text-blue-800">{{ session('locale', 'id') == 'en' ? 'Destination' : 'Pelabuhan Tujuan' }}</p>
                        <p class="text-sm font-extrabold text-blue-950 mt-1">{{ $shipment->destinationLocation->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $shipment->destinationLocation->city ?? '' }}{{ !empty($shipment->destinationLocation->country) ? ', ' . $shipment->destinationLocation->country : '' }}</p>
                    </div>

                    <div class="bg-blue-50/80 border border-blue-100 rounded-xl p-4">
                        <p class="text-[11px] uppercase font-bold text-blue-800">{{ session('locale', 'id') == 'en' ? 'Weight' : 'Berat Kargo' }}</p>
                        <p class="text-lg font-black text-blue-950 mt-1">{{ number_format((float) ($shipment->weight ?? 0), 2) }} <span class="text-xs font-semibold">kg</span></p>
                    </div>

                    <div class="bg-blue-50/80 border border-blue-100 rounded-xl p-4">
                        <p class="text-[11px] uppercase font-bold text-blue-800">{{ session('locale', 'id') == 'en' ? 'Volume' : 'Volume Kargo' }}</p>
                        <p class="text-lg font-black text-blue-950 mt-1">{{ number_format((float) ($shipment->volume ?? 0), 2) }} <span class="text-xs font-semibold">m³</span></p>
                    </div>
                </div>
            </div>

            <!-- Prakiraan Cuaca Pelabuhan Tujuan -->
            <div class="light-glass rounded-2xl p-6 shadow-md border border-blue-200 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-blue-950 flex items-center space-x-2">
                        <span>🌤️</span>
                        <span>{{ session('locale', 'id') == 'en' ? 'Destination Weather' : 'Cuaca Pelabuhan Tujuan' }}</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ $shipment->destinationLocation->name ?? '-' }}
                        @if(!empty($shipment->destinationLocation->latitude) && !empty($shipment->destinationLocation->longitude))
                            <span class="font-mono">({{ $shipment->destinationLocation->latitude }}, {{ $shipment->destinationLocation->longitude }})</span>
                        @endif
                    </p>
                </div>

                @if(!empty($weather) && is_array($weather))
                    @php
                        $rawTemp = (string) ($weather['temperature'] ?? $weather['temp'] ?? '-');
                        $cleanTemp = str_contains($rawTemp, '°') ? $rawTemp : ($rawTemp !== '-' ? $rawTemp . '°C' : '-');
                    @endphp
                    <div class="mt-4 bg-blue-50/90 border border-blue-200 rounded-xl p-4 space-y-2">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-3xl font-black text-blue-950">
                                {{ $cleanTemp }}
                            </span>
                            <span class="text-xs font-bold uppercase px-2.5 py-1 rounded-full bg-blue-900 text-white">
                                {{ $weather['condition'] ?? $weather['description'] ?? $weather['weather'] ?? 'Clear' }}
                            </span>
                        </div>
                        @if(isset($weather['wind_speed']))
                            <p class="text-xs text-slate-600 font-medium">
                                {{ session('locale', 'id') == 'en' ? 'Wind Speed:' : 'Kecepatan Angin:' }}
                                <strong class="text-blue-950">{{ $weather['wind_speed'] }} km/h</strong>
                            </p>
                        @endif
                        @if(isset($weather['humidity']))
                            <p class="text-xs text-slate-600 font-medium">
                                {{ session('locale', 'id') == 'en' ? 'Humidity:' : 'Kelembapan:' }}
                                <strong class="text-blue-950">{{ $weather['humidity'] }}%</strong>
                            </p>
                        @endif
                    </div>
                @else
                    <div class="mt-4 bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-500">
                        {{ session('locale', 'id') == 'en' ? 'Weather data is currently unavailable for this destination.' : 'Data cuaca belum tersedia untuk pelabuhan tujuan ini.' }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Peta Dunia Rute Pengiriman Maritim -->
        <div class="light-glass rounded-2xl p-6 shadow-md border border-blue-200 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <h3 class="text-lg font-extrabold text-blue-950 flex items-center space-x-2">
                    <span>🗺️</span>
                    <span>{{ session('locale', 'id') == 'en' ? 'Global Maritime Route Map' : 'Peta Dunia Rute Pengiriman Maritim' }}</span>
                </h3>
                <div class="flex items-center space-x-4 text-xs font-bold text-slate-600">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span> {{ session('locale', 'id') == 'en' ? 'Origin Port' : 'Pelabuhan Asal' }}</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-rose-600 inline-block"></span> {{ session('locale', 'id') == 'en' ? 'Destination Port' : 'Pelabuhan Tujuan' }}</span>
                </div>
            </div>

            <div id="route-map" class="rounded-2xl border border-blue-200 shadow-inner overflow-hidden"></div>
        </div>

        <!-- Linimasa Tracking Events & Kendala (Exceptions) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Tracking Events Timeline -->
            <div class="light-glass rounded-2xl p-6 shadow-md border border-blue-200">
                <h3 class="text-lg font-extrabold text-blue-950 mb-5 flex items-center space-x-2">
                    <span>🧭</span>
                    <span>{{ session('locale', 'id') == 'en' ? 'Tracking Events Timeline' : 'Linimasa Pergerakan (Tracking Events)' }}</span>
                </h3>

                @if(isset($shipment->trackingEvents) && $shipment->trackingEvents->count() > 0)
                    <div class="space-y-4 border-l-2 border-blue-300 ml-2.5 pl-5">
                        @foreach($shipment->trackingEvents as $event)
                            @php
                                $eventTitle = $event->title ?? $event->status ?? $event->event_name ?? (session('locale', 'id') == 'en' ? 'Shipment Update' : 'Pembaruan Pengiriman');
                                $eventDesc = $event->description ?? $event->notes ?? $event->remarks ?? '';
                                $eventLoc = $event->location_name ?? $event->location ?? '';
                                $eventTime = $event->event_time ?? $event->occurred_at ?? $event->created_at;
                            @endphp
                            <div class="relative bg-white/90 border border-blue-100 rounded-xl p-4 shadow-sm">
                                <span class="w-3.5 h-3.5 bg-blue-900 border-2 border-white rounded-full absolute -left-[27px] top-5 shadow"></span>
                                <div class="flex flex-wrap justify-between items-start gap-2">
                                    <h4 class="font-extrabold text-blue-950 text-sm">{{ $eventTitle }}</h4>
                                    @if(!empty($eventTime))
                                        <span class="text-xs font-mono text-slate-500">
                                            {{ is_object($eventTime) ? $eventTime->format('d M Y H:i') : $eventTime }}
                                        </span>
                                    @endif
                                </div>
                                @if(!empty($eventLoc))
                                    <p class="text-xs font-semibold text-blue-800 mt-1">📍 {{ $eventLoc }}</p>
                                @endif
                                @if(!empty($eventDesc))
                                    <p class="text-sm text-slate-600 mt-1.5">{{ $eventDesc }}</p>
                                @endif
                                @if(!empty($event->creator->name))
                                    <p class="text-[11px] text-slate-400 mt-2">
                                        {{ session('locale', 'id') == 'en' ? 'Updated by:' : 'Dicatat oleh:' }} {{ $event->creator->name }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 text-center text-sm text-slate-500">
                        {{ session('locale', 'id') == 'en' ? 'No tracking events recorded yet for this shipment.' : 'Belum ada catatan linimasa pergerakan untuk pengiriman ini.' }}
                    </div>
                @endif
            </div>

            <!-- Operational Exceptions -->
            <div class="light-glass rounded-2xl p-6 shadow-md border border-blue-200">
                <h3 class="text-lg font-extrabold text-blue-950 mb-5 flex items-center space-x-2">
                    <span>⚠️</span>
                    <span>{{ session('locale', 'id') == 'en' ? 'Operational Exceptions' : 'Kendala Operasional (Exceptions)' }}</span>
                </h3>

                @if(isset($shipment->exceptions) && $shipment->exceptions->count() > 0)
                    <div class="space-y-4">
                        @foreach($shipment->exceptions as $exc)
                            @php
                                $excTitle = $exc->exception_type ?? $exc->title ?? $exc->reason ?? (session('locale', 'id') == 'en' ? 'Operational Issue' : 'Kendala Pengiriman');
                                $excDesc = $exc->description ?? $exc->notes ?? $exc->details ?? '';
                                $isResolved = !empty($exc->resolved) || !empty($exc->resolved_at) || (($exc->status ?? '') === 'resolved');
                            @endphp
                            <div class="bg-amber-50/80 border border-amber-200 rounded-xl p-4 space-y-1.5">
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    <h4 class="font-extrabold text-amber-950 text-sm">
                                        {{ $excTitle }}
                                        @if(!empty($exc->severity))
                                            <span class="ml-1 text-[10px] font-mono bg-white border border-amber-300 text-amber-900 px-2 py-0.5 rounded">{{ $exc->severity }}</span>
                                        @endif
                                    </h4>
                                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full {{ $isResolved ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-200 text-amber-950' }}">
                                        {{ $isResolved
                                            ? (session('locale', 'id') == 'en' ? 'Resolved' : 'Selesai Ditangani')
                                            : (session('locale', 'id') == 'en' ? 'Active' : 'Dalam Penanganan') }}
                                    </span>
                                </div>
                                @if(!empty($excDesc))
                                    <p class="text-sm text-slate-700">{{ $excDesc }}</p>
                                @endif
                                @if(!empty($exc->created_at))
                                    <p class="text-xs font-mono text-slate-500">
                                        {{ is_object($exc->created_at) ? $exc->created_at->format('d M Y H:i') : $exc->created_at }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-6 text-center text-sm text-emerald-900 font-medium">
                        {{ session('locale', 'id') == 'en' ? 'No operational exceptions reported. Shipment is running smoothly.' : 'Tidak ada kendala operasional yang dilaporkan. Pengiriman berjalan lancar.' }}
                    </div>
                @endif
            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const originLat = {{ (float) ($shipment->originLocation->latitude ?? -6.1045) }};
            const originLng = {{ (float) ($shipment->originLocation->longitude ?? 106.8803) }};
            const destLat = {{ (float) ($shipment->destinationLocation->latitude ?? 1.29027) }};
            const destLng = {{ (float) ($shipment->destinationLocation->longitude ?? 103.851959) }};

            const originName = @json($shipment->originLocation->name ?? 'Origin Port');
            const destName = @json($shipment->destinationLocation->name ?? 'Destination Port');

            const map = L.map('route-map', {
                scrollWheelZoom: false
            }).setView([(originLat + destLat) / 2, (originLng + destLng) / 2], 4);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const originMarker = L.circleMarker([originLat, originLng], {
                radius: 9,
                fillColor: '#059669',
                color: '#ffffff',
                weight: 2.5,
                opacity: 1,
                fillOpacity: 0.95
            }).addTo(map).bindPopup('<strong>Asal:</strong> ' + originName);

            const destMarker = L.circleMarker([destLat, destLng], {
                radius: 9,
                fillColor: '#e11d48',
                color: '#ffffff',
                weight: 2.5,
                opacity: 1,
                fillOpacity: 0.95
            }).addTo(map).bindPopup('<strong>Tujuan:</strong> ' + destName);

            const routeLine = L.polyline([
                [originLat, originLng],
                [destLat, destLng]
            ], {
                color: '#1e3a8a',
                weight: 3.5,
                dashArray: '8, 8'
            }).addTo(map);

            map.fitBounds(routeLine.getBounds(), { padding: [50, 50] });
        });
    </script>
</body>
</html>