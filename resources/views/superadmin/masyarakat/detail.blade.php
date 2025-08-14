@extends('layout')

@section('title', 'Detail Data Penjualan')

@section('content')
    <style>
        .custom-file-input::-webkit-file-upload-button {
            visibility: hidden;
        }

        .custom-file-input::before {
            content: 'Pilih file';
            display: inline-block;
            background: #4B5563;
            color: white;
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            outline: none;
            cursor: pointer;
            font-weight: 500;
            white-space: nowrap;
            user-select: none;
        }

        .custom-file-input:hover::before {
            background: #374151;
        }

        .custom-file-input:active::before {
            background: #1F2937;
        }
    </style>
    <div class="flex-1 overflow-x-hidden overflow-y-auto p-6 md:p-8 scrollbar-hide">
        <div class="bg-white rounded-xl shadow-lg w-full p-8 md:p-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-1 flex flex-col items-center">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Foto KTP / Wajah</h3>
                    <div class="bg-gray-100 rounded-lg p-2 border-2 border-gray-300 w-full max-w-sm">
                        <img src="{{ asset('uploads/ktp/' . $masyarakat->foto_ktp) }}" 
                             alt="Foto KTP Masyarakat"
                             class="w-full h-auto rounded-md">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Penerima</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah Beras (Kg)</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $masyarakat->jumlah_beras }} Kg</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Ditambahkan</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($masyarakat->created_at)->translatedFormat('d F Y H:i') }}
                            </dd>
                        </div>
                    </dl>

                    {{-- Tombol Edit & Hapus dihilangkan untuk superadmin --}}
                    <div class="mt-8">
                        <a href="{{ route('superadmin.masyarakat') }}"
                           class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Kembali ke Daftar
                        </a>
                    </div>
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
