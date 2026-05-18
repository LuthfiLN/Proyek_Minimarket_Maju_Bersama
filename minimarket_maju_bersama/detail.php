<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || !isset($_GET['id'])) {
    header("location:index.php?page=riwayat");
    exit;
}

$id_transaksi = mysqli_real_escape_string($conn, $_GET['id']);

$sql_trx = "SELECT t.*, k.nama_user FROM transaksi t 
            JOIN karyawan k ON t.id_karyawan = k.id_karyawan 
            WHERE t.id_transaksi = '$id_transaksi'";
$res_trx = mysqli_query($conn, $sql_trx);

if (mysqli_num_rows($res_trx) == 0) {
    echo "<h3 style='text-align:center; font-family:sans-serif;'>Transaksi tidak ditemukan.</h3>";
    exit;
}
$trx = mysqli_fetch_assoc($res_trx);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Transaksis #<?= $id_transaksi ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7f6;
            display: flex;
            justify-content: center;
            padding: 40px;
            margin: 0;
        }

        .struk-card {
            background: white;
            width: 100%;
            max-width: 450px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-top: 6px solid #1A531A;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #ccc;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            color: #1A531A;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }

        .info {
            font-size: 14px;
            color: #444;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            text-align: left;
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            color: #1A531A;
            font-size: 14px;
        }

        td {
            padding: 10px 0;
            border-bottom: 1px solid #f9f9f9;
            font-size: 14px;
            color: #333;
        }

        .td-right {
            text-align: right;
        }

        .total-box {
            border-top: 2px dashed #ccc;
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-teks {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .total-harga {
            font-size: 20px;
            font-weight: bold;
            color: #FF8600;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-kembali {
            background: #666;
            color: white;
        }

        .btn-cetak {
            background: #1A531A;
            color: white;
        }

        .btn-baru {
            background: #FF8600;
            color: white;
        }

        @media print {
            .btn-group {
                display: none;
            }

            body {
                background: white;
                padding: 0;
            }

            .struk-card {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body>

    <div class="struk-card">
        <div class="header">
            <h2>MAJU BERSAMA</h2>
            <p>Struk Bukti Pembelian Digital</p>
        </div>

        <div class="info">
            <div><strong>No. Transaksi:</strong> #TRX-<?= $trx['id_transaksi'] ?></div>
            <div><strong>Tanggal & Waktu:</strong> <?= $trx['tanggal_transaksi'] ?></div>
            <div><strong>Kasir Melayani:</strong> <?= $trx['nama_user'] ?></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th class="td-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_detail = "SELECT d.*, p.nama_produk FROM detail_transaksi d 
                            JOIN produk p ON d.id_produk = p.id_produk 
                            WHERE d.id_transaksi = '$id_transaksi'";
                $res_detail = mysqli_query($conn, $sql_detail);

                while ($d = mysqli_fetch_assoc($res_detail)) : ?>
                    <tr>
                        <td><?= $d['nama_produk'] ?></td>
                        <td><?= $d['jumlah'] ?>x</td>
                        <td class="td-right">Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="total-box">
            <span class="total-teks">Total Belanja</span>
            <span class="total-harga">Rp <?= number_format($trx['total_harga'], 0, ',', '.') ?></span>
        </div>

        <div class="btn-group">
            <a href="index.php?page=riwayat" class="btn btn-kembali">Riwayat</a>
            <a href="index.php?page=transaksi" class="btn btn-baru">Transaksi Baru</a>
            <button onclick="window.print()" class="btn btn-cetak">Cetak Struk</button>
        </div>
    </div>

</body>

</html>