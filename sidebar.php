<?php
// Pastikan file ini dipanggil di dalam file yang sudah menjalankan session_start()
$role_user = $_SESSION['role'] ?? 'Guest';
$nama_user = $_SESSION['nama'] ?? 'Pengunjung';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="hidden md:flex flex-col w-64 bg-slate-900 text-white p-5 shadow-xl shrink-0">
    <a href="index.php" class="flex items-center gap-3 text-xl font-bold tracking-wider mb-6">
        <i class="fa-solid fa-shop text-blue-500"></i>
        <span>MajuMart</span>
    </a>

    <div class="flex items-center gap-3 p-3 bg-slate-800 rounded-xl mb-4">
        <div class="w-10 h-10 bg-gradient-to-tr from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
            <?= substr($role_user, 0, 1); ?>
        </div>
        <div class="truncate">
            <h6 class="font-bold text-xs text-slate-100 truncate"><?= $nama_user; ?></h6>
            <span class="text-[10px] bg-slate-700 px-2 py-0.5 rounded text-blue-300 font-semibold"><?= $role_user; ?></span>
        </div>
    </div>

    <hr class="border-slate-700 mb-4">

    <nav class="flex-1 space-y-1">
        <a href="index.php" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition <?= $current_page == 'index.php' || $current_page == 'view.php' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fa-solid fa-box text-xs w-4"></i>
            <span>Daftar Produk</span>
        </a>
        <a href="transaksi.php" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition <?= $current_page == 'transaksi.php' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fa-solid fa-cash-register text-xs w-4"></i>
            <span>Transaksi Kasir</span>
        </a>
        <a href="riwayat.php" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition <?= $current_page == 'riwayat.php' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fa-solid fa-clock-rotate-left text-xs w-4"></i>
            <span>Riwayat & Laporan</span>
        </a>
    </nav>

    <hr class="border-slate-700 my-4">
    <a href="logout.php" class="flex items-center gap-3 bg-red-600/10 text-red-400 hover:bg-red-600 hover:text-white px-4 py-2.5 rounded-lg text-sm font-medium transition">
        <i class="fa-solid fa-right-from-bracket text-xs"></i>
        <span>Keluar Sistem</span>
    </a>
</aside>

<div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/50 z-40 hidden transition-opacity duration-300 opacity-0 md:hidden"></div>

<aside id="mobileSidebar" class="fixed top-0 bottom-0 left-0 w-72 bg-slate-900 text-white p-5 z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden flex flex-col shadow-2xl">
    <div class="flex items-center justify-between mb-6">
        <a href="index.php" class="flex items-center gap-3 text-xl font-bold tracking-wider">
            <i class="fa-solid fa-shop text-blue-500"></i>
            <span>MajuMart</span>
        </a>
        <button id="closeSidebar" class="text-slate-400 hover:text-white p-2 text-xl">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="flex items-center gap-3 p-4 bg-slate-800/60 rounded-xl mb-6">
        <div class="w-12 h-12 bg-gradient-to-tr from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-lg font-bold shadow-md">
            <?= substr($role_user, 0, 1); ?>
        </div>
        <div>
            <h6 class="font-bold text-sm text-slate-100"><?= $nama_user; ?></h6>
            <p class="text-xs text-slate-400"><?= $role_user; ?></p>
        </div>
    </div>

    <nav class="flex-1 space-y-2">
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium <?= $current_page == 'index.php' || $current_page == 'view.php' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800'; ?>">
            <i class="fa-solid fa-box text-sm"></i>
            <span>Daftar Produk</span>
        </a>
        <a href="transaksi.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium <?= $current_page == 'transaksi.php' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800'; ?>">
            <i class="fa-solid fa-cash-register text-sm"></i>
            <span>Transaksi Kasir</span>
        </a>
        <a href="riwayat.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium <?= $current_page == 'riwayat.php' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800'; ?>">
            <i class="fa-solid fa-clock-rotate-left text-sm"></i>
            <span>Riwayat & Laporan</span>
        </a>
    </nav>

    <hr class="border-slate-700 my-4">
    <a href="logout.php" class="flex items-center gap-3 bg-red-600 text-white px-4 py-3 rounded-lg text-sm font-semibold text-center justify-center shadow-md">
        <i class="fa-solid fa-right-from-bracket"></i> Keluar
    </a>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const openBtn = document.getElementById('openSidebar');
        const closeBtn = document.getElementById('closeSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const sidebar = document.getElementById('mobileSidebar');

        if (openBtn && closeBtn && backdrop && sidebar) {
            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                backdrop.classList.toggle('hidden');
                setTimeout(() => {
                    backdrop.classList.toggle('opacity-0');
                }, 20);
            }
            openBtn.addEventListener('click', toggleSidebar);
            closeBtn.addEventListener('click', toggleSidebar);
            backdrop.addEventListener('click', toggleSidebar);
        }
    });
</script>