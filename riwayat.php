<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$result = mysqli_query($koneksi, "SELECT transaksi.*, user.nama_lengkap FROM transaksi LEFT JOIN user ON transaksi.id_user = user.id ORDER BY transaksi.tanggal_transaksi DESC");
$omset = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM transaksi"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>MajuMart - Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="flex min-vh-100 min-h-screen">
        <?php include 'sidebar.php'; ?>
        <main class="flex-1 p-4 md:p-8 w-full">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b">
                <button id="openSidebar" class="md:hidden w-10 h-10 bg-white border rounded-lg"><i class="fa-solid fa-bars"></i></button>
                <h1 class="text-xl font-bold">Laporan & Riwayat Penjualan</h1>
            </div>
            <div class="bg-gradient-to-tr from-indigo-600 to-blue-600 text-white p-4 rounded-xl shadow-md w-fit min-w-[280px] mb-6">
                <span class="text-xs text-indigo-200 font-semibold uppercase">Total Pendapatan (Omset)</span>
                <p class="text-2xl font-black mt-1">Rp<?= number_format($omset, 0, ',', '.'); ?></p>
            </div>
            <div class="bg-white rounded-xl border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left" style="min-width:700px;">
                        <thead class="bg-slate-50 border-b text-xs text-slate-500 uppercase font-semibold">
                            <tr>
                                <th class="p-4 text-center">No</th>
                                <th class="p-4">No Faktur</th>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4">Kasir</th>
                                <th class="p-4">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php $no = 1;
                            while ($t = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-50/50">
                                    <td class="p-4 text-center text-slate-400"><?= $no++; ?></td>
                                    <td class="p-4 font-mono font-bold text-blue-600"><?= $t['no_faktur']; ?></td>
                                    <td class="p-4 text-slate-500"><?= date('d/m/Y H:i', strtotime($t['tanggal_transaksi'])); ?></td>
                                    <td class="p-4 font-medium"><?= $t['nama_lengkap'] ?? 'User Terhapus'; ?></td>
                                    <td class="p-4 font-bold text-slate-900">Rp<?= number_format($t['total_harga'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endwhile;
                            if (mysqli_num_rows($result) == 0): ?>
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400">Belum ada transaksi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>