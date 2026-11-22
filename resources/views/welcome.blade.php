<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale') == 'en' ? 'CargoTrace - Global Shipment Tracking' : 'CargoTrace - Platform Pelacakan & Logistik Global' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .light-glass {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(191, 219, 254, 0.6);
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen relative bg-cover bg-center flex items-center justify-center" style="background-image: linear-gradient(to bottom, rgba(241, 245, 249, 0.88), rgba(226, 232, 240, 0.96)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1920&auto=format&fit=crop');">

    <!-- Tombol Pilihan Bahasa (ID / EN) di Pojok Kanan Atas -->
    <div class="absolute top-6 right-6 z-50">
        <div class="flex space-x-2 text-xs bg-white/90 px-3.5 py-1.5 rounded-full border border-blue-200 shadow-md font-bold">
            <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
            <span class="text-blue-200">|</span>
            <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'en', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
        </div>
    </div>

    <!-- Tombol Login Cepat di Pojok Kiri Atas -->
    <div class="absolute top-6 left-6 z-50">
        <a href="{{ route('login') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-md flex items-center space-x-2">
            <span>🔐</span>
            <span>{{ session('locale') == 'en' ? 'Sign In' : 'Masuk Sistem' }}</span>
        </a>
    </div>

    <!-- Container Utama Pelacakan -->
    <div class="w-full max-w-xl p-6 relative z-10">
        <div class="light-glass rounded-3xl p-8 md:p-10 shadow-2xl border border-blue-200 text-center">
            
            <div class="inline-block bg-blue-950 text-white p-3 rounded-2xl shadow-md text-2xl mb-3">🚢</div>
            <h1 class="text-3xl md:text-4xl font-black text-blue-950 tracking-wide">CargoTrace</h1>
            <p class="text-slate-600 text-sm md:text-base mt-1 font-medium mb-8">
                {{ session('locale') == 'en' ? 'Global Shipment Visibility & Tracking Platform' : 'Platform Pelacakan & Transparansi Logistik Maritim Global' }}
            </p>

            <!-- Form Cari Resi -->
            <form action="{{ url('/track/search') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <input type="text" name="tracking_number" placeholder="{{ session('locale') == 'en' ? 'ENTER TRACKING NUMBER (e.g., CT-2026-9988)' : 'MASUKKAN NOMOR RESI (CONTOH: CT-2026-9988)' }}" required class="w-full bg-white/95 border border-blue-200 rounded-2xl px-5 py-4 text-sm md:text-base focus:outline-none focus:border-blue-700 shadow-inner font-mono font-bold text-center text-blue-950 placeholder-slate-400">
                </div>

                <button type="submit" class="w-full bg-blue-950 hover:bg-blue-900 text-white py-4 rounded-2xl text-sm md:text-base font-bold transition shadow-lg tracking-wider uppercase">
                    {{ session('locale') == 'en' ? 'Track Shipment' : 'Lacak Pengiriman' }}
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-blue-200/60">
                <a href="{{ route('login') }}" class="text-blue-900 hover:text-blue-700 text-xs md:text-sm font-bold underline transition">
                    {{ session('locale') == 'en' ? 'Sign In to System Portal' : 'Masuk ke Dashboard Sistem' }}
                </a>
            </div>

        </div>
    </div>

</body>
</html>