<?php
session_start();
include 'koneksi.php';

if (isset($_POST['selesai_transaksi']) && isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0) {

    $id_karyawan = $_SESSION['id_karyawan'];

    $total_harga = 0;
    foreach ($_SESSION['keranjang'] as $item) {
        $total_harga += $item['subtotal'];
    }

    $query_trx = "INSERT INTO transaksi (id_karyawan, total_harga) VALUES ('$id_karyawan', '$total_harga')";
    mysqli_query($conn, $query_trx);

    $id_transaksi_baru = mysqli_insert_id($conn);

    foreach ($_SESSION['keranjang'] as $item) {
        $id_produk = $item['id_produk'];
        $jumlah    = $item['jumlah'];
        $subtotal  = $item['subtotal'];

        $query_detail = "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, subtotal) 
                        VALUES ('$id_transaksi_baru', '$id_produk', '$jumlah', '$subtotal')";
        mysqli_query($conn, $query_detail);
    }

    unset($_SESSION['keranjang']);

    header("location:detail.php?id=" . $id_transaksi_baru);
    exit;
} else {
    header("location:index.php?page=transaksi");
    exit;
}
