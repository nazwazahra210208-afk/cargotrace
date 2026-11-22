<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale') == 'en' ? 'Shipment List - CargoTrace' : 'Daftar Pengiriman - CargoTrace' }}</title>
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
                    CargoTrace <span class="text-blue-700 font-medium">— {{ session('locale') == 'en' ? 'Shipments' : 'Pengiriman' }}</span>
                </h1>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex space-x-2 text-sm bg-white/90 px-3.5 py-1.5 rounded-full border border-blue-200 shadow-inner font-semibold">
                    <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
                    <span class="text-blue-200">|</span>
                    <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'en', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
                </div>

                <a href="{{ url('/dashboard') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2 rounded-xl text-sm font-semibold transition shadow-md">
                    {{ session('locale') == 'en' ? 'Dashboard' : 'Dashboard' }}
                </a>
            </div>
        </div>
    </nav>

    <!-- Content Container -->
    <div class="container mx-auto p-6 space-y-6 relative z-10">
        <div class="light-glass rounded-3xl p-8 shadow-xl border border-blue-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-blue-950">
                    {{ session('locale') == 'en' ? 'CargoTrace - Shipment List' : 'CargoTrace - Daftar Pengiriman' }}
                </h2>
                <p class="text-slate-600 text-sm font-medium mt-1">
                    {{ session('locale') == 'en' ? 'Manage and monitor all active maritime logistics shipments.' : 'Kelola dan pantau seluruh pengiriman logistik maritim aktif.' }}
                </p>
            </div>
            <a href="{{ url('/shipments/create') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg transition">
                {{ session('locale') == 'en' ? '+ Add Shipment' : '+ Tambah Shipment' }}
            </a>
        </div>

        <!-- Tabel / Konten -->
        <div class="light-glass rounded-3xl p-6 shadow-xl border border-blue-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-blue-950 text-blue-100 uppercase text-xs border-b border-blue-900">
                            <th class="p-4 rounded-l-xl">TRACKING NUMBER</th>
                            <th class="p-4">{{ session('locale') == 'en' ? 'CUSTOMER' : 'PELANGGAN' }}</th>
                            <th class="p-4">{{ session('locale') == 'en' ? 'ROUTE' : 'RUTE' }}</th>
                            <th class="p-4">CARRIER</th>
                            <th class="p-4">STATUS</th>
                            <th class="p-4 rounded-r-xl">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700 divide-y divide-blue-100">
                        @forelse($shipments ?? [] as $shipment)
                            <tr class="hover:bg-blue-50/60 transition">
                                <td class="p-4 font-mono font-bold text-blue-900">{{ $shipment->tracking_number }}</td>
                                <td class="p-4 font-semibold text-slate-900">{{ $shipment->user->name ?? '-' }}</td>
                                <td class="p-4">{{ $shipment->origin ?? '-' }} → {{ $shipment->destination ?? '-' }}</td>
                                <td class="p-4 text-slate-600">{{ $shipment->carrier->name ?? '-' }}</td>
                                <td class="p-4">
                                    <span class="bg-blue-100 text-blue-900 border border-blue-300 px-3 py-1 rounded-md text-xs font-mono font-bold">
                                        {{ $shipment->status ?? 'Pending' }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <a href="{{ url('/track/' . $shipment->tracking_number) }}" class="text-blue-700 hover:underline font-bold text-xs">
                                        {{ session('locale') == 'en' ? 'View' : 'Detail' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-slate-400 italic font-medium">
                                    {{ session('locale') == 'en' ? 'No shipment data found in the database.' : 'Belum ada data pengiriman di dalam database.' }}
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