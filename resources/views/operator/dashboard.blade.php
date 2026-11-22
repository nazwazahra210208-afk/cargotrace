<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale') == 'en' ? 'Operator Dashboard - CargoTrace' : 'Dashboard Operator - CargoTrace' }}</title>
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

    <!-- Navbar Operator -->
    <nav class="light-glass border-b border-blue-200 sticky top-0 z-50 px-6 py-4 shadow-sm">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-900 text-white p-2 rounded-xl shadow">🚢</div>
                <h1 class="text-xl font-extrabold tracking-wide text-blue-950">
                    CargoTrace <span class="text-blue-700 font-medium">— {{ session('locale') == 'en' ? 'Operator Portal' : 'Portal Operator' }}</span>
                </h1>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex space-x-2 text-sm bg-white/90 px-3.5 py-1.5 rounded-full border border-blue-200 shadow-inner font-semibold">
                    <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
                    <span class="text-blue-200">|</span>
                    <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'en', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
                </div>

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
        
        <!-- Welcome Banner -->
        <div class="light-glass rounded-2xl p-8 shadow-lg border-l-8 border-blue-900 flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h2 class="text-3xl font-black text-blue-950 mb-2">
                    {{ session('locale') == 'en' ? 'Welcome, Operator ' . (auth()->user()->name ?? '') . '!' : 'Selamat Datang, Operator ' . (auth()->user()->name ?? '') . '!' }}
                </h2>
                <p class="text-slate-600 text-sm md:text-base font-medium">
                    {{ session('locale') == 'en' ? 'Manage cargo shipments, track logistics routes, and handle operational exceptions.' : 'Kelola pengiriman kargo, pantau rute logistik, dan tangani kendala operasional.' }}
                </p>
            </div>
            <div class="mt-4 md:mt-0 bg-blue-950 text-blue-100 border border-blue-800 px-4 py-2 rounded-xl text-xs font-mono font-bold tracking-wider shadow">
                ACCOUNT TYPE: OPERATOR
            </div>
        </div>

        <!-- Statistik Ringkasan (Bisa Diklik untuk Memfilter) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Kartu 1: Total Kiriman -->
            <a href="{{ url('/shipments') }}" class="light-glass p-6 rounded-2xl shadow-md flex items-center justify-between border border-blue-200 hover:border-blue-500 hover:scale-[1.02] transition cursor-pointer group">
                <div>
                    <p class="text-xs uppercase tracking-wider text-blue-900 font-bold group-hover:text-blue-700 transition">
                        {{ session('locale') == 'en' ? 'Total Shipments' : 'Total Pengiriman' }}
                    </p>
                    <h3 class="text-4xl font-black text-blue-950 mt-2">{{ $totalShipments ?? 0 }}</h3>
                </div>
                <div class="bg-blue-100 border border-blue-300 p-4 rounded-2xl text-blue-900 text-2xl shadow-inner group-hover:bg-blue-200 transition">📦</div>
            </a>

            <!-- Kartu 2: Dalam Perjalanan (In Transit) -->
            <a href="{{ url('/shipments?status=In+Transit') }}" class="light-glass p-6 rounded-2xl shadow-md flex items-center justify-between border border-blue-200 hover:border-blue-500 hover:scale-[1.02] transition cursor-pointer group">
                <div>
                    <p class="text-xs uppercase tracking-wider text-blue-900 font-bold group-hover:text-blue-700 transition">
                        {{ session('locale') == 'en' ? 'In Transit' : 'Dalam Perjalanan' }}
                    </p>
                    <h3 class="text-4xl font-black text-blue-950 mt-2">{{ $inTransitCount ?? 0 }}</h3>
                </div>
                <div class="bg-blue-100 border border-blue-300 p-4 rounded-2xl text-blue-900 text-2xl shadow-inner group-hover:bg-blue-200 transition">🚢</div>
            </a>

            <!-- Kartu 3: Selesai (Delivered) -->
            <a href="{{ url('/shipments?status=Delivered') }}" class="light-glass p-6 rounded-2xl shadow-md flex items-center justify-between border border-blue-200 hover:border-blue-500 hover:scale-[1.02] transition cursor-pointer group">
                <div>
                    <p class="text-xs uppercase tracking-wider text-blue-900 font-bold group-hover:text-blue-700 transition">
                        {{ session('locale') == 'en' ? 'Delivered' : 'Selesai / Tiba' }}
                    </p>
                    <h3 class="text-4xl font-black text-blue-950 mt-2">{{ $deliveredCount ?? 0 }}</h3>
                </div>
                <div class="bg-blue-100 border border-blue-300 p-4 rounded-2xl text-blue-900 text-2xl shadow-inner group-hover:bg-blue-200 transition">✅</div>
            </a>

        </div>

        <!-- Kartu Informasi Tugas Logistics Operator -->
        <div class="light-glass rounded-3xl p-8 shadow-xl border border-blue-200 border-l-8 border-l-cyan-600 max-w-xl">
            <h3 class="text-2xl font-black text-blue-950 mb-6">
                {{ session('locale') == 'en' ? 'Logistics Operator Duties' : 'Logistics Operator' }}
            </h3>
            <ul class="space-y-3 text-slate-700 font-medium text-base">
                <li class="flex items-center space-x-3">
                    <span class="w-2 h-2 bg-blue-950 rounded-full"></span>
                    <span>{{ session('locale') == 'en' ? 'Create and update shipments.' : 'Buat dan perbarui shipment.' }}</span>
                </li>
                <li class="flex items-center space-x-3">
                    <span class="w-2 h-2 bg-blue-950 rounded-full"></span>
                    <span>{{ session('locale') == 'en' ? 'Add tracking events.' : 'Tambah tracking event.' }}</span>
                </li>
                <li class="flex items-center space-x-3">
                    <span class="w-2 h-2 bg-blue-950 rounded-full"></span>
                    <span>{{ session('locale') == 'en' ? 'Manage exceptions.' : 'Kelola exception.' }}</span>
                </li>
                <li class="flex items-center space-x-3">
                    <span class="w-2 h-2 bg-blue-950 rounded-full"></span>
                    <span>{{ session('locale') == 'en' ? 'View operational shipments.' : 'Lihat shipment operasional.' }}</span>
                </li>
            </ul>
        </div>

        <!-- Menu Pintasan Aksi Operator -->
        <div class="light-glass rounded-2xl p-6 shadow-md border border-blue-200 flex justify-between items-center">
            <h3 class="text-lg font-extrabold text-blue-950 flex items-center space-x-2">
                <span>⚡</span>
                <span>{{ session('locale') == 'en' ? 'Operational Shortcuts' : 'Akses Cepat Operasional' }}</span>
            </h3>
            <div class="space-x-3">
                <a href="{{ url('/shipments') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md transition">
                    {{ session('locale') == 'en' ? 'Manage Shipments' : 'Kelola Pengiriman' }}
                </a>
                <a href="{{ url('/shipments/create') }}" class="bg-blue-800 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md transition">
                    {{ session('locale') == 'en' ? '+ Create Shipment' : '+ Tambah Pengiriman' }}
                </a>
            </div>
        </div>

    </div>
</body>
</html>