<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = array();
}

if (isset($_POST['tambah_keranjang'])) {
    $nama_prod = mysqli_real_escape_string($conn, $_POST['produk_input']);
    $jumlah = (int)$_POST['jumlah'];

    $cek = mysqli_query($conn, "SELECT id_produk, nama_produk, harga, stok FROM produk WHERE nama_produk = '$nama_prod'");
    if (mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_assoc($cek);
        if ($data['stok'] >= $jumlah) {
            $_SESSION['keranjang'][] = [
                'id_produk'   => $data['id_produk'],
                'nama_produk' => $data['nama_produk'],
                'harga'       => $data['harga'],
                'jumlah'      => $jumlah,
                'subtotal'    => $data['harga'] * $jumlah
            ];
        } else {
            echo "<script>alert('Stok tidak mencukupi! Sisa stok: {$data['stok']}');</script>";
        }
    } else {
        echo "<script>alert('Produk tidak ditemukan!');</script>";
    }
}

if (isset($_GET['kosongkan'])) {
    unset($_SESSION['keranjang']);
    header("location:index.php?page=transaksi");
    exit;
}

if (!isset($_SESSION['role'])) {
    header("location:login.php");
    exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Minimarket Maju Bersama</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        :root {
            --hijau-tua: #1A531A;
            --hijau-muda: #A4C639;
            --putih: #ffffff;
            --abu: #f4f7f6;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--abu);
            margin: 0;
            display: flex;
            overflow-x: hidden;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--hijau-muda);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--hijau-tua);
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #1A531A 0%, #0a240a 100%);
            height: 100vh;
            color: white;
            position: fixed;
            padding-top: 20px;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            white-space: nowrap;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            text-align: center;
            font-size: 1.1rem;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: opacity 0.3s;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: white;
            padding: 15px 25px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 1rem;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(164, 198, 57, 0.2);
            color: var(--hijau-muda);
            border-left: 5px solid var(--hijau-muda);
        }

        .sidebar a .ikon {
            margin-right: 15px;
            font-size: 1.4rem;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed h2 {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar.collapsed .teks-menu {
            display: none;
        }

        .sidebar.collapsed a {
            padding: 15px 20px;
            justify-content: center;
            border-left: 5px solid transparent;
        }

        .sidebar.collapsed a:hover,
        .sidebar.collapsed a.active {
            border-left: 5px solid var(--hijau-muda);
        }

        .sidebar.collapsed a .ikon {
            margin-right: 0;
            font-size: 1.5rem;
        }

        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 30px;
            min-height: 100vh;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        .top-bar {
            background: var(--putih);
            padding: 15px 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .btn-toggle {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--hijau-tua);
            margin-right: 15px;
            transition: 0.3s;
        }

        .btn-toggle:hover {
            color: var(--hijau-muda);
        }

        .section-card {
            background: var(--putih);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-top: 5px solid var(--hijau-tua);
        }

        h3 {
            color: var(--hijau-tua);
            margin-top: 0;
            border-bottom: 2px solid var(--hijau-muda);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .card-metrik {
            transition: all 0.3s ease;
            cursor: default;
        }

        .card-metrik:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            color: var(--hijau-tua);
            border-bottom: 2px solid #dee2e6;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            filter: brightness(1.05);
        }

        .btn:active {
            transform: scale(0.95);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-hijau {
            background: var(--hijau-tua);
            color: white;
        }

        .btn-merah {
            background: #dc3545;
            color: white;
        }

        input[type="text"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            font-family: inherit;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: var(--hijau-muda);
            box-shadow: 0 0 5px rgba(164, 198, 57, 0.5);
        }
    </style>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <h2 id="logo-text">MINIMARKET<br>MAJU BERSAMA</h2>

        <a href="index.php?page=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>">
            <span class="ikon"><i class='bx bx-home-alt'></i></span> <span class="teks-menu">Dashboard</span>
        </a>
        <a href="index.php?page=produk" class="<?= $page == 'produk' ? 'active' : '' ?>">
            <span class="ikon"><i class='bx bx-box'></i></span> <span class="teks-menu">Produk & Stok</span>
        </a>
        <a href="index.php?page=transaksi" class="<?= $page == 'transaksi' ? 'active' : '' ?>">
            <span class="ikon"><i class='bx bx-cart-add'></i></span> <span class="teks-menu">Transaksi Baru</span>
        </a>
        <a href="index.php?page=riwayat" class="<?= $page == 'riwayat' ? 'active' : '' ?>">
            <span class="ikon"><i class='bx bx-receipt'></i></span> <span class="teks-menu">Riwayat Transaksi</span>
        </a>

        <?php if ($role == 'admin') : ?>
            <a href="index.php?page=penjualan" class="<?= $page == 'penjualan' ? 'active' : '' ?>">
                <span class="ikon"><i class='bx bx-wallet'></i></span> <span class="teks-menu">Laporan Keuangan</span>
            </a>
            <a href="index.php?page=akun" class="<?= $page == 'akun' ? 'active' : '' ?>">
                <span class="ikon"><i class='bx bx-group'></i></span> <span class="teks-menu">Kelola Karyawan</span>
            </a>
        <?php endif; ?>

        <a href="logout.php" style="margin-top: 50px; color: #ffc107;">
            <span class="ikon"><i class='bx bx-log-out-circle'></i></span> <span class="teks-menu">Logout</span>
        </a>
    </div>

    <div class="main-content" id="main-content">
        <div class="top-bar">
            <div style="display: flex; align-items: center;">
                <button class="btn-toggle" id="toggleBtn"><i class='bx bx-menu'></i></button>
                <span>Halo, <strong><?= $nama ?></strong></span>
            </div>
            <span>Akses: <strong style="color: var(--hijau-tua);"><?= strtoupper($role) ?></strong></span>
        </div>

        <?php if ($page == 'dashboard') : ?>
            <div class="section-card">
                <h3 style="border-bottom: 2px solid var(--hijau-muda); padding-bottom: 10px; margin-bottom: 20px;">
                    <i class='bx bx-home-alt'></i> Dashboard Pusat Maju Bersama
                </h3>

                <?php
                $q_trx = mysqli_query($conn, "SELECT COUNT(id_transaksi) as jml, SUM(total_harga) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()");
                $d_trx = mysqli_fetch_assoc($q_trx);
                $jml_trx = $d_trx['jml'] ?? 0;
                $total_rp = $d_trx['total'] ?? 0;

                $q_prod = mysqli_query($conn, "SELECT COUNT(id_produk) as total_prod FROM produk");
                $jml_prod = mysqli_fetch_assoc($q_prod)['total_prod'] ?? 0;
                ?>
                <div style="display:flex; gap:20px; flex-wrap: wrap; margin-bottom: 30px;">
                    <div class="card-metrik" style="flex:1; min-width: 200px; background:#eef5ee; padding:20px; border-radius:10px; border-left: 5px solid var(--hijau-tua);">
                        <h4 style="margin-top:0; color:#666;"><i class='bx bx-receipt'></i> Transaksi Hari Ini</h4>
                        <p style="font-size: 1.8rem; margin:0; color: var(--hijau-tua); font-weight: bold;"><?= $jml_trx ?> <span style="font-size:1rem; font-weight:normal;">Struk</span></p>
                    </div>

                    <?php if ($role == 'admin') : ?>
                        <div class="card-metrik" style="flex:1; min-width: 200px; background:#eef5ee; padding:20px; border-radius:10px; border-left: 5px solid var(--hijau-muda);">
                            <h4 style="margin-top:0; color:#666;"><i class='bx bx-money'></i> Pendapatan Hari Ini</h4>
                            <p style="font-size: 1.8rem; margin:0; color: var(--hijau-tua); font-weight: bold;">Rp <?= number_format($total_rp, 0, ',', '.') ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="card-metrik" style="flex:1; min-width: 200px; background:#eef5ee; padding:20px; border-radius:10px; border-left: 5px solid #FF8600;">
                        <h4 style="margin-top:0; color:#666;"><i class='bx bx-package'></i> Total Jenis Produk</h4>
                        <p style="font-size: 1.8rem; margin:0; color: var(--hijau-tua); font-weight: bold;"><?= $jml_prod ?> <span style="font-size:1rem; font-weight:normal;">Item</span></p>
                    </div>
                </div>

                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 300px;">
                        <h4 style="color: var(--hijau-tua);"><i class='bx bx-bolt-circle'></i> Akses Cepat</h4>
                        <div style="display: grid; gap: 10px;">
                            <a href="index.php?page=transaksi" class="btn btn-hijau" style="text-align:center; padding:15px; font-size:1.1rem; background: #FF8600;"><i class='bx bx-cart-add'></i> Mulai Transaksi Baru</a>
                            <?php if ($role == 'admin') : ?>
                                <a href="index.php?page=produk" class="btn" style="text-align:center; padding:15px; background:#1A531A; color:white;"><i class='bx bx-box'></i> Kelola Stok Produk</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                    $q_stok = mysqli_query($conn, "SELECT nama_produk, stok FROM produk WHERE stok < 10 ORDER BY stok ASC LIMIT 5");
                    $ada_stok_kritis = mysqli_num_rows($q_stok) > 0;
                    ?>

                    <?php if (!$ada_stok_kritis) : ?>
                        <div class="card-metrik" style="flex: 2; min-width: 300px; background: #eef5ee; padding: 20px; border-radius: 10px; border: 1px solid #A4C639; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                            <h4 style="color: var(--hijau-tua); margin: 0; font-size: 1.5rem;"><i class='bx bx-check-circle'></i> Stok Semua Barang Aman</h4>
                            <p style="color: #666; margin-top: 10px;">Tidak ada produk yang perlu segera di-restock saat ini.</p>
                        </div>
                    <?php else : ?>
                        <div class="card-metrik" style="flex: 2; min-width: 300px; background: #fff5f5; padding: 20px; border-radius: 10px; border: 1px solid #ffcccc;">
                            <h4 style="color: #d9534f; margin-top: 0;"><i class='bx bx-error-circle'></i> Peringatan Stok Menipis (Sisa < 10)</h4>
                                    <table style="margin-top: 0; width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th style="background: #f9d6d5; color: #a94442; padding: 8px; text-align: left;">Nama Produk</th>
                                                <th style="background: #f9d6d5; color: #a94442; padding: 8px; text-align: center;">Sisa Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($s = mysqli_fetch_assoc($q_stok)) {
                                                echo "<tr>
                                            <td style='padding: 8px; border-bottom: 1px solid #ffcccc;'>{$s['nama_produk']}</td>
                                            <td style='padding: 8px; border-bottom: 1px solid #ffcccc; text-align:center;'>
                                                <strong style='color:#d9534f; background:#fff; padding:2px 8px; border-radius:10px; border:1px solid #d9534f;'>{$s['stok']}</strong>
                                            </td>
                                        </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($page == 'produk') : ?>
            <div class="section-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin:0; border:none; padding:0;"><i class='bx bx-box'></i> Daftar Produk & Stok</h3>
                    <?php if ($role == 'admin') : ?>
                        <a href="index.php?page=tambah_produk" class="btn btn-hijau" style="background: #FF8600;"><i class='bx bx-plus-circle'></i> Tambah Produk Baru</a>
                    <?php endif; ?>
                </div>

                <form method="GET" action="index.php" style="margin-bottom: 20px;">
                    <input type="hidden" name="page" value="produk">
                    <input type="text" name="keyword" placeholder="Cari nama barang..." value="<?= isset($_GET['keyword']) ? $_GET['keyword'] : '' ?>" style="width: 250px;">
                    <button type="submit" class="btn btn-hijau"><i class='bx bx-search'></i> Cari</button>
                    <a href="index.php?page=produk" class="btn" style="background:#666; color:white;"><i class='bx bx-reset'></i> Reset</a>
                </form>

                <table>
                    <tr>
                        <th>Kategori</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <?php if ($role == 'admin') echo "<th>Aksi</th>"; ?>
                    </tr>
                    <?php
                    $kw = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
                    $sql_p = "SELECT p.*, k.nama_kategori FROM produk p JOIN kategori k ON p.id_kategori = k.id_kategori";
                    if ($kw != '') $sql_p .= " WHERE p.nama_produk LIKE '%$kw%' OR k.nama_kategori LIKE '%$kw%'";
                    $res_p = mysqli_query($conn, $sql_p);

                    if (mysqli_num_rows($res_p) > 0) {
                        while ($p = mysqli_fetch_assoc($res_p)) : ?>
                            <tr>
                                <td><?= $p['nama_kategori'] ?></td>
                                <td><?= $p['nama_produk'] ?></td>
                                <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                                <td><strong><?= $p['stok'] ?></strong></td>

                                <?php if ($role == 'admin') : ?>
                                    <td>
                                        <a href="index.php?page=edit_produk&id=<?= $p['id_produk'] ?>" class="btn" style="background: #17a2b8; color: white; padding: 5px 10px; font-size: 12px;"><i class='bx bx-edit'></i> Edit</a>
                                        <a href="proses_produk.php?aksi=hapus&id=<?= $p['id_produk'] ?>" class="btn btn-merah" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus produk ini?')"><i class='bx bx-trash'></i> Hapus</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                    <?php endwhile;
                    } else {
                        $cols = ($role == 'admin') ? 5 : 4;
                        echo "<tr><td colspan='$cols' style='text-align:center;'>Produk tidak ditemukan.</td></tr>";
                    }
                    ?>
                </table>
            </div>

        <?php elseif ($page == 'tambah_produk' && $role == 'admin') : ?>
            <div class="section-card">
                <h3><i class='bx bx-plus-circle'></i> Tambah Produk Baru</h3>
                <form method="POST" action="proses_produk.php?aksi=tambah">
                    <label>Kategori:</label><br>
                    <select name="id_kategori" required style="width: 100%; max-width: 400px;">
                        <?php
                        $kat = mysqli_query($conn, "SELECT * FROM kategori");
                        while ($k = mysqli_fetch_assoc($kat)) echo "<option value='{$k['id_kategori']}'>{$k['nama_kategori']}</option>";
                        ?>
                    </select><br>
                    <label>Nama Produk:</label><br>
                    <input type="text" name="nama_produk" required style="width: 100%; max-width: 400px;"><br>
                    <label>Harga Jual (Rp):</label><br>
                    <input type="number" name="harga" required style="width: 100%; max-width: 400px;"><br>
                    <label>Stok:</label><br>
                    <input type="number" name="stok" required style="width: 100%; max-width: 400px; margin-bottom: 20px;"><br>

                    <button type="submit" class="btn btn-hijau"><i class='bx bx-save'></i> Simpan</button>
                    <a href="index.php?page=produk" class="btn" style="background:#666; color:white; text-decoration:none;">Batal</a>
                </form>
            </div>

        <?php elseif ($page == 'edit_produk' && $role == 'admin') :
            $id_edit = $_GET['id'];
            $q_edit = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id_edit'");
            $d_edit = mysqli_fetch_assoc($q_edit);
        ?>
            <div class="section-card">
                <h3><i class='bx bx-edit'></i> Edit Data Produk</h3>
                <form method="POST" action="proses_produk.php?aksi=edit">
                    <input type="hidden" name="id_produk" value="<?= $d_edit['id_produk'] ?>">
                    <label>Kategori:</label><br>
                    <select name="id_kategori" required style="width: 100%; max-width: 400px;">
                        <?php
                        $kat = mysqli_query($conn, "SELECT * FROM kategori");
                        while ($k = mysqli_fetch_assoc($kat)) {
                            $sel = ($k['id_kategori'] == $d_edit['id_kategori']) ? "selected" : "";
                            echo "<option value='{$k['id_kategori']}' $sel>{$k['nama_kategori']}</option>";
                        }
                        ?>
                    </select><br>
                    <label>Nama Produk:</label><br>
                    <input type="text" name="nama_produk" value="<?= $d_edit['nama_produk'] ?>" required style="width: 100%; max-width: 400px;"><br>
                    <label>Harga Jual (Rp):</label><br>
                    <input type="number" name="harga" value="<?= $d_edit['harga'] ?>" required style="width: 100%; max-width: 400px;"><br>
                    <label>Stok:</label><br>
                    <input type="number" name="stok" value="<?= $d_edit['stok'] ?>" required style="width: 100%; max-width: 400px; margin-bottom: 20px;"><br>

                    <button type="submit" class="btn btn-hijau"><i class='bx bx-check'></i> Update Data</button>
                    <a href="index.php?page=produk" class="btn" style="background:#666; color:white; text-decoration:none;">Batal</a>
                </form>
            </div>

        <?php elseif ($page == 'transaksi') : ?>
            <div class="section-card">
                <h3><i class='bx bx-cart-add'></i> Transaksi Penjualan Baru</h3>

                <form method="POST" action="index.php?page=transaksi" style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 2;">
                            <label>Pilih Produk (Ketik untuk mencari):</label><br>
                            <input type="text" name="produk_input" list="list_produk" placeholder="Ketik nama produk..." style="width: 100%;" required autocomplete="off">
                            <datalist id="list_produk">
                                <?php
                                $all_p = mysqli_query($conn, "SELECT nama_produk FROM produk WHERE stok > 0");
                                while ($ap = mysqli_fetch_assoc($all_p)) echo "<option value='{$ap['nama_produk']}'>";
                                ?>
                            </datalist>
                        </div>
                        <div style="flex: 1;">
                            <label>Jumlah Beli:</label><br>
                            <input type="number" name="jumlah" value="1" min="1" required style="width: 100%;">
                        </div>
                    </div>
                    <button type="submit" name="tambah_keranjang" class="btn btn-hijau" style="margin-top: 15px;"><i class='bx bx-plus'></i> Tambah ke Keranjang</button>
                </form>

                <h4 style="color: var(--hijau-tua); border-bottom: 2px solid var(--hijau-muda); padding-bottom: 10px;"><i class='bx bx-shopping-bag'></i> Daftar Belanjaan</h4>
                <table>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php
                    $total_belanja = 0;
                    if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0) {
                        foreach ($_SESSION['keranjang'] as $item) {
                            $total_belanja += $item['subtotal'];
                            echo "<tr>
                                <td>{$item['nama_produk']}</td>
                                <td>Rp " . number_format($item['harga'], 0, ',', '.') . "</td>
                                <td>{$item['jumlah']}</td>
                                <td>Rp " . number_format($item['subtotal'], 0, ',', '.') . "</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center;'>Keranjang masih kosong. Silakan tambah produk.</td></tr>";
                    }
                    ?>
                    <tr style="background: #eef5ee;">
                        <td colspan="3" style="text-align:right; font-size: 1.1rem;"><strong>Total Bayar:</strong></td>
                        <td style="font-size: 1.1rem; color: var(--hijau-tua);"><strong>Rp <?= number_format($total_belanja, 0, ',', '.') ?></strong></td>
                    </tr>
                </table>

                <?php if ($total_belanja > 0) : ?>
                    <div style="margin-top: 20px; display: flex; gap: 15px;">
                        <form method="POST" action="proses_transaksi.php" style="flex: 1;">
                            <button type="submit" name="selesai_transaksi" class="btn btn-hijau" style="width: 100%; font-size: 1.1rem; padding: 12px; background: #FF8600;"><i class='bx bx-check-double'></i> Selesaikan Transaksi & Bayar</button>
                        </form>
                        <a href="index.php?page=transaksi&kosongkan=1" class="btn btn-merah" style="padding: 12px; text-decoration: none;"><i class='bx bx-trash'></i> Batalkan</a>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($page == 'riwayat') : ?>
            <div class="section-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                    <h3 style="margin: 0; border: none; padding: 0;"><i class='bx bx-receipt'></i> Riwayat Transaksi</h3>

                    <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: center;">
                        <input type="hidden" name="page" value="riwayat">

                        <input type="date" name="tgl_spesifik" value="<?= isset($_GET['tgl_spesifik']) ? $_GET['tgl_spesifik'] : '' ?>" style="margin-bottom: 0; cursor: pointer;" required>

                        <button type="submit" class="btn btn-hijau" style="padding: 10px 15px; margin-bottom: 0; display: flex; align-items: center;"><i class='bx bx-search'></i></button>

                        <select name="filter" onchange="this.form.submit()" style="margin-bottom: 0; cursor: pointer;">
                            <?php
                            $flt = isset($_GET['filter']) ? $_GET['filter'] : 'harian';
                            if (!empty($_GET['tgl_spesifik'])) {
                                $flt = 'custom';
                            }
                            ?>
                            <option value="harian" <?= $flt == 'harian' ? 'selected' : '' ?>>Harian (Hari Ini)</option>
                            <option value="bulanan" <?= $flt == 'bulanan' ? 'selected' : '' ?>>Bulanan (Bulan Ini)</option>
                            <option value="tahunan" <?= $flt == 'tahunan' ? 'selected' : '' ?>>Tahunan (Tahun Ini)</option>
                            <option value="semua" <?= $flt == 'semua' ? 'selected' : '' ?>>Semua Waktu</option>
                            <?php if ($flt == 'custom') echo "<option value='custom' selected hidden>Pencarian Kalender</option>"; ?>
                        </select>

                        <a href="index.php?page=riwayat" class="btn" style="background:#ccc; color:black; padding: 10px 15px; text-decoration:none; margin-bottom: 0; display: flex; align-items: center;"><i class='bx bx-reset'></i></a>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Kasir</th>
                            <th>Total Harga</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $where = "WHERE DATE(t.tanggal_transaksi) = CURDATE()";
                        if (!empty($_GET['tgl_spesifik'])) {
                            $tgl = mysqli_real_escape_string($conn, $_GET['tgl_spesifik']);
                            $where = "WHERE DATE(t.tanggal_transaksi) = '$tgl'";
                        } else {
                            if ($flt == 'bulanan') {
                                $where = "WHERE MONTH(t.tanggal_transaksi) = MONTH(CURDATE()) AND YEAR(t.tanggal_transaksi) = YEAR(CURDATE())";
                            } elseif ($flt == 'tahunan') {
                                $where = "WHERE YEAR(t.tanggal_transaksi) = YEAR(CURDATE())";
                            } elseif ($flt == 'semua') {
                                $where = "";
                            }
                        }

                        $sql_r = "SELECT t.*, k.nama_user FROM transaksi t 
                            JOIN karyawan k ON t.id_karyawan = k.id_karyawan 
                            $where 
                            ORDER BY t.tanggal_transaksi DESC";

                        $res_r = mysqli_query($conn, $sql_r);

                        if ($res_r && mysqli_num_rows($res_r) > 0) :
                            while ($r = mysqli_fetch_assoc($res_r)) : ?>
                                <tr>
                                    <td><?= $r['tanggal_transaksi'] ?></td>
                                    <td><?= $r['nama_user'] ?></td>
                                    <td>Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                                    <td><a href="detail.php?id=<?= $r['id_transaksi'] ?>" style="color:var(--hijau-tua); font-weight:bold; text-decoration:none;"><i class='bx bx-show'></i> Lihat</a></td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan='4' style='text-align:center; color:#888;'>Tidak ada transaksi pada periode ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($page == 'akun' && $role == 'admin') : ?>
            <div class="section-card">
                <h3><i class='bx bx-group'></i> Kelola Karyawan</h3>
                <form method="POST" action="tambah_kasir.php" style="margin-bottom: 20px;">
                    <input type="text" name="nama_user" placeholder="Nama Lengkap" required>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="submit" class="btn btn-hijau"><i class='bx bx-user-plus'></i> Tambah Kasir</button>
                </form>
                <table>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                    <?php
                    $karyawan = mysqli_query($conn, "SELECT * FROM karyawan WHERE role='kasir'");
                    while ($k = mysqli_fetch_assoc($karyawan)) : ?>
                        <tr>
                            <td><?= $k['nama_user'] ?></td>
                            <td><?= $k['username'] ?></td>
                            <td><a href="index.php?page=akun&hapus=<?= $k['id_karyawan'] ?>" class="btn btn-merah" onclick="return confirm('Yakin ingin menghapus kasir ini?')"><i class='bx bx-trash'></i> Hapus</a></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>

        <?php elseif ($page == 'penjualan' && $role == 'admin') : ?>
            <div class="section-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                    <h3 style="margin: 0; border: none; padding: 0;"><i class='bx bx-wallet'></i> Laporan Keuangan</h3>

                    <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: center;">
                        <input type="hidden" name="page" value="penjualan">

                        <select name="bulan" onchange="this.form.submit()">
                            <?php
                            $bulan_pilih = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
                            $nama_bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

                            for ($i = 1; $i <= 12; $i++) {
                                $val = str_pad($i, 2, "0", STR_PAD_LEFT);
                                $sel = ($val == $bulan_pilih) ? "selected" : "";
                                echo "<option value='$val' $sel>{$nama_bulan[$i]}</option>";
                            }
                            ?>
                        </select>

                        <select name="tahun" onchange="this.form.submit()">
                            <?php
                            $tahun_pilih = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
                            $tahun_mulai = 2026;
                            $tahun_sekarang = date('Y');

                            for ($t = $tahun_sekarang; $t >= $tahun_mulai; $t--) {
                                $sel = ($t == $tahun_pilih) ? "selected" : "";
                                echo "<option value='$t' $sel>Tahun $t</option>";
                            }
                            ?>
                        </select>

                        <a href="index.php?page=penjualan" class="btn" style="background:#ccc; color:black; padding: 7px 10px; font-size:12px; text-decoration:none;"><i class='bx bx-reset'></i></a>
                    </form>
                </div>

                <?php
                $q_hari = mysqli_query($conn, "SELECT SUM(total_harga) AS total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()");
                $hari = mysqli_fetch_assoc($q_hari)['total'] ?? 0;

                $q_bulan = mysqli_query($conn, "SELECT SUM(total_harga) AS total FROM transaksi WHERE MONTH(tanggal_transaksi) = '$bulan_pilih' AND YEAR(tanggal_transaksi) = '$tahun_pilih'");
                $bulan = mysqli_fetch_assoc($q_bulan)['total'] ?? 0;

                $q_tahun = mysqli_query($conn, "SELECT SUM(total_harga) AS total FROM transaksi WHERE YEAR(tanggal_transaksi) = '$tahun_pilih'");
                $tahun = mysqli_fetch_assoc($q_tahun)['total'] ?? 0;

                $q_karyawan = mysqli_query($conn, "
                SELECT k.nama_user, COUNT(t.id_transaksi) as jumlah_transaksi, SUM(t.total_harga) as total_uang 
                FROM transaksi t 
                JOIN karyawan k ON t.id_karyawan = k.id_karyawan 
                WHERE MONTH(t.tanggal_transaksi) = '$bulan_pilih' AND YEAR(t.tanggal_transaksi) = '$tahun_pilih' 
                GROUP BY k.id_karyawan 
                ORDER BY total_uang DESC
            ");
                ?>

                <div style="display:flex; gap:20px; margin-top:20px; flex-wrap: wrap;">
                    <div class="card-metrik" style="flex:1; min-width: 200px; background:#eef5ee; padding:20px; border-radius:10px; border-left: 5px solid var(--hijau-muda);">
                        <h4 style="margin-top:0; color:#666;">Pendapatan Hari Ini</h4>
                        <p style="font-size: 1.5rem; color: var(--hijau-tua); font-weight: bold; margin:0;">Rp <?= number_format($hari, 0, ',', '.') ?></p>
                    </div>
                    <div class="card-metrik" style="flex:1; min-width: 200px; background:#eef5ee; padding:20px; border-radius:10px; border-left: 5px solid var(--hijau-muda);">
                        <h4 style="margin-top:0; color:#666;">Bulan <?= $nama_bulan[(int)$bulan_pilih] ?></h4>
                        <p style="font-size: 1.5rem; color: var(--hijau-tua); font-weight: bold; margin:0;">Rp <?= number_format($bulan, 0, ',', '.') ?></p>
                    </div>
                    <div class="card-metrik" style="flex:1; min-width: 200px; background:#eef5ee; padding:20px; border-radius:10px; border-left: 5px solid var(--hijau-muda);">
                        <h4 style="margin-top:0; color:#666;">Tahun <?= $tahun_pilih ?></h4>
                        <p style="font-size: 1.5rem; color: var(--hijau-tua); font-weight: bold; margin:0;">Rp <?= number_format($tahun, 0, ',', '.') ?></p>
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <h4 style="color: var(--hijau-tua); border-bottom: 2px solid var(--hijau-muda); padding-bottom: 10px;"><i class='bx bx-bar-chart-alt-2'></i> Performa Penjualan Kasir (Bulan <?= $nama_bulan[(int)$bulan_pilih] ?> <?= $tahun_pilih ?>)</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Peringkat</th>
                                <th>Nama Kasir</th>
                                <th>Jumlah Transaksi Dilayani</th>
                                <th>Total Uang Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($q_karyawan) > 0) {
                                $no = 1;
                                while ($kar = mysqli_fetch_assoc($q_karyawan)) {
                                    $medali = $no;
                                    if ($no == 1) $medali = "🥇 1";
                                    elseif ($no == 2) $medali = "🥈 2";
                                    elseif ($no == 3) $medali = "🥉 3";

                                    echo "<tr>
                                        <td><strong>{$medali}</strong></td>
                                        <td>{$kar['nama_user']}</td>
                                        <td>{$kar['jumlah_transaksi']} Transaksi</td>
                                        <td style='color: var(--hijau-tua); font-weight: bold;'>Rp " . number_format($kar['total_uang'], 0, ',', '.') . "</td>
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center; color:#888;'>Belum ada data penjualan kasir pada periode ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <script>
        document.getElementById('toggleBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('main-content').classList.toggle('expanded');
        });
    </script>

</body>

</html>