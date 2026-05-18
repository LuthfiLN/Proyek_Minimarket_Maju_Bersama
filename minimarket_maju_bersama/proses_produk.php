<?php
session_start();
include 'koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("location:index.php?page=produk");
    exit;
}

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

if ($aksi == 'tambah') {
    $id_kategori = $_POST['id_kategori'];
    $nama_produk = $_POST['nama_produk'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    
    $query = "INSERT INTO produk (id_kategori, nama_produk, harga, stok) 
            VALUES ('$id_kategori', '$nama_produk', '$harga', '$stok')";
    mysqli_query($conn, $query);
    header("location:index.php?page=produk");
}

elseif ($aksi == 'edit') {
    $id_produk   = $_POST['id_produk'];
    $id_kategori = $_POST['id_kategori'];
    $nama_produk = $_POST['nama_produk'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    
    $query = "UPDATE produk SET 
                id_kategori = '$id_kategori', 
                nama_produk = '$nama_produk', 
                harga       = '$harga', 
                stok        = '$stok' 
                WHERE id_produk = '$id_produk'";
    mysqli_query($conn, $query);
    header("location:index.php?page=produk");
}

elseif ($aksi == 'hapus') {
    $id_produk = $_GET['id'];
    
    mysqli_query($conn, "DELETE FROM produk WHERE id_produk = '$id_produk'");
    header("location:index.php?page=produk");
}

else {
    header("location:index.php?page=produk");
}
?>