<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale') == 'en' ? 'Create Shipment - CargoTrace' : 'Tambah Pengiriman - CargoTrace' }}</title>
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
                    CargoTrace <span class="text-blue-700 font-medium">— {{ session('locale') == 'en' ? 'Create Shipment' : 'Tambah Shipment' }}</span>
                </h1>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex space-x-2 text-sm bg-white/90 px-3.5 py-1.5 rounded-full border border-blue-200 shadow-inner font-semibold">
                    <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
                    <span class="text-blue-200">|</span>
                    <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'en', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
                </div>

                <a href="{{ url('/shipments') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2 rounded-xl text-sm font-semibold transition shadow-md">
                    {{ session('locale') == 'en' ? 'Back' : 'Kembali' }}
                </a>
            </div>
        </div>
    </nav>

    <!-- Content Container -->
    <div class="container mx-auto max-w-3xl p-6 relative z-10">
        <div class="light-glass rounded-3xl p-8 shadow-2xl border border-blue-200">
            <h2 class="text-2xl font-black text-blue-950 mb-6">
                {{ session('locale') == 'en' ? 'New Shipment Form' : 'Formulir Tambah Pengiriman Baru' }}
            </h2>

            <form action="{{ url('/shipments') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                        {{ session('locale') == 'en' ? 'Tracking Number' : 'Nomor Resi' }}
                    </label>
                    <input type="text" name="tracking_number" placeholder="{{ session('locale') == 'en' ? 'E.G., CRG-112233' : 'CONTOH: CRG-112233' }}" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">Customer</label>
                        <select name="user_id" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium text-slate-800">
                            <option value="">{{ session('locale') == 'en' ? '-- Select Customer --' : '-- Pilih Customer --' }}</option>
                            @foreach(\App\Models\User::where('role', 'customer')->get() as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Carrier' : 'Carrier (Kurir)' }}
                        </label>
                        <select name="carrier_id" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium text-slate-800">
                            <option value="">{{ session('locale') == 'en' ? '-- Select Carrier --' : '-- Pilih Carrier --' }}</option>
                            @foreach(\App\Models\Carrier::all() as $car)
                                <option value="{{ $car->id }}">{{ $car->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Origin Location' : 'Lokasi Asal' }}
                        </label>
                        <input type="text" name="origin" placeholder="{{ session('locale') == 'en' ? 'E.g., Tanjung Priok Port' : 'Contoh: Pelabuhan Tanjung Priok' }}" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                            {{ session('locale') == 'en' ? 'Destination Location' : 'Lokasi Tujuan' }}
                        </label>
                        <input type="text" name="destination" placeholder="{{ session('locale') == 'en' ? 'E.g., Singapore Port' : 'Contoh: Pelabuhan Singapura' }}" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                        {{ session('locale') == 'en' ? 'Cargo Type' : 'Jenis Barang (Cargo Type)' }}
                    </label>
                    <input type="text" name="cargo_type" placeholder="{{ session('locale') == 'en' ? 'E.g., Machinery Spare Parts' : 'Contoh: Suku Cadang Mesin' }}" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <a href="{{ url('/shipments') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-6 py-3 rounded-xl text-sm font-bold transition shadow-sm">
                        {{ session('locale') == 'en' ? 'Cancel' : 'Batal' }}
                    </a>
                    <button type="submit" class="bg-blue-950 hover:bg-blue-900 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-md">
                        {{ session('locale') == 'en' ? 'Save Shipment' : 'Simpan Pengiriman' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>