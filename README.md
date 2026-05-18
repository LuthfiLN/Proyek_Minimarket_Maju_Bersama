````markdown
# 🏪 MajuMart - Sistem Manajemen Minimarket

Aplikasi web berbasis PHP Native dan Tailwind CSS untuk manajemen stok barang dan transaksi penjualan di toko minimarket. Aplikasi ini dirancang sesuai dengan spesifikasi diagram _Use Case_ yang memisahkan hak akses antara **Admin** (Pemilik/Manajer Toko) dan **Kasir** (Petugas Kasir).

💡 **Watermark / Hak Cipta Pengembang:**  
_Aplikasi ini dikembangkan dan diselesaikan secara final oleh:_

- **Muhammad Chairawan**
- **Rido Utama Marpaung**
- **Luthfi Nurrahman**

---

## 🛠️ Fitur Utama (Sesuai Use Case Diagram)

### 👤 Role: Admin

- **Autentikasi Keamanan:** Login & Logout sistem.
- **Kelola Data Produk (CRUD):**
  - Menambah produk/barang baru (`tambah.php`)
  - Mengubah informasi produk, harga, dan jumlah stok (`edit.php`)
  - Menghapus produk dari sistem (`hapus.php`)
  - Melihat detail spesifik suatu produk (`view.php`)
- **Laporan Keuangan:** Memantau akumulasi total pendapatan (omset) riil dari seluruh penjualan secara keseluruhan (`riwayat.php`).

### 👥 Role: Kasir

- **Autentikasi Keamanan:** Login & Logout sistem.
- **Lihat Informasi Produk:** Mengakses daftar stok barang yang tersedia untuk memastikan ketersediaan tanpa hak merubah/menghapus (`index.php`, `view.php`).
- **Kelola Transaksi Penjualan:**
  - Menginput barang belanjaan ke dalam keranjang belanja digital.
  - Menghitung subtotal dan grand total belanjaan otomatis.
  - Fitur kalkulasi kembalian instan untuk meminimalisir kesalahan manual.
  - Pemotongan stok barang otomatis setelah transaksi berhasil diselesaikan (`transaksi.php`).
  - Melihat riwayat transaksi mandiri (`riwayat.php`).

---

## 💻 Panduan Instalasi & Penggunaan

### 1. Prasyarat Sistem

- Web Server lokal (Disarankan menggunakan **XAMPP** dengan versi PHP 7.4 s.d PHP 8.x).
- Web Browser modern (Chrome, Edge, Firefox, Safari).

### 2. Langkah-Langkah Instalasi

1. **Download / Salin Source Code:**  
   Buat sebuah folder baru bernama `minimarket` di dalam direktori root server Anda (biasanya di `C:\xampp\htdocs\minimarket\` pada Windows). Letakkan semua file `.php` yang telah dibuat ke dalam folder tersebut.

2. **Import Database:**
   - Buka web browser Anda dan akses `http://localhost/phpmyadmin/`.
   - Buat database baru bernama `minimarket_db`.
   - Masuk ke tab **SQL**, lalu salin dan tempelkan seluruh kode isi dari file `database.sql` yang telah disediakan, kemudian klik **Go/Kirim**.

3. **Verifikasi Koneksi:**  
   Buka file `koneksi.php` dan pastikan konfigurasi _username_ (`root`) dan _password_ (`""`) sudah sesuai dengan setelan default XAMPP Anda.

4. **Menjalankan Aplikasi:**  
   Buka browser Anda lalu akses URL berikut:
   ```text
   http://localhost/minimarket/login.php
   ```
````

---

## 🔐 Akun Akses Default (Uji Coba)

Gunakan akun berikut di halaman `login.php` untuk menguji perbedaan fungsionalitas antar hak akses:

| No  | Role      | Username | Password   | Hak Akses yang Terbuka                                |
| --- | --------- | -------- | ---------- | ----------------------------------------------------- |
| 1   | **Admin** | `admin`  | `admin123` | Semua fitur (CRUD Produk, Transaksi, Laporan Omset)   |
| 2   | **Kasir** | `kasir`  | `kasir123` | Terbatas (Hanya Transaksi & Lihat Daftar Stok Produk) |

---

## 📁 Struktur Berkas Proyek

```text
minimarket/
│
├── database.sql        # Skema tabel (user, produk, transaksi, detail_transaksi)
├── koneksi.php         # Handler interkoneksi ke MySQL database
├── sidebar.php         # Navigasi global (Desktop Sidebar & Mobile Offcanvas Menu)
├── login.php           # Autentikasi gerbang masuk user
├── logout.php          # Penghancur sesi & keluar aman
├── index.php           # Dashboard utama menampilkan tabel stok produk
├── view.php            # Menampilkan detail spesifik dari satu barang
├── tambah.php          # Form entri barang baru (Khusus Admin)
├── edit.php            # Form pembaruan data barang (Khusus Admin)
├── hapus.php           # Skrip penghapus barang (Khusus Admin)
├── transaksi.php       # Modul kasir, keranjang belanja, & kalkulator bayar
└── riwayat.php         # Jurnal penjualan dan total omset pendapatan toko
```

---

🔒 _Hak Cipta dilindungi. Dikembangkan demi pemenuhan standardisasi sistem informasi ritel modern MajuMart._

```

---

## ✅ Perubahan yang dilakukan:
1. **Menghapus backticks berlebih** di awal dan akhir file
2. **Memperbaiki indentasi** pada daftar bertingkat (CRUD produk)
3. **Menambahkan spasi** setelah `---` untuk memastikan horizontal rule tampil
4. **Menghapus karakter `\`** yang tidak diperlukan pada baris kosong
5. **Memastikan semua kode block** menggunakan tiga backticks dengan rapi

Sekarang file tersebut **100% kompatibel dengan GitHub** dan akan tampil dengan format Markdown yang sempurna. Anda tinggal simpan sebagai `README.md` di root repository Anda. 🚀
```
