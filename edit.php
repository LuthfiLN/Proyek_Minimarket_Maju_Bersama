<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}
include 'koneksi.php';

$id = $_GET['id'] ?? 0;
$res = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = $id");
$data = mysqli_fetch_assoc($res);

if (isset($_POST['update'])) {
    $kode = $_POST['kode_produk'];
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if (mysqli_query($koneksi, "UPDATE produk SET kode_produk='$kode', nama_produk='$nama', harga=$harga, stok=$stok WHERE id=$id")) {
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>MajuMart - Edit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="flex min-vh-100 min-h-screen">
        <?php include 'sidebar.php'; ?>
        <main class="flex-1 p-4 md:p-8 w-full">
            <div class="mb-6 pb-4 border-b border-slate-200">
                <h1 class="text-xl font-bold">Ubah Produk</h1>
            </div>
            <div class="bg-white p-6 rounded-xl border max-w-xl">
                <form action="" method="POST" class="space-y-4">
                    <div><label class="block text-sm font-semibold mb-1">Kode</label><input type="text" name="kode_produk" value="<?= $data['kode_produk']; ?>" required class="w-full border p-2 rounded-lg text-sm"></div>
                    <div><label class="block text-sm font-semibold mb-1">Nama</label><input type="text" name="nama_produk" value="<?= $data['nama_produk']; ?>" required class="w-full border p-2 rounded-lg text-sm"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-semibold mb-1">Harga</label><input type="number" name="harga" value="<?= $data['harga']; ?>" required class="w-full border p-2 rounded-lg text-sm"></div>
                        <div><label class="block text-sm font-semibold mb-1">Stok</label><input type="number" name="stok" value="<?= $data['stok']; ?>" required class="w-full border p-2 rounded-lg text-sm"></div>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" name="update" class="bg-amber-500 text-white px-4 py-2 rounded-lg text-sm font-semibold">Perbarui</button>
                        <a href="index.php" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm">Batal</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>