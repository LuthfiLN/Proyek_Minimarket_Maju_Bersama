<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

if (isset($_POST['tambah_keranjang'])) {
    $id_produk = $_POST['id_produk'];
    $jumlah = intval($_POST['jumlah']);

    $res = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = $id_produk");
    $prod = mysqli_fetch_assoc($res);

    if ($prod) {
        if ($jumlah > $prod['stok']) {
            echo "<script>alert('Gagal! Stok tersisa: " . $prod['stok'] . "');</script>";
        } else {
            if (isset($_SESSION['keranjang'][$id_produk])) {
                $_SESSION['keranjang'][$id_produk]['jumlah'] += $jumlah;
                $_SESSION['keranjang'][$id_produk]['subtotal'] = $_SESSION['keranjang'][$id_produk]['jumlah'] * $prod['harga'];
            } else {
                $_SESSION['keranjang'][$id_produk] = [
                    'nama_produk' => $prod['nama_produk'],
                    'harga' => $prod['harga'],
                    'jumlah' => $jumlah,
                    'subtotal' => $prod['harga'] * $jumlah
                ];
            }
        }
    }
}

if (isset($_GET['hapus_item'])) {
    unset($_SESSION['keranjang'][$_GET['hapus_item']]);
    header("Location: transaksi.php");
    exit;
}

if (isset($_POST['checkout'])) {
    $total_harga = intval($_POST['total_harga']);
    $bayar = intval($_POST['bayar']);
    $kembali = $bayar - $total_harga;

    if ($bayar >= $total_harga && !empty($_SESSION['keranjang'])) {
        $no_faktur = "TRX-" . time();
        $id_user = $_SESSION['id_user'];

        if (mysqli_query($koneksi, "INSERT INTO transaksi (no_faktur, total_harga, bayar, kembali, id_user) VALUES ('$no_faktur', $total_harga, $bayar, $kembali, $id_user)")) {
            $id_transaksi = mysqli_insert_id($koneksi);
            foreach ($_SESSION['keranjang'] as $id_p => $item) {
                $jml = $item['jumlah'];
                $sub = $item['subtotal'];
                mysqli_query($koneksi, "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, subtotal) VALUES ($id_transaksi, $id_p, $jml, $sub)");
                mysqli_query($koneksi, "UPDATE produk SET stok = stok - $jml WHERE id = $id_p");
            }
            unset($_SESSION['keranjang']);
            echo "<script>alert('Transaksi Berhasil!'); window.location='riwayat.php';</script>";
        }
    }
}

$produk_query = mysqli_query($koneksi, "SELECT * FROM produk WHERE stok > 0 ORDER BY nama_produk ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>MajuMart - Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="flex min-vh-100 min-h-screen">
        <?php include 'sidebar.php'; ?>
        <main class="flex-1 p-4 md:p-8 w-full">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b">
                <button id="openSidebar" class="md:hidden w-10 h-10 bg-white border rounded-lg"><i class="fa-solid fa-bars"></i></button>
                <h1 class="text-xl font-bold">Transaksi Penjualan</h1>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white p-5 rounded-xl border h-fit shadow-sm">
                    <form action="" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Item Produk</label>
                            <select name="id_produk" required class="w-full border p-2 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20">
                                <option value="">-- Pilih --</option>
                                <?php while ($p = mysqli_fetch_assoc($produk_query)) : ?>
                                    <option value="<?= $p['id']; ?>"><?= $p['nama_produk']; ?> (Rp<?= number_format($p['harga'], 0, ',', '.'); ?>)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Qty</label><input type="number" name="jumlah" value="1" min="1" class="w-full border p-2 rounded-lg text-sm"></div>
                        <button type="submit" name="tambah_keranjang" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold text-sm shadow">Tambah</button>
                    </form>
                </div>
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white rounded-xl border overflow-hidden">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 border-b text-xs font-semibold text-slate-500 uppercase">
                                <tr>
                                    <th class="p-3 pl-4">Item</th>
                                    <th class="p-3">Harga</th>
                                    <th class="p-3 text-center">Qty</th>
                                    <th class="p-3">Subtotal</th>
                                    <th class="p-3 text-center">Hapus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <?php $total = 0;
                                foreach ($_SESSION['keranjang'] as $id_k => $item): $total += $item['subtotal']; ?>
                                    <tr>
                                        <td class="p-3 pl-4 font-semibold"><?= $item['nama_produk']; ?></td>
                                        <td class="p-3">Rp<?= number_format($item['harga'], 0, ',', '.'); ?></td>
                                        <td class="p-3 text-center"><?= $item['jumlah']; ?></td>
                                        <td class="p-3 font-bold">Rp<?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                        <td class="p-3 text-center"><a href="transaksi.php?hapus_item=<?= $id_k; ?>" class="text-red-500"><i class="fa-solid fa-trash-can"></i></a></td>
                                    </tr>
                                <?php endforeach;
                                if (empty($_SESSION['keranjang'])): ?>
                                    <tr>
                                        <td colspan="5" class="p-6 text-center text-slate-400">Keranjang kosong.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($total > 0) : ?>
                        <div class="bg-slate-900 text-white rounded-xl p-5 shadow">
                            <form action="" method="POST" class="space-y-4">
                                <input type="hidden" name="total_harga" value="<?= $total; ?>">
                                <div class="flex justify-between items-center border-b border-slate-800 pb-3"><span>TOTAL:</span><span class="text-2xl font-black text-blue-400">Rp<?= number_format($total, 0, ',', '.'); ?></span></div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div><label class="block text-xs text-slate-400 mb-1">Bayar (Rp)</label><input type="number" name="bayar" id="bayar" min="<?= $total; ?>" required class="w-full bg-slate-800 p-2 rounded-lg font-bold border border-slate-700"></div>
                                    <div><label class="block text-xs text-slate-400 mb-1">Kembali</label>
                                        <div id="kembali" class="w-full bg-slate-800/50 p-2 rounded-lg text-emerald-400 font-bold text-lg">Rp0</div>
                                    </div>
                                </div>
                                <button type="submit" name="checkout" class="w-full bg-emerald-600 text-white py-2.5 rounded-lg font-bold shadow">Proses Transaksi</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script>
        const bInput = document.getElementById('bayar');
        const kDiv = document.getElementById('kembali');
        if (bInput) {
            bInput.addEventListener('input', function() {
                const sisa = (parseInt(this.value) || 0) - <?= $total; ?>;
                kDiv.innerText = sisa >= 0 ? "Rp" + sisa.toLocaleString('id-ID') : "Rp0";
            });
        }
    </script>
</body>

</html>