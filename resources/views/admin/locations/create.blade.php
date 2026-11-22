<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale') == 'en' ? 'Add Location - CargoTrace Admin' : 'Tambah Lokasi - CargoTrace Admin' }}</title>
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
                <div class="bg-blue-950 text-white p-2 rounded-xl shadow">📍</div>
                <h1 class="text-xl font-extrabold tracking-wide text-blue-950">
                    CargoTrace <span class="text-blue-700 font-medium">— {{ session('locale') == 'en' ? 'Add Location' : 'Tambah Lokasi' }}</span>
                </h1>
            </div>
            <a href="{{ route('admin.locations.index') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2 rounded-xl text-sm font-semibold transition shadow-md">
                {{ session('locale') == 'en' ? 'Back' : 'Kembali' }}
            </a>
        </div>
    </nav>

    <!-- Content Container -->
    <div class="container mx-auto max-w-2xl p-6 relative z-10">
        <div class="light-glass rounded-2xl p-8 shadow-xl border border-blue-200">
            <h2 class="text-2xl font-black text-blue-950 mb-6">
                {{ session('locale') == 'en' ? 'New Location Form' : 'Formulir Tambah Lokasi Baru' }}
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

            <form action="{{ route('admin.locations.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                        {{ session('locale') == 'en' ? 'Location / Warehouse / Port Name' : 'Nama Lokasi / Gudang / Pelabuhan' }}
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ session('locale') == 'en' ? 'e.g., Tanjung Priok Cargo Hub' : 'Contoh: Tanjung Priok Cargo Hub' }}" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                        {{ session('locale') == 'en' ? 'City' : 'Kota' }}
                    </label>
                    <input type="text" name="city" value="{{ old('city') }}" placeholder="{{ session('locale') == 'en' ? 'e.g., Jakarta' : 'Contoh: Jakarta' }}" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                        {{ session('locale') == 'en' ? 'Country' : 'Negara' }}
                    </label>
                    <input type="text" name="country" value="{{ old('country') }}" placeholder="{{ session('locale') == 'en' ? 'e.g., Indonesia' : 'Contoh: Indonesia' }}" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <a href="{{ route('admin.locations.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-6 py-3 rounded-xl text-sm font-bold transition shadow-sm">
                        {{ session('locale') == 'en' ? 'Cancel' : 'Batal' }}
                    </a>
                    <button type="submit" class="bg-blue-950 hover:bg-blue-900 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-md">
                        {{ session('locale') == 'en' ? 'Save Location' : 'Simpan Lokasi' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>