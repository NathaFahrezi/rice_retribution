@extends('layout')

@section('title', 'Dashboard Super Admin')

@section('content')
<div class="flex-1 overflow-x-hidden overflow-y-auto p-6 md:p-8 scrollbar-hide">
    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Stok Awal</p>
                <h2 id="stokAwalCard" class="text-3xl font-bold text-gray-800 mt-1">0 Kg</h2>
            </div>
            <div class="bg-yellow-100 text-yellow-600 rounded-full p-3">
                <i class="fas fa-warehouse text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Jumlah Terdistribusi</p>
                <h2 id="terdistribusiCard" class="text-3xl font-bold text-gray-800 mt-1">0 Kg</h2>
            </div>
            <div class="bg-blue-100 text-blue-600 rounded-full p-3">
                <i class="fas fa-truck text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Sisa Stok</p>
                <h2 id="sisaStokCard" class="text-3xl font-bold text-gray-800 mt-1">0 Kg</h2>
            </div>
            <div class="bg-green-100 text-green-600 rounded-full p-3">
                <i class="fas fa-box text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Filter dan Grafik --}}
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" id="start_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" id="end_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500">
            </div>

            <div class="md:col-span-2">
                <label for="polres" class="block text-sm font-medium text-gray-700 mb-1">Pilih Polres</label>
                <select id="polres" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-green-500 focus:ring-green-500">
                    <option value="all">Seluruh Polres</option>
                    <option value="padang">Polresta Padang</option>
                    <option value="bukittinggi">Polresta Bukittinggi</option>
                    <option value="agam">Polres Agam</option>
                    <option value="dharmasraya">Polres Dharmasraya</option>
                    <option value="kep_mentawai">Polres Kep. Mentawai</option>
                    <option value="limapuluhkota">Polres Lima Puluh Kota</option>
                    <option value="padangpanjang">Polres Padang Panjang</option>
                    <option value="padangpariaman">Polres Padang Pariaman</option>
                    <option value="pariaman">Polres Pariaman</option>
                    <option value="pasaman">Polres Pasaman</option>
                    <option value="pasamanbarat">Polres Pasaman Barat</option>
                    <option value="payakumbuh">Polres Payakumbuh</option>
                    <option value="pesisirselatan">Polres Pesisir Selatan</option>
                    <option value="sawahlunto">Polres Sawahlunto</option>
                    <option value="sijunjung">Polres Sijunjung</option>
                    <option value="solok">Polres Solok</option>
                    <option value="solokkota">Polres Solok Kota</option>
                    <option value="solokselatan">Polres Solok Selatan</option>
                    <option value="tanahtdatar">Polres Tanah Datar</option>
                </select>
            </div>
        </div>

        {{-- Tombol aksi --}}
        <div class="flex items-center justify-between mb-4">
            <div class="text-sm text-gray-600">Pilih rentang tanggal dan polres lalu klik <span class="font-medium text-gray-800">Tampilkan</span>.</div>
            <div class="space-x-2">
                <button id="btnShow" class="px-4 py-2 bg-lime-600 text-white rounded-lg shadow hover:bg-lime-700 transition"><i class="fas fa-chart-line mr-2"></i>Tampilkan</button>
                <button id="btnExportPDF" class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-gray-900 transition"><i class="fas fa-file-pdf mr-2"></i>Unduh PDF</button>
                <button id="btnExportExcel" class="px-4 py-2 bg-white border border-gray-300 text-gray-800 rounded-lg shadow hover:bg-gray-50 transition"><i class="fas fa-file-excel mr-2 text-green-600"></i>Unduh Excel</button>
            </div>
        </div>

        <canvas id="distributionChart" height="140"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    const stokAwalCard = document.getElementById('stokAwalCard');
    const terdistribusiCard = document.getElementById('terdistribusiCard');
    const sisaStokCard = document.getElementById('sisaStokCard');
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const polresSelect = document.getElementById('polres');
    const btnShow = document.getElementById('btnShow');
    const btnExportPDF = document.getElementById('btnExportPDF');
    const btnExportExcel = document.getElementById('btnExportExcel');

    function fmtDate(d){
        const yyyy=d.getFullYear();
        const mm=String(d.getMonth()+1).padStart(2,'0');
        const dd=String(d.getDate()).padStart(2,'0');
        return `${yyyy}-${mm}-${dd}`;
    }

    function dateRangeLabels(startStr, endStr){
        const start = new Date(startStr), end = new Date(endStr);
        if(isNaN(start) || isNaN(end) || start>end) return [];
        const labels=[];
        for(let d=new Date(start); d<=end; d.setDate(d.getDate()+1)){
            labels.push(fmtDate(new Date(d)));
        }
        return labels;
    }

    function demoDataForLabels(labels){
        const distribusi = labels.map(()=>Math.floor(Math.random()*300)+50);
        const stokAwal = labels.map(()=>2000);
        let remaining=stokAwal[0];
        const sisa=[];
        for(let i=0;i<labels.length;i++){ remaining=Math.max(0, remaining - distribusi[i]); sisa.push(remaining); }
        return { distribusi, stokAwal, sisa };
    }

    const ctx = document.getElementById('distributionChart').getContext('2d');
    let distributionChart = new Chart(ctx, {
        type:'bar',
        data:{ labels:[], datasets:[
            { label:'Distribusi (Kg)', data:[], backgroundColor:'rgba(132,204,22,0.8)', borderRadius:6 },
            { label:'Stok Awal (Kg)', data:[], backgroundColor:'rgba(59,130,246,0.8)', borderRadius:6 },
            { label:'Sisa Stok (Kg)', data:[], backgroundColor:'rgba(239,68,68,0.8)', borderRadius:6 }
        ]},
        options:{ responsive:true, interaction:{mode:'index',intersect:false}, plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:true}} }
    });

    function updateChartData(labels, distribusi, stokAwal, sisa, totals=null){
        distributionChart.data.labels = labels;
        distributionChart.data.datasets[0].data = distribusi;
        distributionChart.data.datasets[1].data = stokAwal;
        distributionChart.data.datasets[2].data = sisa;
        distributionChart.update();

        if(totals){
            stokAwalCard.textContent = totals.total_stok_awal + ' Kg';
            terdistribusiCard.textContent = totals.total_terdistribusi + ' Kg';
            sisaStokCard.textContent = totals.total_sisa + ' Kg';
        } else {
            const totalDistribusi = distribusi.reduce((a,b)=>a+b,0);
            const totalStokAwal = stokAwal.reduce((a,b)=>a+b,0);
            const totalSisa = sisa.reduce((a,b)=>a+b,0);
            stokAwalCard.textContent = totalStokAwal + ' Kg';
            terdistribusiCard.textContent = totalDistribusi + ' Kg';
            sisaStokCard.textContent = totalSisa + ' Kg';
        }
    }

    btnShow.addEventListener('click', async ()=>{
        const start = startInput.value, end=endInput.value, polres=polresSelect.value;
        if(!start||!end){ alert('Silakan pilih mulai dan sampai tanggal.'); return; }
        if(new Date(start)>new Date(end)){ alert('Tanggal "Dari" tidak boleh lebih besar dari "Sampai".'); return; }

        try{
            const url = `/api/chart-data?start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}&polres=${encodeURIComponent(polres)}`;
            const resp = await fetch(url);
            if(resp.ok){
                const json = await resp.json();
                if(json && json.labels){
                    const totals = {
                        total_stok_awal: json.total_stok_awal,
                        total_terdistribusi: json.total_terdistribusi,
                        total_sisa: json.total_sisa
                    };
                    updateChartData(json.labels, json.distribusi, json.stok_awal, json.sisa, totals);
                    return;
                }
            }
            const labels = dateRangeLabels(start,end);
            const demo = demoDataForLabels(labels);
            updateChartData(labels,demo.distribusi,demo.stokAwal,demo.sisa);
        }catch(err){
            const labels = dateRangeLabels(start,end);
            const demo = demoDataForLabels(labels);
            updateChartData(labels,demo.distribusi,demo.stokAwal,demo.sisa);
            console.warn('Gagal ambil data dari server, pakai demo.',err);
        }
    });

    // Export PDF
    btnExportPDF.addEventListener('click', ()=>{
        const labels = distributionChart.data.labels||[];
        if(!labels.length){ alert('Tidak ada data untuk diekspor.'); return; }
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape','pt','a4');
        const canvas = document.getElementById('distributionChart');
        const imgData = canvas.toDataURL('image/png',1.0);
        const pageWidth = pdf.internal.pageSize.getWidth(), margin=20;
        const imgWidth = pageWidth-margin*2, imgHeight=(canvas.height/canvas.width)*imgWidth;
        pdf.setFontSize(12);
        pdf.text(`Grafik Distribusi Beras - Polres: ${polresSelect.value} | ${startInput.value || '-'} to ${endInput.value || '-'}`, margin,30);
        pdf.addImage(imgData,'PNG',margin,40,imgWidth,imgHeight);
        pdf.save(`grafik-distribusi-${polresSelect.value}-${startInput.value||'all'}-${endInput.value||'all'}.pdf`);
    });

    // Export Excel
    btnExportExcel.addEventListener('click', ()=>{
        const labels = distributionChart.data.labels||[];
        if(!labels.length){ alert('Tidak ada data untuk diekspor.'); return; }
        const distribusi = distributionChart.data.datasets[0].data||[];
        const stokAwal = distributionChart.data.datasets[1].data||[];
        const sisa = distributionChart.data.datasets[2].data||[];
        const rows=[['Tanggal','Distribusi (Kg)','Stok Awal (Kg)','Sisa Stok (Kg)']];
        for(let i=0;i<labels.length;i++){
            rows.push([labels[i]||'',distribusi[i]||0,stokAwal[i]||0,sisa[i]||0]);
        }
        const ws=XLSX.utils.aoa_to_sheet(rows);
        const wb=XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb,ws,'GrafikDistribusi');
        XLSX.writeFile(wb,`grafik-distribusi-${polresSelect.value}-${startInput.value||'all'}-${endInput.value||'all'}.xlsx`);
    });

    // default last 7 hari
    (function setDefaultDates(){
        const end=new Date(), start=new Date(); start.setDate(end.getDate()-7);
        if(!startInput.value) startInput.value=fmtDate(start);
        if(!endInput.value) endInput.value=fmtDate(end);
    })();

    document.getElementById('btnShow').click();
</script>

@if(session('alert'))
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
