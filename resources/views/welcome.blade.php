<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale', 'id') == 'en' ? 'CargoTrace - Tracking Platform' : 'CargoTrace - Platform Pelacakan' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .light-glass {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(191, 219, 254, 0.6);
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen flex flex-col justify-between relative bg-cover bg-center" style="background-image: linear-gradient(to bottom, rgba(241, 245, 249, 0.85), rgba(226, 232, 240, 0.95)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1920&auto=format&fit=crop');">

    <!-- Top Bar -->
    <div class="container mx-auto px-6 py-5 flex justify-between items-center relative z-10">
        @auth
            @php
                $dashUrl = auth()->user()->role === 'admin' ? route('admin.dashboard') : url('/dashboard');
            @endphp
            <a href="{{ $dashUrl }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2.5 rounded-full text-xs font-bold shadow-md transition flex items-center space-x-2">
                <span>📊</span>
                <span>Dashboard</span>
            </a>
        @else
            <a href="{{ url('/login') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2.5 rounded-full text-xs font-bold shadow-md transition flex items-center space-x-2">
                <span>🔐</span>
                <span>{{ session('locale', 'id') == 'en' ? 'System Login' : 'Masuk Sistem' }}</span>
            </a>
        @endauth

        <div class="flex space-x-2 text-xs bg-white/95 px-4 py-2 rounded-full border border-blue-200 shadow-sm font-bold">
            <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
            <span class="text-blue-200">|</span>
            <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
        </div>
    </div>

    <!-- Center Tracking Card -->
    <div class="container mx-auto px-4 flex items-center justify-center flex-1 relative z-10">
        <div class="light-glass rounded-3xl p-8 md:p-12 shadow-2xl border border-blue-200 w-full max-w-xl text-center space-y-6">
            <div class="mx-auto w-14 h-14 bg-blue-950 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg">
                🚢
            </div>

            <div>
                <h1 class="text-4xl font-black tracking-tight text-blue-950">CargoTrace</h1>
                <p class="text-slate-600 text-sm font-medium mt-2">
                    {{ session('locale', 'id') == 'en' ? 'Global Maritime Logistics Tracking & Transparency Platform' : 'Platform Pelacakan & Transparansi Logistik Maritim Global' }}
                </p>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl text-xs font-bold">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl text-xs font-bold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('/track/search') }}" method="POST" class="space-y-4">
                @csrf
                <input
                    type="text"
                    name="shipment_number"
                    value="{{ old('shipment_number') }}"
                    required
                    placeholder="{{ session('locale', 'id') == 'en' ? 'ENTER TRACKING NUMBER (E.G. CRG-2026-001)' : 'MASUKKAN NOMOR RESI (CONTOH: CRG-2026-001)' }}"
                    class="w-full text-center font-mono font-bold text-sm uppercase tracking-wider rounded-2xl border-2 border-blue-200 bg-blue-50/60 focus:bg-white px-5 py-4 text-blue-950 focus:outline-none focus:border-blue-900 transition"
                >

                <button type="submit" class="w-full bg-blue-950 hover:bg-blue-900 text-white font-extrabold tracking-widest uppercase text-xs py-4 rounded-2xl shadow-lg transition">
                    {{ session('locale', 'id') == 'en' ? 'TRACK SHIPMENT' : 'LACAK PENGIRIMAN' }}
                </button>
            </form>

            <div class="pt-2">
                <a href="{{ url('/login') }}" class="text-xs font-bold text-blue-900 hover:underline">
                    {{ session('locale', 'id') == 'en' ? 'Go to System Dashboard' : 'Masuk ke Dashboard Sistem' }}
                </a>
            </div>
        </div>
    </div>

    <div class="h-8"></div>
</body>
</html>