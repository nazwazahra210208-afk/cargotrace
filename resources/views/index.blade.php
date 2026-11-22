<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Operasional - CargoTrace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="container mx-auto p-6 max-w-5xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-600">Dashboard Operasional CargoTrace</h1>
            <a href="/shipments" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">Kelola Shipment</a>
        </div>

        <!-- Kartu Statistik Utama -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                <p class="text-gray-500 text-xs uppercase font-bold">Total Shipment</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalShipments }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-yellow-500">
                <p class="text-gray-500 text-xs uppercase font-bold">In Transit</p>
                <p class="text-2xl font-bold text-gray-800">{{ $inTransit }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-green-500">
                <p class="text-gray-500 text-xs uppercase font-bold">Delivered</p>
                <p class="text-2xl font-bold text-gray-800">{{ $delivered }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-red-500">
                <p class="text-gray-500 text-xs uppercase font-bold">Open Exceptions</p>
                <p class="text-2xl font-bold text-red-600">{{ $openExceptions }}</p>
            </div>
        </div>

        <!-- Grafik Statistik (Chart.js) -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Grafik Distribusi Status Pengiriman</h2>
            <div class="w-full h-72 flex justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'],
                datasets: [{
                    label: 'Jumlah Shipment',
                    data: @json($statusCounts),
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(40, 167, 69, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(40, 167, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>
</body>
</html>