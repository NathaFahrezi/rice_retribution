@extends('layout')

@section('title', 'Data Penjualan')

@section('content')
    <div class="flex-1 overflow-x-hidden overflow-y-auto p-6 md:p-8 scrollbar-hide">

        <div class="bg-white rounded-xl shadow-md p-6">
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
                    <a href="{{ route('superadmin.masyarakat') }}"
                        class="w-full px-4 py-2 bg-white border border-green-600 text-green-600 rounded-lg shadow hover:bg-green-50 focus:ring-2 focus:ring-green-400 focus:ring-offset-1 transition text-center">
                        Reset
                    </a>
                </div>
            </form>

            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-800">Data Penjualan</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                No
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lampiran
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Beras
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal dan jam
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($masyarakat as $i => $m)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $masyarakat->firstItem() + $i }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($m->foto_ktp)
                                        <a href="{{ asset('uploads/ktp/' . $m->foto_ktp) }}" data-fancybox="gallery"
                                            data-caption="Foto KTP">
                                            <img src="{{ asset('uploads/ktp/' . $m->foto_ktp) }}" alt="Foto KTP"
                                                class="h-15 w-auto rounded border">
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $m->jumlah_beras }} Kg
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($m->created_at)->translatedFormat('d F Y H:i') }}
                                </td>
                                <td>
                                    <a href="{{ route('superadmin.masyarakat.detail', $m->id) }}"
                                        class="px-4 py-2 bg-white border border-yellow-600 text-yellow-600 hover:bg-yellow-50 font-semibold rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-1 transition">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Data tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $masyarakat->links() }}
                </div>

            </div>
        </div>
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
