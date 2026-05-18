<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$result = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = $id");
if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit;
}
$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MajuMart - Detail</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-800 antialiased font-sans">
    <div class="flex min-vh-100 min-h-screen relative overflow-x-hidden">
        <?php include 'sidebar.php'; ?>
        <main class="flex-1 p-4 md:p-8 w-full">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <button id="openSidebar" class="md:hidden flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-lg shadow-sm"><i class="fa-solid fa-bars text-lg"></i></button>
                    <h1 class="text-xl md:text-2xl font-bold text-slate-800">Rincian Informasi Barang</h1>
                </div>
                <a href="index.php" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2.5 rounded-lg text-sm transition">Kembali</a>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-xl">
                <div class="bg-slate-900 text-white p-4">
                    <p class="text-[10px] uppercase font-bold text-slate-400">Kode Unik Produk</p>
                    <h2 class="text-xl font-mono text-blue-400 font-bold"><?= $data['kode_produk']; ?></h2>
                </div>
                <div class="p-6 space-y-4">
                    <div><span class="text-xs text-slate-400 block uppercase font-semibold">Nama Produk</span>
                        <h3 class="text-xl font-bold"><?= $data['nama_produk']; ?></h3>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-3 rounded-lg border">
                            <span class="text-xs text-slate-400 block font-semibold">Harga</span>
                            <p class="text-lg font-bold text-blue-600">Rp <?= number_format($data['harga'], 0, ',', '.'); ?></p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg border">
                            <span class="text-xs text-slate-400 block font-semibold">Ketersediaan Stok</span>
                            <p class="text-lg font-bold"><?= $data['stok']; ?> Pcs</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>