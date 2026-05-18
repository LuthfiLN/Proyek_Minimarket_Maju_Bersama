CREATE DATABASE IF NOT EXISTS minimarket_db;
USE minimarket_db;

-- 1. Tabel Akun Pengguna (Admin & Kasir)
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('Admin', 'Kasir') NOT NULL
);

-- 2. Tabel Master Produk / Barang
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(20) UNIQUE NOT NULL,
    nama_produk VARCHAR(100) NOT NULL,  
    harga INT NOT NULL,
    stok INT NOT NULL
);

-- 3. Tabel Riwayat Transaksi Utama
CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_faktur VARCHAR(50) UNIQUE NOT NULL,
    tanggal_transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_harga INT NOT NULL,
    bayar INT NOT NULL,
    kembali INT NOT NULL,
    id_user INT,
    FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE SET NULL
);

-- 4. Tabel Detail Item Transaksi (Pecahan Produk Belanjaan)
CREATE TABLE IF NOT EXISTS detail_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT,
    id_produk INT,
    jumlah INT NOT NULL,
    subtotal INT NOT NULL,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE SET NULL
);

-- ================= DATA AWAL UNTUK TESTING =================
-- Password admin: admin123 | Password kasir: kasir123 (Menggunakan MD5)
INSERT INTO user (username, password, nama_lengkap, role) VALUES
('admin', MD5('admin123'), 'Admin Utama Toko', 'Admin'),
('kasir', MD5('kasir123'), 'Siti Kasir', 'Kasir')
ON DUPLICATE KEY UPDATE username=username;

INSERT INTO produk (kode_produk, nama_produk, harga, stok) VALUES
('BRG001', 'Indomie Goreng Rasa Nusantara', 3500, 120),
('BRG002', 'Susu UHT Cokelat 250ml', 7000, 45),
('BRG003', 'Pringles Potato Chips', 22000, 15)
ON DUPLICATE KEY UPDATE kode_produk=kode_produk;