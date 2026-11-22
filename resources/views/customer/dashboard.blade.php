<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale') == 'en' ? 'Customer Dashboard - CargoTrace' : 'Dashboard Customer - CargoTrace' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .light-glass {
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(191, 219, 254, 0.6);
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen relative bg-cover bg-center" style="background-image: linear-gradient(to bottom, rgba(241, 245, 249, 0.88), rgba(226, 232, 240, 0.96)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1920&auto=format&fit=crop');">

    <!-- Navbar dengan pilihan bahasa -->
    <nav class="light-glass border-b border-blue-200 sticky top-0 z-50 px-6 py-4 shadow-sm">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-900 text-white p-2 rounded-xl shadow">🚢</div>
                <h1 class="text-xl font-extrabold tracking-wide text-blue-950">
                    CargoTrace <span class="text-blue-700 font-medium">— {{ session('locale') == 'en' ? 'Customer Portal' : 'Portal Pelanggan' }}</span>
                </h1>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Tombol Ganti Bahasa (ID / EN) -->
                <div class="flex space-x-2 text-sm bg-white/90 px-3.5 py-1.5 rounded-full border border-blue-200 shadow-inner font-semibold">
                    <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
                    <span class="text-blue-200">|</span>
                    <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
                </div>

                <!-- Logout Form -->
                <form action="/logout" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2 rounded-full text-sm font-semibold transition shadow-md">
                        {{ session('locale') == 'en' ? 'Logout' : 'Keluar' }}
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Content Container -->
    <div class="container mx-auto p-6 space-y-8 relative z-10">

        @if(session('error'))
            <div class="bg-red-50 border border-red-300 text-red-800 px-5 py-4 rounded-2xl text-sm font-semibold shadow-sm">
                {{ session('error') }}
            </div>
        @endif
        
        <!-- Welcome Banner -->
        <div class="light-glass rounded-2xl p-8 shadow-lg border-l-8 border-blue-900 flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h2 class="text-3xl font-black text-blue-950 mb-2">
                    {{ session('locale') == 'en' ? 'Welcome, ' . (auth()->user()->name ?? 'Customer') . '!' : 'Selamat Datang, ' . (auth()->user()->name ?? 'Pelanggan') . '!' }}
                </h2>
                <p class="text-slate-600 text-sm md:text-base font-medium">
                    {{ session('locale') == 'en' ? 'Track your active cargo shipments and monitor global maritime routes seamlessly.' : 'Pantau pengiriman kargo aktif Anda dan lacak rute pelayaran maritim dengan mudah.' }}
                </p>
            </div>
            <div class="mt-4 md:mt-0 bg-blue-950 text-blue-100 border border-blue-800 px-4 py-2 rounded-xl text-xs font-mono font-bold tracking-wider shadow">
                ACCOUNT TYPE: CUSTOMER
            </div>
        </div>

        <!-- Quick Track Box (Form Cek Resi Cepat) -->
        <div class="light-glass rounded-2xl p-6 shadow-md border border-blue-200">
            <h3 class="text-lg font-extrabold text-blue-950 mb-4 flex items-center space-x-2">
                <span>🔍</span>
                <span>{{ session('locale') == 'en' ? 'Quick Shipment Tracking' : 'Lacak Resi Pengiriman Cepat' }}</span>
            </h3>
            <form action="{{ url('/track/search') }}" method="POST" class="flex flex-col md:flex-row gap-4">
                @csrf
                <input type="text" name="shipment_number" placeholder="{{ session('locale') == 'en' ? 'Enter tracking number (e.g., CRG-2026-001)' : 'Masukkan nomor resi (contoh: CRG-2026-001)' }}" required class="flex-1 bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm uppercase font-mono focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                <button type="submit" class="bg-blue-950 hover:bg-blue-900 text-white px-8 py-3 rounded-xl text-sm font-bold transition shadow-md">
                    {{ session('locale') == 'en' ? 'Track Cargo' : 'Lacak Kargo' }}
                </button>
            </form>
        </div>

        @php
            $activeStatus = request('status');
        @endphp

        <!-- Statistik Ringkasan Pengiriman Customer (Bisa Diklik untuk Filter Tabel) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Kartu 1: Total Kiriman -->
            <a href="{{ url('/dashboard#riwayat-pengiriman') }}" class="light-glass p-6 rounded-2xl shadow-md flex items-center justify-between border transition cursor-pointer group {{ empty($activeStatus) ? 'border-blue-900 ring-2 ring-blue-900/20' : 'border-blue-200 hover:border-blue-500 hover:scale-[1.02]' }}">
                <div>
                    <p class="text-xs uppercase tracking-wider text-blue-900 font-bold group-hover:text-blue-700 transition">{{ session('locale') == 'en' ? 'Total Shipments' : 'Total Pengiriman' }}</p>
                    <h3 class="text-4xl font-black text-blue-950 mt-2">{{ $totalShipments ?? 0 }}</h3>
                </div>
                <div class="bg-blue-100 border border-blue-300 p-4 rounded-2xl text-blue-900 text-2xl shadow-inner group-hover:bg-blue-200 transition">📦</div>
            </a>

            <!-- Kartu 2: Dalam Perjalanan (In Transit) -->
            <a href="{{ url('/dashboard?status=In+Transit#riwayat-pengiriman') }}" class="light-glass p-6 rounded-2xl shadow-md flex items-center justify-between border transition cursor-pointer group {{ $activeStatus === 'In Transit' ? 'border-blue-900 ring-2 ring-blue-900/20' : 'border-blue-200 hover:border-blue-500 hover:scale-[1.02]' }}">
                <div>
                    <p class="text-xs uppercase tracking-wider text-blue-900 font-bold group-hover:text-blue-700 transition">{{ session('locale') == 'en' ? 'In Transit' : 'Dalam Perjalanan' }}</p>
                    <h3 class="text-4xl font-black text-blue-950 mt-2">{{ $inTransitCount ?? 0 }}</h3>
                </div>
                <div class="bg-blue-100 border border-blue-300 p-4 rounded-2xl text-blue-900 text-2xl shadow-inner group-hover:bg-blue-200 transition">🚢</div>
            </a>

            <!-- Kartu 3: Selesai (Delivered) -->
            <a href="{{ url('/dashboard?status=Delivered#riwayat-pengiriman') }}" class="light-glass p-6 rounded-2xl shadow-md flex items-center justify-between border transition cursor-pointer group {{ $activeStatus === 'Delivered' ? 'border-blue-900 ring-2 ring-blue-900/20' : 'border-blue-200 hover:border-blue-500 hover:scale-[1.02]' }}">
                <div>
                    <p class="text-xs uppercase tracking-wider text-blue-900 font-bold group-hover:text-blue-700 transition">{{ session('locale') == 'en' ? 'Delivered' : 'Selesai / Tiba' }}</p>
                    <h3 class="text-4xl font-black text-blue-950 mt-2">{{ $deliveredCount ?? 0 }}</h3>
                </div>
                <div class="bg-blue-100 border border-blue-300 p-4 rounded-2xl text-blue-900 text-2xl shadow-inner group-hover:bg-blue-200 transition">✅</div>
            </a>
        </div>

        <!-- Tabel Daftar Kiriman Milik Customer -->
        <div id="riwayat-pengiriman" class="light-glass rounded-2xl p-6 shadow-md border border-blue-200 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <h3 class="text-lg font-extrabold text-blue-950 flex items-center space-x-2">
                    <span>📋</span>
                    <span>
                        {{ session('locale') == 'en' ? 'My Cargo Shipments History' : 'Riwayat Pengiriman Kargo Saya' }}
                        @if(!empty($activeStatus))
                            <span class="text-xs font-bold bg-blue-100 text-blue-900 px-2.5 py-1 rounded-full ml-2">Filter: {{ $activeStatus }}</span>
                        @endif
                    </span>
                </h3>
                @if(!empty($activeStatus))
                    <a href="{{ url('/dashboard#riwayat-pengiriman') }}" class="text-xs font-bold text-blue-900 hover:underline">
                        {{ session('locale') == 'en' ? 'Show All' : 'Tampilkan Semua' }}
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-blue-950 text-blue-100 uppercase text-xs border-b border-blue-900">
                            <th class="p-4 rounded-l-xl">TRACKING NUMBER</th>
                            <th class="p-4">{{ session('locale') == 'en' ? 'ROUTE (ORIGIN → DESTINATION)' : 'RUTE (ASAL → TUJUAN)' }}</th>
                            <th class="p-4">CARRIER</th>
                            <th class="p-4">STATUS</th>
                            <th class="p-4">ETA</th>
                            <th class="p-4 rounded-r-xl text-right">{{ session('locale') == 'en' ? 'ACTION' : 'AKSI' }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700 divide-y divide-blue-100">
                        @forelse($myShipments ?? [] as $shipment)
                            <tr class="hover:bg-blue-50/60 transition">
                                <td class="p-4 font-mono text-xs text-blue-900 font-bold">
                                    <a href="{{ url('/track/' . $shipment->shipment_number) }}" class="underline hover:text-blue-700">
                                        {{ $shipment->shipment_number }}
                                    </a>
                                </td>
                                <td class="p-4 font-semibold text-slate-900">
                                    {{ $shipment->originLocation->name ?? '-' }} → {{ $shipment->destinationLocation->name ?? '-' }}
                                </td>
                                <td class="p-4 text-slate-600 font-medium">{{ $shipment->carrier->name ?? '-' }}</td>
                                <td class="p-4">
                                    <span class="bg-blue-100 text-blue-900 border border-blue-300 px-3 py-1 rounded-md text-xs font-mono font-bold shadow-sm">
                                        {{ $shipment->status ?? 'Created' }}
                                    </span>
                                </td>
                                <td class="p-4 text-xs text-slate-500 font-medium">{{ $shipment->expected_arrival_date ?? '-' }}</td>
                                <td class="p-4 text-right">
                                    <a href="{{ url('/track/' . $shipment->shipment_number) }}" class="inline-block bg-blue-950 hover:bg-blue-900 text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                                        {{ session('locale') == 'en' ? 'Track & Map' : 'Lacak & Peta' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 italic">
                                    {{ session('locale') == 'en' ? 'No shipment history found under your account.' : 'Belum ada riwayat pengiriman kargo yang terdaftar di akun Anda.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>