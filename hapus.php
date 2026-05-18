<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}
include 'koneksi.php';

$id = $_GET['id'] ?? 0;
mysqli_query($koneksi, "DELETE FROM produk WHERE id = $id");
header("Location: index.php");
exit;
