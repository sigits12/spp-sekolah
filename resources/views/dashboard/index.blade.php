<!DOCTYPE html>
<html>
<head>
    <title>Simple Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <h1 class="text-2xl font-bold mb-5">Dashboard Keuangan Sekolah</h1>
    
    <div class="grid grid-cols-2 gap-4">
        
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-gray-500">Siswa Aktif</h2>
            <p class="text-xl font-semibold">{{ $jumlahSiswa }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-gray-500">Jumlah Kelas</h2>
            <p class="text-xl font-semibold">{{ $jumlahKelas }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-gray-500">Tahun Ajaran Aktif</h2>
            <p class="text-xl font-semibold">{{ $tahunAktif->nama ?? 'Tidak Ada' }}</p>
        </div>
        
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-gray-500">Total Item Biaya</h2>
            <p class="text-xl font-semibold">{{ $totalBiaya }} Item</p>
        </div>
        
    </div>
</body>
</html>