<?php
include 'koneksi.php';
session_start();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM karyawan WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['id_karyawan'] = $data['id_karyawan'];
        $_SESSION['nama'] = $data['nama_user'];
        $_SESSION['role'] = $data['role'];
        header("location:index.php");
    } else {
        echo "<script>alert('Username atau Password Salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Maju Bersama</title>
    <style>
        body { font-family: Arial; background-color: #f0f2f1; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 300px; border-top: 5px solid #1A531A; }
        h2 { text-align: center; color: #1A531A; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #1A531A; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #A4C639; color: black; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login Kasir</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Masuk</button>
        </form>
    </div>
</body>
</html>