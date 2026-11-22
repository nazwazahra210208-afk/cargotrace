<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale') == 'en' ? 'Add Shipment - CargoTrace Admin' : 'Tambah Pengiriman - CargoTrace Admin' }}</title>
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
                <div class="bg-blue-950 text-white p-2 rounded-xl shadow">📦</div>
                <h1 class="text-xl font-extrabold tracking-wide text-blue-950">
                    CargoTrace <span class="text-blue-700 font-medium">— {{ session('locale') == 'en' ? 'Add Shipment (Admin)' : 'Tambah Pengiriman (Admin)' }}</span>
                </h1>
            </div>
            <a href="{{ route('admin.shipments.index') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2 rounded-xl text-sm font-semibold transition shadow-md">
                {{ session('locale') == 'en' ? 'Back' : 'Kembali' }}
            </a>
        </div>
    </nav>

    <!-- Content Container -->
    <div class="container mx-auto max-w-3xl p-6 relative z-10">
        <div class="light-glass rounded-2xl p-8 shadow-xl border border-blue-200">
            <h2 class="text-2xl font-black text-blue-950 mb-6">
                {{ session('locale') == 'en' ? 'New Shipment Form' : 'Form Tambah Shipment Baru' }}
            </h2>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm font-medium">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.shipments.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                        {{ session('locale') == 'en' ? 'Tracking Number' : 'Nomor Resi (Tracking Number)' }}
                    </label>
                    <input type="text" name="tracking_number" value="{{ old('tracking_number') }}" placeholder="Contoh: CT-2026-9988" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Customer / User' : 'Customer / Pengguna' }}
                        </label>
                        <select name="user_id" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium text-slate-800">
                            <option value="">{{ session('locale') == 'en' ? 'Select Customer' : 'Pilih Customer' }}</option>
                            @foreach($customers ?? [] as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Carrier (Shipping Line)' : 'Carrier (Armada/Pelayaran)' }}
                        </label>
                        <select name="carrier_id" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium text-slate-800">
                            <option value="">{{ session('locale') == 'en' ? 'Select Carrier' : 'Pilih Carrier' }}</option>
                            @foreach($carriers ?? [] as $carrier)
                                <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Origin Location' : 'Lokasi Asal (Origin)' }}
                        </label>
                        <select name="origin_id" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium text-slate-800">
                            <option value="">{{ session('locale') == 'en' ? 'Select Origin' : 'Pilih Asal' }}</option>
                            @foreach($locations ?? [] as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->city }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Destination Location' : 'Lokasi Tujuan (Destination)' }}
                        </label>
                        <select name="destination_id" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium text-slate-800">
                            <option value="">{{ session('locale') == 'en' ? 'Select Destination' : 'Pilih Tujuan' }}</option>
                            @foreach($locations ?? [] as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->city }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Cargo Type' : 'Tipe Kargo' }}
                        </label>
                        <input type="text" name="cargo_type" placeholder="Contoh: Elektronik / Dokumen" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Weight (KG)' : 'Berat (KG)' }}
                        </label>
                        <input type="number" step="0.1" name="weight" placeholder="10.5" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Volume (M³)' : 'Volume (M³)' }}
                        </label>
                        <input type="number" step="0.1" name="volume" placeholder="1.2" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Departure Date' : 'Estimasi Berangkat' }}
                        </label>
                        <input type="date" name="departure_date" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Estimated Time of Arrival (ETA)' : 'Estimasi Tiba (ETA)' }}
                        </label>
                        <input type="date" name="eta" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <a href="{{ route('admin.shipments.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-6 py-3 rounded-xl text-sm font-bold transition shadow-sm">
                        {{ session('locale') == 'en' ? 'Cancel' : 'Batal' }}
                    </a>
                    <button type="submit" class="bg-blue-950 hover:bg-blue-900 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-md">
                        {{ session('locale') == 'en' ? 'Save Shipment' : 'Simpan Shipment' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>