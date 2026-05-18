<?php
include 'koneksi.php';
session_start();

if ($_SESSION['role'] == 'admin' && isset($_POST['submit'])) {
    $nama = $_POST['nama_user'];
    $user = $_POST['username'];
    $pass = $_POST['password'];

    mysqli_query($conn, "INSERT INTO karyawan (nama_user, username, password, role) VALUES ('$nama', '$user', '$pass', 'kasir')");
    header("location:index.php");
}
