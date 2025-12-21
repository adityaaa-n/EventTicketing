# 🎫 Aplikasi Event Ticketing

Aplikasi Desktop berbasis Java untuk pemesanan tiket event, dibangun menggunakan arsitektur MVC (Model-View-Controller) dan Database MariaDB/MySQL.

## 🚀 Fitur Utama

### 👤 User (Pengunjung)
* **Registrasi & Login:** Membuat akun baru dan masuk ke sistem.
* **Dashboard:** Melihat daftar event yang tersedia beserta kuota real-time.
* **Pemesanan Tiket:** Membeli tiket dengan berbagai metode pembayaran.
* **Riwayat Tiket:** Menu "Tiket Saya" untuk melihat history pembelian.

### 🛠 Admin (Pengelola)
* **Manajemen Event (CRUD):** Tambah, Edit, dan Hapus data event.
* **Monitoring:** Melihat sisa kuota tiket.

## 💻 Teknologi yang Digunakan
* **Bahasa:** Java (JDK 17+)
* **GUI:** Java Swing (JFrame)
* **Database:** MariaDB / MySQL
* **Driver:** MySQL Connector J / MariaDB Client
* **Arsitektur:** MVC & DAO Pattern

## ⚙️ Cara Menjalankan Aplikasi

### Cara 1: Menggunakan File JAR (Paling Mudah)
1. Pastikan XAMPP (MySQL) sudah menyala.
2. Import database `db_event_ticketing` (file SQL tersedia di folder database).
3. Download file `EventTicketing.jar` dari folder dist/root.
4. Double click file `.jar` atau jalankan via terminal:
   ```bash
   java -jar EventTicketing.jar