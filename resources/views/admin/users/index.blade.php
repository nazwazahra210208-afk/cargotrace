<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kelola User' }} - CargoTrace Admin</title>
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
                <div class="bg-blue-950 text-white p-2 rounded-xl shadow">👥</div>
                <h1 class="text-xl font-extrabold tracking-wide text-blue-950">
                    CargoTrace <span class="text-blue-700 font-medium">— {{ session('locale') == 'en' ? 'User Management' : 'Kelola User' }}</span>
                </h1>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Pilihan Bahasa (i18n) -->
                <div class="flex space-x-2 text-sm bg-white/90 px-3.5 py-1.5 rounded-full border border-blue-200 shadow-inner font-semibold">
                    <a href="{{ route('admin.lang', 'id') }}" class="{{ session('locale', 'id') == 'id' ? 'text-blue-900 underline' : 'text-slate-400' }}">ID</a>
                    <span class="text-blue-200">|</span>
                    <a href="{{ route('admin.lang', 'en') }}" class="{{ session('locale', 'en', 'id') == 'en' ? 'text-blue-900 underline' : 'text-slate-400' }}">EN</a>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2 rounded-xl text-sm font-semibold transition shadow-md">
                    {{ session('locale') == 'en' ? 'Back to Dashboard' : 'Kembali ke Dashboard' }}
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

        <!-- Header Card Dinamis -->
        <div class="light-glass rounded-2xl p-6 shadow-md border border-blue-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <!-- Judul berubah otomatis sesuai controller ($title) -->
                <h2 class="text-2xl font-black text-blue-950">{{ $title ?? 'Daftar Pengguna Sistem' }}</h2>
                <p class="text-slate-600 text-sm mt-1 font-medium">
                    {{ session('locale') == 'en' ? 'Manage Administrator, Logistics Operator, and Customer accounts specifically.' : 'Kelola akun Administrator, Operator Logistik, dan Customer secara spesifik.' }}
                </p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-950 hover:bg-blue-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow transition flex items-center space-x-2">
                <span>{{ session('locale') == 'en' ? '+ Add New User' : '+ Tambah User Baru' }}</span>
            </a>
        </div>

        <!-- Tabel Data User -->
        <div class="light-glass rounded-2xl shadow-md border border-blue-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-950 text-blue-100 uppercase text-xs border-b border-blue-900">
                        <th class="p-4 rounded-l-xl">ID</th>
                        <th class="p-4">{{ session('locale') == 'en' ? 'Name' : 'Nama' }}</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">{{ session('locale') == 'en' ? 'Role / Permission' : 'Role / Peran' }}</th>
                        <th class="p-4 text-center rounded-r-xl">{{ session('locale') == 'en' ? 'Actions' : 'Aksi' }}</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-700 divide-y divide-blue-100">
                    @forelse($users ?? [] as $user)
                        <tr class="hover:bg-blue-50/60 transition">
                            <td class="p-4 font-mono text-xs text-blue-900 font-bold">#{{ $user->id }}</td>
                            <td class="p-4 font-bold text-slate-900">{{ $user->name }}</td>
                            <td class="p-4 text-slate-600 font-medium">{{ $user->email }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-md text-xs font-mono font-bold uppercase {{ $user->role == 'admin' ? 'bg-blue-900 text-white shadow-sm' : 'bg-blue-100 text-blue-900 border border-blue-300' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if(auth()->id() == $user->id)
                                    <span class="text-xs text-slate-400 italic font-medium">({{ session('locale') == 'en' ? 'Your Account' : 'Akun Anda' }})</span>
                                @else
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ session('locale') == 'en' ? 'Are you sure you want to delete this user?' : 'Yakin ingin menghapus user ini?' }}');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg text-xs hover:bg-red-100 font-semibold shadow-sm transition">
                                            {{ session('locale') == 'en' ? 'Delete' : 'Hapus' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 italic font-medium">
                                {{ session('locale') == 'en' ? 'No user data found for this category in the system.' : 'Belum ada data pengguna untuk kategori ini dalam sistem.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>