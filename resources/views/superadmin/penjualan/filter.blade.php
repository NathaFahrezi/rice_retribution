@extends('layout')

@section('title', 'Data Penjualan')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Filter Data Penjualan</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.penjualan.filter') }}" class="row g-3">

                <!-- Polres -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Polres</label>
                    <select name="polres_id" id="polres_id" class="form-select">
                        <option value="">-- Pilih Polres --</option>
                        @foreach($polresList as $polres)
                            <option value="{{ $polres->id }}" {{ request('polres_id') == $polres->id ? 'selected' : '' }}>
                                {{ $polres->nama ?? $polres->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Polsek -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Polsek</label>
                    <select name="polsek_id" id="polsek_id" class="form-select">
                        <option value="">-- Pilih Polsek --</option>
                        @foreach($polsekList as $polsek)
                            <option value="{{ $polsek->id }}" {{ request('polsek_id') == $polsek->id ? 'selected' : '' }}>
                                {{ $polsek->nama ?? $polsek->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- User -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">User</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">-- Pilih User --</option>
                        @foreach($userList as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name ?? $user->nama }} / {{ $user->nrp ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Rentang Tanggal</label>
                    <div class="d-flex gap-2">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>
                </div>

                <!-- Tombol Filter -->
                <div class="col-12 text-end mt-2">
                    <button class="btn btn-success px-4" type="submit">Filter</button>
                    <a href="{{ route('superadmin.penjualan.index') }}" class="btn btn-secondary px-4">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Data Penjualan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>NRP</th>
                            <th>Pangkat</th>
                            <th>Jabatan</th>
                            <th>Polres</th>
                            <th>Polsek</th>
                            <th>Jumlah Penjualan</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataPenjualan as $i => $item)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>{{ $item->user->nrp ?? '-' }}</td>
                                <td>{{ $item->user->profile->pangkat ?? '-' }}</td>
                                <td>{{ $item->user->profile->jabatan ?? '-' }}</td>
                                <td>{{ $item->polres->nama ?? $item->polres->name ?? '-' }}</td>
                                <td>{{ $item->polsek->nama ?? $item->polsek->name ?? '-' }}</td>
                                <td class="text-center">{{ $item->jumlah_beras ?? '-' }}</td>
                                <td class="text-center">
                                    @if($item->foto_ktp)
                                        <img src="{{ asset('storage/'.$item->foto_ktp) }}" width="80" class="img-thumbnail">
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- AJAX Script untuk Polres → Polsek → User -->
    <script>
    document.getElementById('polres_id').addEventListener('change', function(){
        let polresId = this.value;
        let polsekSelect = document.getElementById('polsek_id');
        let userSelect = document.getElementById('user_id');

        polsekSelect.innerHTML = '<option value="">-- Pilih Polsek --</option>';
        userSelect.innerHTML = '<option value="">-- Pilih User --</option>';

        if(polresId) {
            fetch(`/superadmin/api/polsek/${polresId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(p => {
                        polsekSelect.innerHTML += `<option value="${p.id}">${p.nama ?? p.name}</option>`;
                    });
                })
                .catch(err => console.error(err));
        }
    });

    document.getElementById('polsek_id').addEventListener('change', function(){
        let polsekId = this.value;
        let userSelect = document.getElementById('user_id');

        userSelect.innerHTML = '<option value="">-- Pilih User --</option>';

        if(polsekId) {
            fetch(`/superadmin/api/users/${polsekId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(u => {
                        userSelect.innerHTML += `<option value="${u.id}">${u.name}</option>`;
                    });
                })
                .catch(err => console.error(err));
        }
    });
    </script>

</div>
@endsection
