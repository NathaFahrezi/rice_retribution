@extends('layout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Data Penjualan</h2>

    <!-- Form filter -->
     <div class="col-md-4">
            <label for="start_date" class="form-label">Dari Tanggal:</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label">Sampai Tanggal:</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>


    <form method="GET" action="{{ route('superadmin.penjualan.filter') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="polres_id" class="form-label">Polres:</label>
            <select name="polres_id" id="polres_id" class="form-select">
                <option value="">-- Pilih Polres --</option>
                @foreach($polresList as $polres)
                    <option value="{{ $polres->id }}" {{ request('polres_id') == $polres->id ? 'selected' : '' }}>{{ $polres->nama ?? $polres->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label for="polsek_id" class="form-label">Polsek:</label>
            <select name="polsek_id" id="polsek_id" class="form-select">
                <option value="">-- Pilih Polsek --</option>
                @foreach($polsekList as $polsek)
                    <option value="{{ $polsek->id }}" {{ request('polsek_id') == $polsek->id ? 'selected' : '' }}>{{ $polsek->nama ?? $polsek->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label for="user_id" class="form-label">User:</label>
            <select name="user_id" id="user_id" class="form-select">
                <option value="">-- Pilih User --</option>
                @foreach($userList ?? [] as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name ?? $user->nama }} / {{ $user->nrp ?? '-' }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <div class="table-responsive" id="table-container">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>NRP</th>
                        <th>Pangkat</th>
                        <th>Jabatan</th>
                        <th>Polres</th>
                        <th>Polsek</th>
                        <th>Jumlah Beras</th>
                        <th>Foto KTP</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 0; @endphp
                @forelse($dataPenjualan as $data)
                @php $i++; @endphp
                    <tr>
                            <td class="text-center">{{ $i+1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d-m-Y H:i') }}</td>
                            <td>{{ $data->user->name ?? '-' }}</td>
                            <td>{{ $data->user->nrp ?? '-' }}</td>
                            <td>{{ $data->user->pangkat ?? '-' }}</td>
                            <td>{{ $data->user->jabatan ?? '-' }}</td>
                            <td>{{ $data->polres->nama ?? '-' }}</td>
                            <td>{{ $data->polsek->nama ?? '-' }}</td>
                            <td class="text-center">{{ $data->jumlah_beras ?? '-' }}</td>
                            <td class="text-center">
                                @if($data->foto_ktp)
                                    <img src="{{ asset('storage/'.$data->foto_ktp) }}" alt="Foto KTP" class="img-thumbnail" width="100">
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

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
            .catch(err => console.error('Error fetch polsek:', err));
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
            .catch(err => console.error('Error fetch users:', err));
    }
});

document.getElementById('user_id').addEventListener('change', function(){
    let userId = this.value;
    let polresId = document.getElementById('polres_id').value;
    let polsekId = document.getElementById('polsek_id').value;

    if(userId) {
        fetch(`/superadmin/api/penjualan/filter?polres_id=${polresId}&polsek_id=${polsekId}&user_id=${userId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('table-container').innerHTML = data.html;
            })
            .catch(err => console.error('Error fetch penjualan:', err));
    }
});

</script>
@endsection
