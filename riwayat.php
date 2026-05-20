<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// 1. Logika Filter Rentang Tanggal (Default: awal bulan ini s.d hari ini)
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01');
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : date('Y-m-d');

// Keamanan query terhadap SQL Injection
$tgl_mulai_query = mysqli_real_escape_string($koneksi, $tgl_mulai) . " 00:00:00";
$tgl_selesai_query = mysqli_real_escape_string($koneksi, $tgl_selesai) . " 23:59:59";

// 2. Tarik Data Utama Sesuai Rentang Tanggal
$result = mysqli_query($koneksi, "SELECT transaksi.*, user.nama_lengkap FROM transaksi LEFT JOIN user ON transaksi.id_user = user.id WHERE transaksi.tanggal_transaksi BETWEEN '$tgl_mulai_query' AND '$tgl_selesai_query' ORDER BY transaksi.tanggal_transaksi DESC");
$omset = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM transaksi WHERE tanggal_transaksi BETWEEN '$tgl_mulai_query' AND '$tgl_selesai_query'"))['total'] ?? 0;

// 3. Tarik Agregasi Harian untuk Keperluan Chart.js
$query_grafik = mysqli_query($koneksi, "SELECT DATE(tanggal_transaksi) as tanggal, SUM(total_harga) as total_harian FROM transaksi WHERE tanggal_transaksi BETWEEN '$tgl_mulai_query' AND '$tgl_selesai_query' GROUP BY DATE(tanggal_transaksi) ORDER BY tanggal ASC");

$labels_grafik = [];
$data_grafik = [];
while ($g = mysqli_fetch_assoc($query_grafik)) {
    $labels_grafik[] = date('d/m', strtotime($g['tanggal']));
    $data_grafik[] = (int)$g['total_harian'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MajuMart - Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Library grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* CSS internal untuk handle kebersihan cetakan cetak dokumen/PDF */
        @media print {
            #sidebar, .no-print, form, button { display: none !important; }
            body { background-color: #fff; color: #000; }
            main { padding: 0 !important; }
        }
    </style>
</head>

<!-- Menyelaraskan kelas antialiased dan font-sans agar tipografi sama dengan page produk -->
<body class="bg-slate-100 text-slate-800 antialiased font-sans">
    <!-- Ditambahkan kelas relative dan overflow-x-hidden agar wrapper sidebar mobile tidak merusak layar -->
    <div class="flex min-vh-100 min-h-screen relative overflow-x-hidden">

        <?php include 'sidebar.php'; ?>

        <!-- Menggunakan overflow-hidden agar chart dan tabel panjang tidak memotong grid utama -->
        <main class="flex-1 p-4 md:p-8 w-full overflow-hidden">
            
            <!-- Header (Menggunakan struktur flex justify-between dan border-slate-200 asli page produk) -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <button id="openSidebar" class="md:hidden flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-lg shadow-sm"><i class="fa-solid fa-bars text-lg"></i></button>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-slate-800">Laporan & Riwayat Penjualan</h1>
                        <p class="hidden print:block text-xs text-slate-500 font-semibold mt-1">Periode: <?= date('d/m/Y', strtotime($tgl_mulai)); ?> - <?= date('d/m/Y', strtotime($tgl_selesai)); ?></p>
                    </div>
                </div>
                <!-- Tombol Aksi Cetak dengan gaya visual tombol "Tambah Produk" -->
                <button onclick="window.print()" class="no-print bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow transition active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Cetak Laporan
                </button>
            </div>

            <!-- Form Filter Tanggal (Menyesuaikan shadow-sm dan border-slate-200) -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 no-print">
                <form method="GET" action="" class="flex flex-col sm:flex-row items-end gap-4 text-sm">
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" value="<?= htmlspecialchars($tgl_mulai); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 outline-none focus:border-indigo-500 text-slate-700">
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" value="<?= htmlspecialchars($tgl_selesai); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 outline-none focus:border-indigo-500 text-slate-700">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg text-sm shadow transition active:scale-95 flex items-center gap-2 h-[38px] w-full sm:w-auto justify-center">
                        <i class="fa-solidxl fa-solid fa-sliders"></i> Filter
                    </button>
                </form>
            </div>

            <!-- Card Total Pendapatan Omset -->
            <div class="bg-gradient-to-tr from-indigo-600 to-blue-600 text-white p-4 rounded-xl shadow-md w-fit min-w-[280px] mb-6">
                <span class="text-xs text-indigo-200 font-semibold uppercase">Total Pendapatan (Omset)</span>
                <p class="text-2xl font-black mt-1">Rp<?= number_format($omset, 0, ',', '.'); ?></p>
            </div>

            <!-- Area Canvas Chart.js (Disetarakan dengan shadow-sm & border-slate-200) -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6">
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block mb-3">Grafik Tren Pendapatan</span>
                <div class="relative w-full h-64">
                    <canvas id="canvasGrafik"></canvas>
                </div>
            </div>

            <!-- Tabel Riwayat Penjualan (100% Identik dengan arsitektur pembungkus halaman manajemen produk) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse" style="min-width: 700px;">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-semibold uppercase">
                                <th class="py-4 px-6 text-center w-16">No</th>
                                <th class="py-4 px-4">No Faktur</th>
                                <th class="py-4 px-4">Tanggal</th>
                                <th class="py-4 px-4">Kasir</th>
                                <th class="py-4 px-6 text-right w-44">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php $no = 1;
                            while ($t = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-4 px-6 text-center text-slate-400 font-medium"><?= $no++; ?></td>
                                    <td class="py-4 px-4">
                                        <span class="bg-slate-100 text-slate-800 font-mono text-xs font-semibold px-2 py-1 rounded border border-slate-200">
                                            <?= htmlspecialchars($t['no_faktur']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-slate-500"><?= date('d/m/Y H:i', strtotime($t['tanggal_transaksi'])); ?></td>
                                    <td class="py-4 px-4 font-semibold text-slate-700"><?= htmlspecialchars($t['nama_lengkap'] ?? 'User Terhapus'); ?></td>
                                    <td class="py-4 px-6 font-bold text-slate-900 text-right">Rp <?= number_format($t['total_harga'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endwhile;
                            if (mysqli_num_rows($result) == 0): ?>
                                <tr>
                                    <td colspan="5" class="py-6 px-6 text-center text-slate-400">Belum ada data transaksi pada rentang tanggal ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Script Pembuat Grafik Chart.js -->
    <script>
        const ctx = document.getElementById('canvasGrafik').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels_grafik); ?>,
                datasets: [{
                    label: 'Omset Harian',
                    data: <?= json_encode($data_grafik); ?>,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>