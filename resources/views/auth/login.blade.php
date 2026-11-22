<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('locale') == 'en' ? 'Login - CargoTrace' : 'Login - CargoTrace' }}</title>
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

    <!-- Container Form Login -->
    <div class="w-full max-w-md p-6 relative z-10">
        <div class="light-glass rounded-3xl p-8 shadow-2xl border border-blue-200 relative">
            
            <!-- Tombol Pilihan Bahasa (ID / EN) di dalam Card -->
            <div class="absolute top-6 right-6">
                <div class="flex space-x-2 text-xs bg-white px-3 py-1.5 rounded-full border border-blue-200 shadow-sm font-bold">
                    <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
                    <span class="text-blue-200">|</span>
                    <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'en', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
                </div>
            </div>

            <!-- Header Login -->
            <div class="text-center mb-8 pt-2">
                <div class="inline-block bg-blue-950 text-white p-3 rounded-2xl shadow-md text-xl mb-3">🚢</div>
                <h1 class="text-3xl font-black text-blue-950 tracking-wide">CargoTrace</h1>
                <p class="text-slate-600 text-sm mt-1 font-medium">
                    {{ session('locale') == 'en' ? 'Please login to continue' : 'Silakan login untuk melanjutkan' }}
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm font-medium">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form diarahkan langsung ke URL /login (Metode POST yang sah) -->
            <form action="{{ url('/login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@cargotrace.com" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-2">
                        {{ session('locale') == 'en' ? 'Password' : 'Kata Sandi (Password)' }}
                    </label>
                    <input type="password" name="password" placeholder="••••••••" required class="w-full bg-white/95 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-700 shadow-inner font-medium">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-blue-950 hover:bg-blue-900 text-white py-3.5 rounded-xl text-sm font-bold transition shadow-lg tracking-wide">
                        {{ session('locale') == 'en' ? 'Sign In (Login)' : 'Masuk (Login)' }}
                    </button>
                </div>
            </form>

        </div>
    </div>

</body>
</html>