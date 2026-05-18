<?php
include 'koneksi.php';
session_start();

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);

    $query = "SELECT * FROM user WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['nama'] = $row['nama_lengkap'];
        $_SESSION['role'] = $row['role'];

        header("Location: index.php");
        exit;
    } else {
        $error = "Kombinasi Username / Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MajuMart - Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-slate-950 flex items-center justify-center min-h-screen p-4 antialiased">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 bg-gradient-to-tr from-blue-600 to-indigo-700 text-white text-center">
            <h1 class="text-2xl font-black tracking-wide">MajuMart App</h1>
            <p class="text-blue-100 text-xs mt-1">Sistem Informasi Minimarket Bersama</p>
        </div>
        <div class="p-6 md:p-8 space-y-4">
            <?php if (!empty($error)) : ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2.5 rounded-lg text-xs flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i><span><?= $error; ?></span>
                </div>
            <?php endif; ?>
            <form action="" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username</label>
                    <input type="text" name="username" required placeholder="admin / kasir" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                </div>
                <button type="submit" name="login" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg shadow transition active:scale-95 text-sm">
                    Masuk ke Sistem
                </button>
            </form>
        </div>
    </div>
</body>

</html>