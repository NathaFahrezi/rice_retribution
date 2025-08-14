@extends('layout')

@section('title', 'Dashboard Super Admin')

@section('content')

    {{-- <div class="bg-white rounded-xl shadow-md p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500">
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-400 focus:ring-offset-1 transition">
                    Filter
                </button>
            </div>

            <div class="flex items-end">
                <a href="{{ route('user.masyarakat') }}"
                    class="w-full px-4 py-2 bg-white border border-green-600 text-green-600 rounded-lg shadow hover:bg-green-50 focus:ring-2 focus:ring-green-400 focus:ring-offset-1 transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div> --}}
    <div class="flex-1 overflow-x-hidden overflow-y-auto p-6 md:p-8 scrollbar-hide">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Stok Tersedia</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-1">1,250 Kg</h2>
            </div>
            <div class="bg-yellow-100 text-yellow-600 rounded-full p-3">
                <i class="fas fa-warehouse text-2xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Distribusi</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-1">1,250 Kg</h2>
            </div>
            <div class="bg-blue-100 text-blue-600 rounded-full p-3">
                <i class="fas fa-truck text-2xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Sisa Stok</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-1">500 Kg</h2>
            </div>
            <div class="bg-green-100 text-green-600 rounded-full p-3">
                <i class="fas fa-box text-2xl"></i>
            </div>
        </div>

            {{-- <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pesanan Baru</p>
                        <h2 class="text-3xl font-bold text-gray-800 mt-1">45</h2>
                    </div>
                    <div class="bg-green-100 text-green-600 rounded-full p-3">
                        <i class="fas fa-truck text-2xl"></i>
                    </div>
                </div>
            </div> --}}
            <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Masyarakat</p>
                    <h2 class="text-3xl font-bold text-gray-800 mt-1">189</h2>
                </div>
                <div class="bg-purple-100 text-purple-600 rounded-full p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Grafik Distribusi & Stok</h3>
                <select id="chartFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500">
                    <option value="daily">Per Hari</option>
                    <option value="weekly">Per Minggu</option>
                    <option value="monthly">Per Bulan</option>
                </select>
            </div>
            <canvas id="distributionChart" height="120"></canvas>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('distributionChart').getContext('2d');

            const chartDataSets = {
                daily: {
                    labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                    distribusi: [120, 100, 140, 160, 110, 90, 150],
                    stok: [500, 480, 460, 440, 420, 400, 380],
                    sisa: [300, 320, 340, 360, 380, 400, 420]
                },
                weekly: {
                    labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                    distribusi: [500, 450, 480, 530],
                    stok: [2000, 1950, 1900, 1850],
                    sisa: [1500, 1550, 1600, 1650]
                },
                monthly: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu'],
                    distribusi: [2000, 1800, 2200, 2100, 1900, 2300, 2500, 2400],
                    stok: [5000, 4800, 4600, 4400, 4200, 4000, 3800, 3600],
                    sisa: [3000, 3200, 3400, 3600, 3800, 4000, 4200, 4400]
                }
            };

            let distributionChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartDataSets.daily.labels,
                    datasets: [
                        {
                            label: 'Distribusi (Kg)',
                            data: chartDataSets.daily.distribusi,
                            backgroundColor: 'rgba(132, 204, 22, 0.7)',
                            borderRadius: 6
                        },
                        {
                            label: 'Stok Tersedia (Kg)',
                            data: chartDataSets.daily.stok,
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderRadius: 6
                        },
                        {
                            label: 'Sisa Stok (Kg)',
                            data: chartDataSets.daily.sisa,
                            backgroundColor: 'rgba(239, 68, 68, 0.7)',
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            document.getElementById('chartFilter').addEventListener('change', function() {
                const value = this.value;
                distributionChart.data.labels = chartDataSets[value].labels;
                distributionChart.data.datasets[0].data = chartDataSets[value].distribusi;
                distributionChart.data.datasets[1].data = chartDataSets[value].stok;
                distributionChart.data.datasets[2].data = chartDataSets[value].sisa;
                distributionChart.update();
            });
        </script>
    </div>

    @if (session('alert'))
        <script>
            Swal.fire({
                icon: '{{ session('alert.type') }}',
                title: '{{ session('alert.title') }}',
                text: '{{ session('alert.text') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif
@endsection