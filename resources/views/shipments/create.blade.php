<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale', 'id') == 'en' ? 'Add Shipment - CargoTrace' : 'Tambah Pengiriman - CargoTrace' }}</title>
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

    <!-- Navbar -->
    <nav class="light-glass border-b border-blue-200 sticky top-0 z-50 px-6 py-4 shadow-sm">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-950 text-white p-2 rounded-xl shadow">🚢</div>
                <h1 class="text-xl font-extrabold tracking-wide text-blue-950">
                    CargoTrace <span class="text-blue-700 font-medium">— {{ session('locale', 'id') == 'en' ? 'Add Shipment' : 'Tambah Shipment' }}</span>
                </h1>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex space-x-2 text-sm bg-white/90 px-3.5 py-1.5 rounded-full border border-blue-200 shadow-inner font-semibold">
                    <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
                    <span class="text-blue-200">|</span>
                    <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
                </div>

                <a href="{{ url('/shipments') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2 rounded-xl text-sm font-semibold transition shadow-md">
                    {{ session('locale', 'id') == 'en' ? 'Back' : 'Kembali' }}
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container mx-auto p-6 max-w-3xl space-y-6 relative z-10">
        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-800 p-4 rounded-2xl text-sm space-y-1 shadow-sm">
                @foreach($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="light-glass rounded-3xl p-8 md:p-10 shadow-xl border border-blue-200">
            <h2 class="text-2xl font-black text-blue-950 mb-6">
                {{ session('locale', 'id') == 'en' ? 'New Shipment Form' : 'Formulir Tambah Pengiriman Baru' }}
            </h2>

            <form action="{{ url('/shipments') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-blue-950 mb-2">
                        {{ session('locale', 'id') == 'en' ? 'TRACKING / SHIPMENT NUMBER' : 'NOMOR RESI' }}
                    </label>
                    <input type="text" name="shipment_number" value="{{ old('shipment_number') }}" required placeholder="CONTOH: CRG-2026-003"
                        class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-blue-950 mb-2">CUSTOMER</label>
                        <select name="user_id" required class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('user_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-blue-950 mb-2">CARRIER (KURIR)</label>
                        <select name="carrier_id" required class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                            <option value="">-- Pilih Carrier --</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}" {{ old('carrier_id') == $carrier->id ? 'selected' : '' }}>
                                    {{ $carrier->name }} ({{ $carrier->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-blue-950 mb-2">
                            {{ session('locale', 'id') == 'en' ? 'ORIGIN PORT' : 'PELABUHAN ASAL' }}
                        </label>
                        <select name="origin_location_id" required class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                            <option value="">-- Pilih Pelabuhan Asal --</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('origin_location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }} ({{ $location->city }}, {{ $location->country }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-blue-950 mb-2">
                            {{ session('locale', 'id') == 'en' ? 'DESTINATION PORT' : 'PELABUHAN TUJUAN' }}
                        </label>
                        <select name="destination_location_id" required class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                            <option value="">-- Pilih Pelabuhan Tujuan --</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('destination_location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }} ({{ $location->city }}, {{ $location->country }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-blue-950 mb-2">
                        {{ session('locale', 'id') == 'en' ? 'CARGO TYPE' : 'JENIS BARANG (CARGO TYPE)' }}
                    </label>
                    <input type="text" name="cargo_type" value="{{ old('cargo_type') }}" required placeholder="Contoh: Suku Cadang Mesin"
                        class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-blue-950 mb-2">
                            {{ session('locale', 'id') == 'en' ? 'WEIGHT (KG)' : 'BERAT KARGO (KG)' }}
                        </label>
                        <input type="number" step="0.01" min="0.1" name="weight" value="{{ old('weight') }}" required placeholder="Contoh: 1250"
                            class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-blue-950 mb-2">
                            {{ session('locale', 'id') == 'en' ? 'VOLUME (M³)' : 'VOLUME KARGO (M³)' }}
                        </label>
                        <input type="number" step="0.01" min="0.01" name="volume" value="{{ old('volume') }}" required placeholder="Contoh: 15.5"
                            class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-blue-950 mb-2">
                            {{ session('locale', 'id') == 'en' ? 'EXPECTED DEPARTURE' : 'ESTIMASI KEBERANGKATAN' }}
                        </label>
                        <input type="date" name="expected_departure_date" value="{{ old('expected_departure_date', date('Y-m-d')) }}" required
                            class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-blue-950 mb-2">
                            {{ session('locale', 'id') == 'en' ? 'EXPECTED ARRIVAL (ETA)' : 'ESTIMASI TIBA (ETA)' }}
                        </label>
                        <input type="date" name="expected_arrival_date" value="{{ old('expected_arrival_date', date('Y-m-d', strtotime('+5 days'))) }}" required
                            class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                    </div>
                </div>

                <div class="flex justify-end items-center gap-3 pt-4">
                    <a href="{{ url('/shipments') }}" class="px-6 py-3 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-100 transition">
                        {{ session('locale', 'id') == 'en' ? 'Cancel' : 'Batal' }}
                    </a>
                    <button type="submit" class="bg-blue-950 hover:bg-blue-900 text-white px-7 py-3 rounded-xl text-sm font-bold shadow-lg transition">
                        {{ session('locale', 'id') == 'en' ? 'Save Shipment' : 'Simpan Pengiriman' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>