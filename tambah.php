<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $kode = $_POST['kode_produk'];
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if (mysqli_query($koneksi, "INSERT INTO produk (kode_produk, nama_produk, harga, stok) VALUES ('$kode', '$nama', $harga, $stok)")) {
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>MajuMart - Tambah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="flex min-vh-100 min-h-screen">
        <?php include 'sidebar.php'; ?>
        <main class="flex-1 p-4 md:p-8 w-full">
            <div class="mb-6 pb-4 border-b border-slate-200">
                <h1 class="text-xl font-bold">Tambah Produk</h1>
            </div>
            <div class="bg-white p-6 rounded-xl border border-slate-200 max-w-xl shadow-sm">
                <form action="" method="POST" class="space-y-4">
                    <div><label class="block text-sm font-semibold mb-1">Kode Produk</label><input type="text" name="kode_produk" required class="w-full border p-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20"></div>
                    <div><label class="block text-sm font-semibold mb-1">Nama Produk</label><input type="text" name="nama_produk" required class="w-full border p-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-semibold mb-1">Harga (Rp)</label><input type="number" name="harga" required class="w-full border p-2 rounded-lg text-sm"></div>
                        <div><label class="block text-sm font-semibold mb-1">Stok</label><input type="number" name="stok" required class="w-full border p-2 rounded-lg text-sm"></div>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" name="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">Simpan</button>
                        <a href="index.php" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm">Kembali</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>