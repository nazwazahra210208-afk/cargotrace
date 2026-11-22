<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale') == 'en' ? 'Admin Dashboard - CargoTrace' : 'Dashboard Admin - CargoTrace' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Efek kaca terang yang mewah dan bersih */
        .light-glass {
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(191, 219, 254, 0.6);
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen relative bg-cover bg-center" style="background-image: linear-gradient(to bottom, rgba(241, 245, 249, 0.88), rgba(226, 232, 240, 0.96)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1920&auto=format&fit=crop');">

    <!-- Navbar dengan aksen biru elegan -->
    <nav class="light-glass border-b border-blue-200 sticky top-0 z-50 px-6 py-4 shadow-sm">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-900 text-white p-2 rounded-xl shadow">⚡</div>
                <h1 class="text-xl font-extrabold tracking-wide text-blue-950">CargoTrace <span class="text-blue-700 font-medium">— Admin Portal</span></h1>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Pilihan Bahasa (i18n) -->
                <div class="flex space-x-2 text-sm bg-white/90 px-3.5 py-1.5 rounded-full border border-blue-200 shadow-inner font-semibold">
                    <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
                    <span class="text-blue-200">|</span>
                    <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'en' , 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
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
        
        <!-- Welcome Banner dengan Border Biru Elegan -->
        <div class="light-glass rounded-2xl p-8 shadow-lg border-l-8 border-blue-900 flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h2 class="text-3xl font-black text-blue-950 mb-2">
                    {{ session('locale') == 'en' ? 'Welcome, Administrator!' : 'Selamat Datang, Administrator!' }}
                </h2>
                <p class="text-slate-600 text-sm md:text-base font-medium">
                    {{ session('locale') == 'en' ? 'Global Logistics, Seamlessly Monitored & Controlled.' : 'Kelola dan Pantau Operasional Logistik Global dengan Presisi Tinggi.' }}
                </p>
            </div>
            <div class="mt-4 md:mt-0 bg-blue-950 text-blue-100 border border-blue-800 px-4 py-2 rounded-xl text-xs font-mono font-bold tracking-wider shadow">
                SYSTEM STATUS: SECURE & ACTIVE
            </div>
        </div>

        <!-- Statistik Pengguna (Cards Interaktif Sesuai Tempatnya) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Kartu 1: Total Users -->
            <a href="{{ route('admin.users.index') }}" class="light-glass p-6 rounded-2xl shadow-md flex items-center justify-between hover:border-blue-700 hover:shadow-xl transition duration-300 group">
                <div>
                    <p class="text-xs uppercase tracking-wider text-blue-900 font-bold group-hover:text-blue-700 transition">{{ session('locale') == 'en' ? 'Total Users' : 'Total Pengguna' }}</p>
                    <h3 class="text-4xl font-black text-blue-950 mt-2">{{ $totalUsers ?? 0 }}</h3>
                </div>
                <div class="bg-blue-100 border border-blue-300 p-4 rounded-2xl text-blue-900 text-2xl group-hover:scale-110 transition shadow-inner">👥</div>
            </a>

            <!-- Kartu 2: Logistics Operators (Mengarah ke User dengan Filter Operator) -->
            <a href="{{ route('admin.users.index', ['role' => 'operator']) }}" class="light-glass p-6 rounded-2xl shadow-md flex items-center justify-between hover:border-blue-700 hover:shadow-xl transition duration-300 group">
                <div>
                    <p class="text-xs uppercase tracking-wider text-blue-900 font-bold group-hover:text-blue-700 transition">{{ session('locale') == 'en' ? 'Logistics Operators' : 'Operator Logistik' }}</p>
                    <h3 class="text-4xl font-black text-blue-950 mt-2">{{ $totalOperators ?? 0 }}</h3>
                </div>
                <div class="bg-blue-100 border border-blue-300 p-4 rounded-2xl text-blue-900 text-2xl group-hover:scale-110 transition shadow-inner">🛠️</div>
            </a>

            <!-- Kartu 3: Active Customers (Mengarah ke User dengan Filter Customer) -->
            <a href="{{ route('admin.users.index', ['role' => 'customer']) }}" class="light-glass p-6 rounded-2xl shadow-md flex items-center justify-between hover:border-blue-700 hover:shadow-xl transition duration-300 group">
                <div>
                    <p class="text-xs uppercase tracking-wider text-blue-900 font-bold group-hover:text-blue-700 transition">{{ session('locale') == 'en' ? 'Active Customers' : 'Customer Aktif' }}</p>
                    <h3 class="text-4xl font-black text-blue-950 mt-2">{{ $totalCustomers ?? 0 }}</h3>
                </div>
                <div class="bg-blue-100 border border-blue-300 p-4 rounded-2xl text-blue-900 text-2xl group-hover:scale-110 transition shadow-inner">📦</div>
            </a>
        </div>

        <!-- Tombol Pintasan (Quick Configuration Shortcuts) dengan Nuansa Biru Elegan -->
        <div class="light-glass rounded-2xl p-6 shadow-md border border-blue-200">
            <h3 class="text-lg font-extrabold text-blue-950 mb-4 flex items-center space-x-2">
                <span>⚙️</span>
                <span>{{ session('locale') == 'en' ? 'Quick Configuration Shortcuts' : 'Pintasan Konfigurasi Sistem' }}</span>
            </h3>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.carriers.index') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-3 rounded-xl shadow-md transition text-sm font-bold flex items-center space-x-3 border border-blue-900">
                    <span class="text-lg">🚢</span>
                    <span>{{ session('locale') == 'en' ? 'Manage Carriers (' . ($totalCarriers ?? 0) . ')' : 'Kelola Carrier (' . ($totalCarriers ?? 0) . ')' }}</span>
                </a>
                <a href="{{ route('admin.locations.index') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-3 rounded-xl shadow-md transition text-sm font-bold flex items-center space-x-3 border border-blue-900">
                    <span class="text-lg">📍</span>
                    <span>{{ session('locale') == 'en' ? 'Manage Locations (' . ($totalLocations ?? 0) . ')' : 'Kelola Lokasi (' . ($totalLocations ?? 0) . ')' }}</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-3 rounded-xl shadow-md transition text-sm font-bold flex items-center space-x-3 border border-blue-900">
                    <span class="text-lg">👥</span>
                    <span>{{ session('locale') == 'en' ? 'Manage Users (' . ($totalUsers ?? 0) . ')' : 'Kelola User (' . ($totalUsers ?? 0) . ')' }}</span>
                </a>
                <a href="{{ route('admin.shipments.index') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-3 rounded-xl shadow-md transition text-sm font-bold flex items-center space-x-3 border border-blue-900">
                    <span class="text-lg">📦</span>
                    <span>{{ session('locale') == 'en' ? 'Manage Shipments' : 'Data Pengiriman' }}</span>
                </a>
            </div>
        </div>

        <!-- Tabel Ringkasan Audit Log -->
        <div class="light-glass rounded-2xl p-6 shadow-md border border-blue-200 overflow-hidden">
            <h3 class="text-lg font-extrabold text-blue-950 mb-4 flex items-center space-x-2">
                <span>📋</span>
                <span>{{ session('locale') == 'en' ? 'System Audit Log Summary' : 'Ringkasan Jejak Audit Sistem' }}</span>
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-blue-950 text-blue-100 uppercase text-xs border-b border-blue-900">
                            <th class="p-4 rounded-l-xl">ID</th>
                            <th class="p-4">{{ session('locale') == 'en' ? 'Actor / User' : 'Aktor / Pengguna' }}</th>
                            <th class="p-4">{{ session('locale') == 'en' ? 'Action Performed' : 'Aksi Dilakukan' }}</th>
                            <th class="p-4 rounded-r-xl">{{ session('locale') == 'en' ? 'Timestamp' : 'Waktu' }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700 divide-y divide-blue-100">
                        @forelse($auditLogs ?? [] as $log)
                            <tr class="hover:bg-blue-50/60 transition">
                                <td class="p-4 font-mono text-xs text-blue-900 font-bold">#{{ $log->id }}</td>
                                <td class="p-4 font-semibold text-slate-900">{{ $log->user->name ?? 'System' }}</td>
                                <td class="p-4"><span class="bg-blue-100 text-blue-900 border border-blue-300 px-3 py-1 rounded-md text-xs font-mono font-semibold shadow-sm">{{ $log->action }}</span></td>
                                <td class="p-4 text-xs text-slate-500 font-medium">{{ $log->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400 italic">
                                    {{ session('locale') == 'en' ? 'No recent audit logs recorded.' : 'Belum ada catatan audit log yang terekam.' }}
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