@extends('layout')

@section('title', 'Data Distribusi')

@section('content')
<div class="flex-1 overflow-x-hidden overflow-y-auto p-6 md:p-8 scrollbar-hide">

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-100 rounded-xl shadow-md p-6 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-medium text-gray-600">Total Distribusi</h4>
                <p class="mt-2 text-2xl font-bold text-gray-800">
                    {{ $totalDistribusi ?? 0 }} Kg
                </p>
            </div>
            <div class="text-green-600 text-4xl">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">

        {{-- Dropdown pilih filter --}}
        <div class="mb-4 relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kolom & Filter</label>
            <button type="button" id="dropdownButton" class="w-full text-left px-3 py-2 border rounded-lg focus:outline-none flex justify-between items-center bg-white">
                Pilih Kolom
                <span id="dropdownIcon">▼</span>
            </button>
            <div id="dropdownMenu" class="absolute z-10 mt-1 w-full bg-white border rounded-lg shadow-lg hidden">
                @php
                    $allFilters = ['tanggal','nama','pangkat','jabatan','polres','polsek','jumlah'];
                    $selectedFilters = request('filters', []);
                @endphp
                @foreach($allFilters as $f)
                    <div class="px-3 py-2 hover:bg-gray-100 cursor-pointer flex justify-between items-center filter-item" data-value="{{ $f }}">
                        <span>{{ ucfirst(str_replace('_',' ',$f)) }}</span>
                        <span class="filter-symbol">{{ in_array($f,$selectedFilters) ? '✔' : '✖' }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Form filter dinamis --}}
        <form method="GET" id="filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 {{ count($selectedFilters) ? '' : 'hidden' }}">
            <input type="hidden" name="filters[]" id="filters-hidden" value="{{ implode(',', $selectedFilters) }}">
            <div id="dynamic-input"></div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
                    Search
                </button>
            </div>

            <div class="flex items-end">
                <button type="button" id="resetButton" class="w-full px-4 py-2 bg-white border border-green-600 text-green-600 rounded-lg shadow hover:bg-green-50 transition text-center">
                    Reset
                </button>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="data-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 col-no">No</th>
                        <th class="px-6 py-3 col-tanggal">Tanggal</th>
                        <th class="px-6 py-3 col-nama">Nama</th>
                        <th class="px-6 py-3 col-pangkat">Pangkat</th>
                        <th class="px-6 py-3 col-jabatan">Jabatan</th>
                        <th class="px-6 py-3 col-polres">Polres</th>
                        <th class="px-6 py-3 col-polsek">Polsek</th>
                        <th class="px-6 py-3 col-jumlah_beras">Jumlah Beras (Kg)</th>
                        <th class="px-6 py-3 col-foto">Foto</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                    @forelse ($penjualan as $i => $p)
                        <tr>
                            <td class="px-6 py-4 col-no">{{ $penjualan->firstItem() + $i ?? $i+1 }}</td>
                            <td class="px-6 py-4 col-tanggal">{{ \Carbon\Carbon::parse($p->created_at)->format('d-m-Y H:i:s') }}</td>
                            <td class="px-6 py-4 col-nama">{{ $p->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 col-pangkat">{{ $p->user->userProfile->pangkat ?? '-' }}</td>
                            <td class="px-6 py-4 col-jabatan">{{ $p->user->userProfile->jabatan ?? '-' }}</td>
                            <td class="px-6 py-4 col-polres">{{ $p->polres->nama ?? '-' }}</td>
                            <td class="px-6 py-4 col-polsek">{{ $p->polsek->nama ?? '-' }}</td>
                            <td class="px-6 py-4 col-jumlah_beras">{{ $p->jumlah_beras }} Kg</td>
                            <td class="px-6 py-4 col-foto">
                                @if ($p->foto_ktp)
                                    <img src="{{ asset('uploads/ktp/' . $p->foto_ktp) }}" class="h-16 w-auto rounded border">
                                @else
                                    <span class="text-gray-400 italic">Tidak ada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Flatpickr --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
const dropdownButton = document.getElementById('dropdownButton');
const dropdownMenu = document.getElementById('dropdownMenu');
const filterItems = document.querySelectorAll('.filter-item');
const filterForm = document.getElementById('filter-form');
const dynamicInput = document.getElementById('dynamic-input');
const resetButton = document.getElementById('resetButton');
const filtersHidden = document.getElementById('filters-hidden');

let selectedFilters = @json($selectedFilters);

dropdownButton.addEventListener('click', () => {
    dropdownMenu.classList.toggle('hidden');
});

function renderInputs() {
    dynamicInput.innerHTML = '';

    if(selectedFilters.length === 0){
        filterForm.classList.add('hidden');
        filtersHidden.value = '';
        return;
    }

    filterForm.classList.remove('hidden');
    filtersHidden.value = selectedFilters.join(',');

    selectedFilters.forEach(value => {
        let requestValue = new URLSearchParams(window.location.search).get(value) || '';
        let inputHtml = '';

        if(value === 'tanggal'){
            inputHtml = `<div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="text" id="date-range" name="tanggal" value="${requestValue}" placeholder="Pilih rentang tanggal" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500">
            </div>`;
        } else if(value === 'jumlah'){
            inputHtml = `<div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Beras (Kg)</label>
                <input type="number" name="jumlah" value="${requestValue}" placeholder="Cari Jumlah Beras" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500">
            </div>`;
        } else {
            inputHtml = `<div>
                <label class="block text-sm font-medium text-gray-700 mb-1">${value.charAt(0).toUpperCase() + value.slice(1)}</label>
                <input type="text" name="${value}" value="${requestValue}" placeholder="Cari ${value}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500">
            </div>`;
        }

        dynamicInput.innerHTML += inputHtml;
    });

    if(selectedFilters.includes('tanggal')){
        let defaultDates = [];
        const urlDate = new URLSearchParams(window.location.search).get('tanggal');
        if(urlDate){
            defaultDates = urlDate.split(' to ');
        }

        flatpickr("#date-range", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: false,
            defaultDate: defaultDates.length ? defaultDates : null
        });
    }
}

function updateSymbols() {
    filterItems.forEach(item => {
        const val = item.dataset.value;
        const symbol = item.querySelector('.filter-symbol');
        symbol.textContent = selectedFilters.includes(val) ? '✔' : '✖';
    });
}

filterItems.forEach(item => {
    item.addEventListener('click', () => {
        const val = item.dataset.value;
        if(selectedFilters.includes(val)){
            selectedFilters = selectedFilters.filter(f => f !== val);
        } else {
            selectedFilters.push(val);
        }
        updateSymbols();
        renderInputs();
    });
});

resetButton.addEventListener('click', () => {
    selectedFilters = [];
    updateSymbols();
    renderInputs();
});

// Inisialisasi awal
updateSymbols();
renderInputs();
</script>
@endsection
