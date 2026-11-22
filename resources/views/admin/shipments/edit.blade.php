<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale', 'id') == 'en' ? 'Edit Shipment - CargoTrace Admin' : 'Edit Pengiriman - CargoTrace Admin' }}</title>
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
                <div class="bg-blue-950 text-white p-2 rounded-xl shadow">✏️</div>
                <h1 class="text-xl font-extrabold tracking-wide text-blue-950">
                    CargoTrace <span class="text-blue-700 font-medium">— {{ session('locale', 'id') == 'en' ? 'Edit Shipment' : 'Edit Pengiriman' }}</span>
                </h1>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex space-x-2 text-sm bg-white/90 px-3.5 py-1.5 rounded-full border border-blue-200 shadow-inner font-semibold">
                    <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
                    <span class="text-blue-200">|</span>
                    <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
                </div>

                <a href="{{ route('admin.shipments.index') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2 rounded-xl text-sm font-semibold transition shadow-md">
                    {{ session('locale', 'id') == 'en' ? 'Back to List' : 'Kembali ke Daftar' }}
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container mx-auto p-6 max-w-3xl space-y-6 relative z-10">
        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-800 p-4 rounded-xl text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="light-glass rounded-3xl p-8 shadow-xl border border-blue-200 space-y-6">
            <div class="border-b border-blue-100 pb-4">
                <span class="text-xs font-mono font-bold bg-blue-950 text-blue-100 px-3 py-1 rounded-lg">
                    {{ $shipment->shipment_number }}
                </span>
                <h2 class="text-2xl font-black text-blue-950 mt-2">
                    {{ session('locale', 'id') == 'en' ? 'Update Shipment Details' : 'Perbarui Data Pengiriman' }}
                </h2>
                <p class="text-slate-600 text-sm mt-1">
                    {{ $shipment->originLocation->name ?? '-' }} → {{ $shipment->destinationLocation->name ?? '-' }}
                </p>
            </div>

            <form action="{{ route('admin.shipments.update', $shipment->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-bold text-blue-950 mb-1.5">
                        {{ session('locale', 'id') == 'en' ? 'Cargo Type' : 'Jenis Muatan (Cargo Type)' }}
                    </label>
                    <input type="text" name="cargo_type" value="{{ old('cargo_type', $shipment->cargo_type) }}" required
                        class="w-full rounded-xl border border-blue-200 bg-white/90 px-4 py-3 text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>

                <div>
                    <label class="block text-sm font-bold text-blue-950 mb-1.5">
                        {{ session('locale', 'id') == 'en' ? 'Cargo Weight (kg)' : 'Berat Muatan (kg)' }}
                    </label>
                    <input type="number" step="0.01" min="0.1" name="weight" value="{{ old('weight', $shipment->weight) }}" required
                        class="w-full rounded-xl border border-blue-200 bg-white/90 px-4 py-3 text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>

                <div>
                    <label class="block text-sm font-bold text-blue-950 mb-1.5">
                        Status
                    </label>
                    <select name="status" required
                        class="w-full rounded-xl border border-blue-200 bg-white/90 px-4 py-3 text-sm font-bold text-blue-950 focus:outline-none focus:ring-2 focus:ring-blue-900">
                        <option value="Created" {{ old('status', $shipment->status) == 'Created' ? 'selected' : '' }}>Created</option>
                        <option value="In Transit" {{ old('status', $shipment->status) == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="Delivered" {{ old('status', $shipment->status) == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-blue-100">
                    <a href="{{ route('admin.shipments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-100 transition">
                        {{ session('locale', 'id') == 'en' ? 'Cancel' : 'Batal' }}
                    </a>
                    <button type="submit" class="bg-blue-950 hover:bg-blue-900 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg transition">
                        {{ session('locale', 'id') == 'en' ? 'Save Changes' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>