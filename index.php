<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role_user = $_SESSION['role'];
$query = "SELECT * FROM produk ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MajuMart - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-800 antialiased font-sans">
    <div class="flex min-vh-100 min-h-screen relative overflow-x-hidden">

        <?php include 'sidebar.php'; ?>

        <main class="flex-1 p-4 md:p-8 w-full overflow-hidden">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <button id="openSidebar" class="md:hidden flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-lg shadow-sm"><i class="fa-solid fa-bars text-lg"></i></button>
                    <h1 class="text-xl md:text-2xl font-bold text-slate-800">Stok Manajemen Produk</h1>
                </div>
                <?php if ($role_user === 'Admin') : ?>
                    <a href="tambah.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow transition active:scale-95"><i class="fa-solid fa-plus mr-1"></i> Tambah Produk</a>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse" style="min-width: 700px;">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-semibold uppercase">
                                <th class="py-4 px-6 text-center w-16">No</th>
                                <th class="py-4 px-4 w-32">Kode</th>
                                <th class="py-4 px-4">Nama Produk</th>
                                <th class="py-4 px-4 w-44">Harga</th>
                                <th class="py-4 px-4 w-32">Stok</th>
                                <th class="py-4 px-6 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-4 px-6 text-center text-slate-400 font-medium"><?= $no++; ?></td>
                                    <td class="py-4 px-4"><span class="bg-slate-100 text-slate-800 font-mono text-xs font-semibold px-2 py-1 rounded border"><?= $row['kode_produk']; ?></span></td>
                                    <td class="py-4 px-4 font-semibold text-slate-900"><a href="view.php?id=<?= $row['id']; ?>" class="text-blue-600 hover:underline"><?= $row['nama_produk']; ?></a></td>
                                    <td class="py-4 px-4">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium <?= $row['stok'] < 20 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>"><?= $row['stok']; ?> pcs</span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex justify-center gap-1.5">
                                            <a href="view.php?id=<?= $row['id']; ?>" class="w-8 h-8 bg-slate-500 hover:bg-slate-600 text-white rounded flex items-center justify-center transition" title="Lihat"><i class="fa-solid fa-eye text-xs"></i></a>
                                            <?php if ($role_user === 'Admin') : ?>
                                                <a href="edit.php?id=<?= $row['id']; ?>" class="w-8 h-8 bg-amber-500 hover:bg-amber-600 text-white rounded flex items-center justify-center transition" title="Edit"><i class="fa-solid fa-pen-to-square text-xs"></i></a>
                                                <a href="hapus.php?id=<?= $row['id']; ?>" class="w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded flex items-center justify-center transition" onclick="return confirm('Hapus item ini?')" title="Hapus"><i class="fa-solid fa-trash text-xs"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>