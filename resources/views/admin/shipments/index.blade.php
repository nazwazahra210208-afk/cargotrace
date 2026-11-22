<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale', 'id') == 'en' ? 'Shipment Data - CargoTrace Admin' : 'Data Pengiriman - CargoTrace Admin' }}</title>
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
                <div class="bg-blue-950 text-white p-2 rounded-xl shadow">📦</div>
                <h1 class="text-xl font-extrabold tracking-wide text-blue-950">
                    CargoTrace <span class="text-blue-700 font-medium">— {{ session('locale', 'id') == 'en' ? 'Shipment Data' : 'Data Pengiriman' }}</span>
                </h1>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Tombol Ganti Bahasa (ID / EN) -->
                <div class="flex space-x-2 text-sm bg-white/90 px-3.5 py-1.5 rounded-full border border-blue-200 shadow-inner font-semibold">
                    <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
                    <span class="text-blue-200">|</span>
                    <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2 rounded-xl text-sm font-semibold transition shadow-md">
                    {{ session('locale', 'id') == 'en' ? 'Back to Dashboard' : 'Kembali ke Dashboard' }}
                </a>
            </div>
        </div>
    </nav>

    <!-- Content Container -->
    <div class="container mx-auto p-6 space-y-6 relative z-10">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 p-4 rounded-xl shadow-sm font-medium">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Header Card -->
        <div class="light-glass rounded-2xl p-6 shadow-md border border-blue-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-blue-950">{{ session('locale', 'id') == 'en' ? 'All Shipment Data' : 'Semua Data Pengiriman' }}</h2>
                <p class="text-slate-600 text-sm mt-1 font-medium">
                    {{ session('locale', 'id') == 'en' ? 'Monitor tracking numbers, routes, current status, and ETA.' : 'Pantau nomor resi, rute, status terkini, dan estimasi waktu (ETA) pengiriman.' }}
                </p>
            </div>
            <a href="{{ route('admin.shipments.create') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow transition flex items-center space-x-2">
                <span>{{ session('locale', 'id') == 'en' ? '+ Add Shipment' : '+ Tambah Shipment' }}</span>
            </a>
        </div>

        <!-- Tabel Data Shipment -->
        <div class="light-glass rounded-2xl shadow-md border border-blue-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-950 text-blue-100 uppercase text-xs border-b border-blue-900">
                        <th class="p-4 rounded-l-xl">TRACKING NUMBER</th>
                        <th class="p-4">{{ session('locale', 'id') == 'en' ? 'ROUTE (ORIGIN → DESTINATION)' : 'RUTE (ASAL → TUJUAN)' }}</th>
                        <th class="p-4">STATUS</th>
                        <th class="p-4">ETA</th>
                        <th class="p-4 text-center rounded-r-xl">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-700 divide-y divide-blue-100">
                    @forelse($shipments ?? [] as $shipment)
                        @php
                            $resi = $shipment->shipment_number ?? $shipment->tracking_number ?? '-';
                            $asal = $shipment->originLocation->name ?? $shipment->origin ?? '-';
                            $tujuan = $shipment->destinationLocation->name ?? $shipment->destination ?? '-';
                            $eta = $shipment->expected_arrival_date ?? $shipment->eta ?? '-';
                        @endphp
                        <tr class="hover:bg-blue-50/60 transition">
                            <td class="p-4 font-mono text-xs text-blue-900 font-bold">
                                <div>{{ $resi }}</div>
                                <div class="text-[11px] font-sans font-normal text-slate-500 mt-0.5">
                                    {{ $shipment->user->name ?? '-' }} • {{ $shipment->carrier->name ?? '-' }}
                                </div>
                            </td>
                            <td class="p-4 font-semibold text-slate-900">{{ $asal }} → {{ $tujuan }}</td>
                            <td class="p-4">
                                <span class="bg-blue-100 text-blue-900 border border-blue-300 px-3 py-1 rounded-md text-xs font-mono font-bold shadow-sm">
                                    {{ $shipment->status ?? 'Pending' }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-600 text-xs font-medium">{{ $eta }}</td>
                            <td class="p-4 text-center">
                                <div class="inline-flex items-center space-x-2">
                                    @if($resi !== '-')
                                        <a href="{{ url('/track/' . $resi) }}" class="bg-blue-50 text-blue-800 border border-blue-200 px-3 py-1.5 rounded-lg text-xs hover:bg-blue-100 font-semibold shadow-sm transition">
                                            Detail
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.shipments.edit', $shipment->id) }}" class="bg-amber-50 text-amber-800 border border-amber-200 px-3 py-1.5 rounded-lg text-xs hover:bg-amber-100 font-semibold shadow-sm transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.shipments.destroy', $shipment->id) }}" method="POST" onsubmit="return confirm('{{ session('locale', 'id') == 'en' ? 'Are you sure you want to delete this shipment?' : 'Yakin ingin menghapus pengiriman ini?' }}');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg text-xs hover:bg-red-100 font-semibold shadow-sm transition">
                                            {{ session('locale', 'id') == 'en' ? 'Delete' : 'Hapus' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 italic font-medium">
                                {{ session('locale', 'id') == 'en' ? 'No shipment data recorded in the system.' : 'Belum ada data pengiriman yang terekam dalam sistem.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>