<nav class="bg-white shadow-md mb-6">
    <div class="container mx-auto px-6 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-6">
            <span class="text-xl font-bold text-blue-600">CargoTrace</span>
            <div class="space-x-4 text-sm font-semibold">
                <a href="/dashboard" class="text-gray-700 hover:text-blue-600 transition">Dashboard</a>
                <a href="/shipments" class="text-gray-700 hover:text-blue-600 transition">Daftar Shipment</a>
                <a href="/shipments/create" class="text-gray-700 hover:text-blue-600 transition">+ Tambah Shipment</a>
            </div>
        </div>
        <div>
            <form action="/logout" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-3 py-1.5 bg-red-500 text-white rounded text-xs font-semibold hover:bg-red-600 transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>